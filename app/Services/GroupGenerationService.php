<?php

namespace App\Services;

use App\Models\Course;
use App\Models\StudentCourseGroup;
use App\Models\StudentCoursePlacement;
use App\Models\StudentCourseSelection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class GroupGenerationService
{
    public function __construct(
        protected PlacementService $placementService
    ) {
    }

    /**
     * Öğrenci tercihlerinden otomatik grupları oluşturur.
     *
     * Dağıtım sırası:
     *
     * 1. tercih
     * 2. tercih
     * 3. tercih
     * Aynı kategori içindeki mevcut uygun gruplar
     *
     * ÖNEMLİ:
     * Yeni bir grup yalnızca minimum öğrenci sayısına
     * (10) ulaşabiliyorsa oluşturulur.
     *
     * Hiçbir öğrencinin altında tek başına / 2-9 kişilik
     * yeni grubu oluşturulmaz.
     */
    public function generate(
        int $academicYearId,
        ?int $maximumStudentsPerGroup = null
    ): array {
        return DB::transaction(function () use (
            $academicYearId,
            $maximumStudentsPerGroup
        ) {
            $this->clearDraftData($academicYearId);

            /*
             * Öğrencilerin gönderilmiş tüm tercihleri.
             */
            $selections = StudentCourseSelection::query()
                ->with([
                    'student',
                    'course.category',
                    'course.gradeOptions',
                    'course.moduleGroups',
                    'moduleGroup',
                    'gradeOption',
                ])
                ->where(
                    'academic_year_id',
                    $academicYearId
                )
                ->where(
                    'status',
                    2
                )
                ->orderBy('student_id')
                ->orderBy('preference_order')
                ->get();

            /*
             * Her öğrencinin 1. tercihleri.
             */
            $primarySelections = $selections
                ->where(
                    'preference_order',
                    1
                );

            /*
             * 1. tercihleri değerlendir.
             *
             * 10+ öğrencili havuzlar grup olur.
             * <10 olanlar pending kalır.
             */
            $primaryResult =
                $this->processPreferenceLevel(
                    $primarySelections,
                    $academicYearId,
                    $maximumStudentsPerGroup,
                    1,
                    collect()
                );

            $pending =
                $primaryResult['pending'];

            /*
             * 2. tercih.
             */
            $secondSelections = $selections
                ->where(
                    'preference_order',
                    2
                );

            $secondResult =
                $this->processPreferenceLevel(
                    $secondSelections,
                    $academicYearId,
                    $maximumStudentsPerGroup,
                    2,
                    $pending
                );

            $pending =
                $secondResult['pending'];

            /*
             * 3. tercih.
             */
            $thirdSelections = $selections
                ->where(
                    'preference_order',
                    3
                );

            $thirdResult =
                $this->processPreferenceLevel(
                    $thirdSelections,
                    $academicYearId,
                    $maximumStudentsPerGroup,
                    3,
                    $pending
                );

            $pending =
                $thirdResult['pending'];

            /*
             * Son aşama:
             *
             * Öğrencinin 1-2-3 tercihlerinden hiçbirine
             * grup oluşturulamadıysa, aynı kategori içinde
             * ZATEN VAR OLAN uygun gruplara dağıt.
             *
             * Yeni grup oluşturulmaz.
             */
            $fallbackResult =
                $this->placeIntoExistingCategoryGroups(
                    $pending,
                    $selections
                );

            /*
             * Son grup notlarını güncelle.
             */
            $this->refreshGroupNotes(
                $academicYearId
            );

            $groups =
                StudentCourseGroup::query()
                    ->with([
                        'course.category',
                        'moduleGroup',
                        'module',
                        'placements.student',
                    ])
                    ->where(
                        'academic_year_id',
                        $academicYearId
                    )
                    ->orderBy('course_id')
                    ->orderBy('course_module_group_id')
                    ->orderBy('course_module_id')
                    ->orderBy('weekly_hours')
                    ->orderBy('group_number')
                    ->get();

            $placements =
                StudentCoursePlacement::query()
                    ->with([
                        'student',
                        'selection',
                        'course',
                        'module',
                        'group',
                    ])
                    ->where(
                        'academic_year_id',
                        $academicYearId
                    )
                    ->orderBy('student_id')
                    ->orderBy('id')
                    ->get();

            return [
                'academic_year_id' =>
                    $academicYearId,

                'selection_count' =>
                    $primarySelections->count(),

                'group_count' =>
                    $groups->count(),

                'placement_count' =>
                    $placements->count(),

                'preference2_count' =>
                    $secondResult['placed_count'],

                'preference3_count' =>
                    $thirdResult['placed_count'],

                'fallback_count' =>
                    $fallbackResult['moved_count'],

                'unresolved_count' =>
                    $fallbackResult['unresolved']->count(),

                'groups' =>
                    $groups,

                'placements' =>
                    $placements,

                'unresolved' =>
                    $fallbackResult['unresolved'],
            ];
        });
    }

    /**
     * Belirli bir tercih seviyesini işler.
     *
     * Örnek:
     *
     * 1. tercih:
     * 27 öğrenci
     * → 14 + 13
     *
     * 2. tercih:
     * 8 öğrenci
     * → grup açılmaz
     * → 3. tercihe geçer.
     *
     * Önce mevcut uygun gruplara öğrenci eklenir.
     * Sonra kalan öğrenciler 10+ havuzlarsa yeni grup açılır.
     */
    protected function processPreferenceLevel(
    Collection $levelSelections,
    int $academicYearId,
    ?int $maximumStudentsPerGroup,
    int $preferenceOrder,
    Collection $pending
): array {
    $requests = collect();

    /*
     * 1. tercih:
     * Her öğrenci doğrudan değerlendirilir.
     */
    if ($preferenceOrder === 1) {

        foreach ($levelSelections as $selection) {
            $requests->push([
                'selection' =>
                    $selection,

                'student_id' =>
                    (int) $selection->student_id,

                'category_id' =>
                    (int) $selection
                        ->course
                        ->course_category_id,

                'weekly_hours' =>
                    (int) $selection->weekly_hours,
            ]);
        }

    } else {

        /*
         * 2. ve 3. tercih:
         * yalnızca önceki aşamada yerleşemeyen
         * öğrencileri değerlendir.
         */
        foreach ($pending as $item) {

            $selection = $levelSelections->first(
                fn ($s) =>
                    (int) $s->student_id
                        ===
                    (int) $item['student_id']
                    &&
                    (int) $s->course->course_category_id
                        ===
                    (int) $item['category_id']
            );

            /*
             * Bu öğrencinin bu seviyede tercihi yoksa
             * PENDING olarak aynen ileri taşı.
             *
             * Örneğin:
             * 2. tercih var, 3. tercih yok.
             */
            if (! $selection) {
                $requests->push([
                    'selection' =>
                        null,

                    'student_id' =>
                        $item['student_id'],

                    'category_id' =>
                        $item['category_id'],

                    'weekly_hours' =>
                        $item['weekly_hours'],

                    'carry_forward' =>
                        true,

                    'failed_preference_order' =>
                        $item[
                            'failed_preference_order'
                        ] ?? $preferenceOrder - 1,
                ]);

                continue;
            }

            $requests->push([
                'selection' =>
                    $selection,

                'student_id' =>
                    $item['student_id'],

                'category_id' =>
                    $item['category_id'],

                /*
                 * Öğrencinin 1. tercih saatini koruyoruz.
                 */
                'weekly_hours' =>
                    (int) $item['weekly_hours'],
            ]);
        }
    }

    $remaining = collect();
    $placedCount = 0;

    /*
     * Önce mevcut uygun gruplara yerleştir.
     */
    foreach ($requests as $request) {

        /*
         * Bu tercih seviyesinde öğrenciye ait seçim yok.
         * Aynen sonraki aşamaya taşı.
         */
        if (
            ! empty($request['carry_forward'])
            ||
            ! $request['selection']
        ) {
            $remaining->push([
                'student_id' =>
                    $request['student_id'],

                'category_id' =>
                    $request['category_id'],

                'weekly_hours' =>
                    $request['weekly_hours'],

                'failed_preference_order' =>
                    $request[
                        'failed_preference_order'
                    ] ?? $preferenceOrder - 1,

                'selection' =>
                    null,

                'resolved' =>
                    null,

                'carry_forward' =>
                    true,
            ]);

            continue;
        }

        $selection =
            $request['selection'];

        $weeklyHours =
            (int) $request['weekly_hours'];

        $resolved =
            $this->resolveSelection(
                $selection,
                $weeklyHours
            );

        /*
         * Bu tercih için uygun saat/program/modül
         * çözülemiyorsa sonraki tercihe bırak.
         */
        if (! $resolved) {
            $remaining->push([
                'student_id' =>
                    $request['student_id'],

                'category_id' =>
                    $request['category_id'],

                'weekly_hours' =>
                    $weeklyHours,

                'failed_preference_order' =>
                    $preferenceOrder,

                'selection' =>
                    $selection,

                'resolved' =>
                    null,

                'carry_forward' =>
                    false,
            ]);

            continue;
        }

        /*
         * Daha önce açılmış uygun grup var mı?
         */
        $group =
            $this->findExistingMatchingGroup(
                $academicYearId,
                $resolved,
                $maximumStudentsPerGroup
            );

        if ($group) {

            $this->createOrUpdatePlacement(
                $selection,
                $group,
                $resolved,
                $preferenceOrder
            );

            $placedCount++;

            continue;
        }

        /*
         * Henüz grup yoksa 10+ kişilik havuz
         * oluşturulması için beklet.
         */
        $remaining->push([
            'student_id' =>
                $request['student_id'],

            'category_id' =>
                $request['category_id'],

            'weekly_hours' =>
                $weeklyHours,

            'failed_preference_order' =>
                $preferenceOrder,

            'selection' =>
                $selection,

            'resolved' =>
                $resolved,

            'carry_forward' =>
                false,
        ]);
    }

    /*
     * Yeni grup oluşturulabilecek talepleri
     * aynı ders/program/modül/saat altında grupla.
     */
    $buckets = [];

    foreach ($remaining as $request) {

        /*
         * Carry-forward kayıtlarının o tercih için
         * bir grup oluşturma talebi yoktur.
         */
        if (
            ! empty($request['carry_forward'])
            ||
            ! $request['selection']
        ) {
            continue;
        }

        $resolved =
            $request['resolved']
            ??
            $this->resolveSelection(
                $request['selection'],
                $request['weekly_hours']
            );

        if (! $resolved) {
            continue;
        }

        $key =
            $this->bucketKey(
                $resolved['course_id'],
                $resolved['module_group_id'],
                $resolved['module_id'],
                $resolved['weekly_hours']
            );

        $buckets[$key] ??= [
            'resolved' =>
                $resolved,

            'rows' =>
                collect(),
        ];

        $buckets[$key]['rows']->push(
            $request
        );
    }

    $stillPending = collect();

    /*
     * Sadece 10+ öğrencilik havuzlarda grup oluştur.
     */
    foreach ($buckets as $bucket) {

        $rows =
            $bucket['rows'];

        $count =
            $rows->count();

        if ($count < 10) {

            foreach ($rows as $row) {
                $stillPending->push([
                    'student_id' =>
                        $row['student_id'],

                    'category_id' =>
                        $row['category_id'],

                    'weekly_hours' =>
                        $row['weekly_hours'],

                    'failed_preference_order' =>
                        $preferenceOrder,

                    'selection' =>
                        $row['selection'],

                    'resolved' =>
                        $row['resolved'] ?? null,

                    'carry_forward' =>
                        false,
                ]);
            }

            continue;
        }

        $groupCount =
            $this->calculateGroupCount(
                $count,
                $maximumStudentsPerGroup
            );

        $chunks =
            $this->balancedChunks(
                $rows,
                $groupCount
            );

        foreach (
            $chunks
            as $index => $chunk
        ) {
            $group =
                $this->createGroup(
                    $academicYearId,
                    $bucket['resolved'],
                    $index + 1,
                    $chunk->count(),
                    $maximumStudentsPerGroup,
                    $preferenceOrder
                );

            foreach ($chunk as $row) {
                $this->createOrUpdatePlacement(
                    $row['selection'],
                    $group,
                    $bucket['resolved'],
                    $preferenceOrder
                );

                $placedCount++;
            }
        }
    }

    /*
     * 10 kişinin altında kalan talepler ve
     * bu seviyede tercihi olmayan öğrenciler
     * bir sonraki seviyeye taşınır.
     */
    foreach ($remaining as $row) {

        if (
            ! empty($row['carry_forward'])
            ||
            ! $row['selection']
        ) {
            $stillPending->push([
                'student_id' =>
                    $row['student_id'],

                'category_id' =>
                    $row['category_id'],

                'weekly_hours' =>
                    $row['weekly_hours'],

                'failed_preference_order' =>
                    $row[
                        'failed_preference_order'
                    ],

                'selection' =>
                    null,

                'resolved' =>
                    null,

                'carry_forward' =>
                    true,
            ]);

            continue;
        }

        /*
         * 10+ grubun içine yerleşmişse tekrar pending'e alma.
         */
        $existingPlacement =
            StudentCoursePlacement::query()
                ->where(
                    'student_id',
                    $row['student_id']
                )
                ->where(
                    'academic_year_id',
                    $academicYearId
                )
                ->where(
                    'student_course_selection_id',
                    $row['selection']->id
                )
                ->whereIn(
                    'status',
                    [1, 2, 3]
                )
                ->first();

        if ($existingPlacement) {
            continue;
        }

        /*
         * Hâlâ yerleşmediyse ileri taşı.
         */
        $stillPending->push([
            'student_id' =>
                $row['student_id'],

            'category_id' =>
                $row['category_id'],

            'weekly_hours' =>
                $row['weekly_hours'],

            'failed_preference_order' =>
                $preferenceOrder,

            'selection' =>
                $row['selection'],

            'resolved' =>
                $row['resolved'] ?? null,

            'carry_forward' =>
                false,
        ]);
    }

    return [
        'pending' =>
            $this->uniquePending(
                $stillPending
            ),

        'placed_count' =>
            $placedCount,
    ];
}

    /**
     * 1. / 2. / 3. tercih için saat + modül
     * çözümünü yapar.
     */
    protected function resolveSelection(
        StudentCourseSelection $selection,
        int $weeklyHours
    ): ?array {
        $course =
            $selection->course;

        if (!$course) {
            return null;
        }

        /*
         * Dersin öğrencinin istediği saate uygun
         * aktif grade option'ı olmalı.
         */
        $gradeOption =
            $course->gradeOptions
                ->where('active', true)
                ->firstWhere(
                    'weekly_hours',
                    $weeklyHours
                );

        if (!$gradeOption) {
            return null;
        }

        /*
         * Modüler derslerde öğrenci programı
         * selection'dan gelir.
         */
        $module = null;

        if ($course->is_modular) {
            $module =
                $this->placementService->suggestedModule(
                    $selection->student,
                    $selection
                );

            if (!$module) {
                return null;
            }
        }

        return [
            'course_id' =>
                (int) $course->id,

            'module_group_id' =>
                $selection->course_module_group_id,

            'module_id' =>
                $module?->id,

            'grade_option_id' =>
                (int) $gradeOption->id,

            'weekly_hours' =>
                (int) $weeklyHours,

            'course_category_id' =>
                (int) $course->course_category_id,

            'course' =>
                $course,

            'module' =>
                $module,
        ];
    }

    /**
     * Aynı ders + program + modül + saat
     * kombinasyonunda zaten açık grup var mı?
     */
    protected function findExistingMatchingGroup(
        int $academicYearId,
        array $resolved,
        ?int $maximumStudentsPerGroup
    ): ?StudentCourseGroup {
        $query =
            StudentCourseGroup::query()
                ->where(
                    'academic_year_id',
                    $academicYearId
                )
                ->where(
                    'course_id',
                    $resolved['course_id']
                )
                ->where(
                    'weekly_hours',
                    $resolved['weekly_hours']
                )
                ->whereIn(
                    'status',
                    [1, 2]
                )
                ->orderBy('group_number');

        if (
            $resolved['module_group_id'] === null
        ) {
            $query->whereNull(
                'course_module_group_id'
            );
        } else {
            $query->where(
                'course_module_group_id',
                $resolved['module_group_id']
            );
        }

        if (
            $resolved['module_id'] === null
        ) {
            $query->whereNull(
                'course_module_id'
            );
        } else {
            $query->where(
                'course_module_id',
                $resolved['module_id']
            );
        }

        foreach ($query->get() as $group) {
            $count =
                $group->placements()
                    ->whereIn(
                        'status',
                        [1, 2, 3]
                    )
                    ->count();

            $maximum =
                $group->maximum_students
                ??
                $maximumStudentsPerGroup;

            if (
                $maximum === null
                ||
                $count < (int) $maximum
            ) {
                return $group;
            }
        }

        return null;
    }

    /**
     * İlk/ikinci/üçüncü tercihler sonunda açıkta kalan
     * öğrencileri aynı kategori içindeki MEVCUT
     * gruplara dağıtır.
     *
     * Burada yeni grup oluşturulmaz.
     */
    protected function placeIntoExistingCategoryGroups(
    Collection $pending,
    Collection $allSelections
): array {
    $unresolved = collect();
    $movedCount = 0;

    foreach ($pending as $item) {
        $studentId =
            (int) $item['student_id'];

        $categoryId =
            (int) $item['category_id'];

        $weeklyHours =
            (int) $item['weekly_hours'];

        /*
         * Öğrencinin bu kategorideki 1. tercih kaydını bul.
         *
         * Placement'ın hangi öğrenci-kategori tercihinden
         * doğduğunu koruyacağız.
         */
        $primarySelection =
            $allSelections
                ->where(
                    'student_id',
                    $studentId
                )
                ->where(
                    'course.course_category_id',
                    $categoryId
                )
                ->where(
                    'preference_order',
                    1
                )
                ->first();

        /*
         * Collection üzerinde nested relation filtresi
         * her durumda güvenilir olmayabileceği için fallback:
         */
        if (! $primarySelection) {
            $primarySelection =
                $allSelections
                    ->first(
                        function ($selection) use (
                            $studentId,
                            $categoryId
                        ) {
                            return
                                (int) $selection->student_id
                                    === $studentId
                                &&
                                (int) $selection
                                    ->course
                                    ->course_category_id
                                    === $categoryId
                                &&
                                (int) $selection
                                    ->preference_order
                                    === 1;
                        }
                    );
        }

        if (! $primarySelection) {
            $unresolved->push([
                ...$item,
                'reason' =>
                    'Öğrencinin bu kategori için ana tercihi bulunamadı.',
            ]);

            continue;
        }

        /*
         * Aynı kategoriye ait mevcut placement'ı bul.
         *
         * Önce primary selection üzerinden arıyoruz.
         */
        $placement =
            StudentCoursePlacement::query()
                ->where(
                    'student_id',
                    $studentId
                )
                ->where(
                    'academic_year_id',
                    $primarySelection->academic_year_id
                )
                ->where(
                    'student_course_selection_id',
                    $primarySelection->id
                )
                ->first();

        /*
         * Primary placement yoksa aynı öğrenci + yıl +
         * kategori üzerinden mevcut placement'ı ara.
         *
         * Böylece 2. veya 3. tercih aşamasında oluşturulmuş
         * placement da yeniden kullanılabilir.
         */
        if (! $placement) {
            $placement =
                StudentCoursePlacement::query()
                    ->where(
                        'student_id',
                        $studentId
                    )
                    ->where(
                        'academic_year_id',
                        $primarySelection->academic_year_id
                    )
                    ->whereHas(
                        'selection.course',
                        function ($query) use (
                            $categoryId
                        ) {
                            $query->where(
                                'course_category_id',
                                $categoryId
                            );
                        }
                    )
                    ->first();
        }

        /*
         * Aynı kategori içinde uygun mevcut grup ara.
         *
         * Burada KESİNLİKLE yeni grup oluşturulmuyor.
         */
        $candidateGroups =
            StudentCourseGroup::query()
                ->with([
                    'course',
                    'course.gradeOptions',
                ])
                ->where(
                    'academic_year_id',
                    $primarySelection->academic_year_id
                )
                ->whereIn(
                    'status',
                    [1, 2]
                )
                ->where(
                    'weekly_hours',
                    $weeklyHours
                )
                ->whereHas(
                    'course',
                    function ($query) use (
                        $categoryId
                    ) {
                        $query->where(
                            'course_category_id',
                            $categoryId
                        );
                    }
                )
                ->orderBy('group_number')
                ->get()
                ->filter(
                    function (
                        StudentCourseGroup $group
                    ) {
                        $count =
                            $group->placements()
                                ->whereIn(
                                    'status',
                                    [1, 2, 3]
                                )
                                ->count();

                        return
                            $group->maximum_students === null
                            ||
                            $count <
                            (int) $group->maximum_students;
                    }
                )
                ->sortBy(
                    function (
                        StudentCourseGroup $group
                    ) {
                        return $group
                            ->placements()
                            ->whereIn(
                                'status',
                                [1, 2, 3]
                            )
                            ->count();
                    }
                )
                ->values();

        $group =
            $candidateGroups->first();

        if (! $group) {
            $unresolved->push([
                ...$item,
                'reason' =>
                    'Üç tercih için de grup oluşmadı ve aynı kategori içinde uygun mevcut grup bulunamadı.',
            ]);

            continue;
        }

        /*
         * Grubun haftalık saatine karşılık gelen
         * gerçek grade option.
         */
        $gradeOption =
            $group->course
                ?->gradeOptions
                ?->where(
                    'active',
                    true
                )
                ?->firstWhere(
                    'weekly_hours',
                    $group->weekly_hours
                );

        if (! $gradeOption) {
            $unresolved->push([
                ...$item,
                'reason' =>
                    'Hedef grubun haftalık saatine uygun ders saati bulunamadı.',
            ]);

            continue;
        }

        /*
         * MEVCUT PLACEMENT VARSA GÜNCELLE.
         *
         * Duplicate selection_id hatasının çözümü burası.
         */
        $data = [
            'student_id' =>
                $studentId,

            'academic_year_id' =>
                $primarySelection->academic_year_id,

            /*
             * Orijinal öğrencinin kategori tercihini
             * koruyoruz.
             *
             * Placement'ın "hangi tercih kaydından doğduğu"
             * kaybolmuyor.
             */
            'student_course_selection_id' =>
                $placement?->student_course_selection_id
                ??
                $primarySelection->id,

            'student_course_group_id' =>
                $group->id,

            'course_id' =>
                $group->course_id,

            'course_module_group_id' =>
                $group->course_module_group_id,

            'course_module_id' =>
                $group->course_module_id,

            'course_grade_option_id' =>
                $gradeOption->id,

            'weekly_hours' =>
                $group->weekly_hours,

            'status' =>
                2,

            'placed_at' =>
                now(),

            'confirmed_at' =>
                null,

            'notes' =>
                'İlk üç tercihe uygun grup oluşmadığı için aynı kategori içindeki mevcut gruba otomatik dağıtıldı.',
        ];

        if ($placement) {
            $placement->update($data);
        } else {
            /*
             * Gerçekten hiç placement yoksa ilk kez oluştur.
             */
            $placement =
                StudentCoursePlacement::create(
                    $data
                );
        }

        $movedCount++;
    }

    return [
        'moved_count' =>
            $movedCount,

        'unresolved' =>
            $unresolved,
    ];
}

    /**
     * Grubun kullandığı grade option.
     */
    protected function groupGradeOptionId(
        StudentCourseGroup $group
    ): ?int {
        return $group->course
            ?->gradeOptions
            ?->firstWhere(
                'weekly_hours',
                $group->weekly_hours
            )
            ?->id;
    }

    /**
     * Placement oluşturur.
     */
    protected function createOrUpdatePlacement(
    StudentCourseSelection $selection,
    StudentCourseGroup $group,
    array $resolved,
    int $preferenceOrder
): StudentCoursePlacement {
    $notes = null;

    if ($preferenceOrder === 2) {
        $notes =
            '2. tercih üzerinden otomatik yerleştirildi.';
    } elseif ($preferenceOrder === 3) {
        $notes =
            '3. tercih üzerinden otomatik yerleştirildi.';
    }

    $data = [
        'student_id' =>
            $selection->student_id,

        'academic_year_id' =>
            $selection->academic_year_id,

        'student_course_selection_id' =>
            $selection->id,

        'student_course_group_id' =>
            $group->id,

        'course_id' =>
            $resolved['course_id'],

        'course_module_group_id' =>
            $resolved['module_group_id'],

        'course_module_id' =>
            $resolved['module_id'],

        'course_grade_option_id' =>
            $resolved['grade_option_id'],

        'weekly_hours' =>
            $resolved['weekly_hours'],

        'status' =>
            2,

        'placed_at' =>
            now(),

        'confirmed_at' =>
            null,

        'notes' =>
            $notes,
    ];

    /*
     * Placement artık öğrenci + yıl ile değil,
     * doğrudan ilgili seçim kaydı ile eşleştirilir.
     */
    $placement =
        StudentCoursePlacement::query()
            ->where(
                'student_course_selection_id',
                $selection->id
            )
            ->first();

    if ($placement) {
        $placement->update($data);

        return $placement->fresh();
    }

    return StudentCoursePlacement::create(
        $data
    );
}
    /**
     * Yeni grup oluşturur.
     *
     * Bu metod yalnızca çağıran taraf havuzun
     * 10+ olduğunu doğruladığında kullanılmalıdır.
     */
    protected function createGroup(
        int $academicYearId,
        array $resolved,
        int $groupNumber,
        int $studentCount,
        ?int $maximumStudentsPerGroup,
        int $preferenceOrder
    ): StudentCourseGroup {
        $label =
            match ($preferenceOrder) {
                1 =>
                    '1. tercih grubu.',

                2 =>
                    '2. tercih üzerinden oluşturulan grup.',

                3 =>
                    '3. tercih üzerinden oluşturulan grup.',

                default =>
                    'Otomatik oluşturulan grup.',
            };

        return StudentCourseGroup::create([
            'academic_year_id' =>
                $academicYearId,

            'course_id' =>
                $resolved['course_id'],

            'course_module_group_id' =>
                $resolved['module_group_id'],

            'course_module_id' =>
                $resolved['module_id'],

            'weekly_hours' =>
                $resolved['weekly_hours'],

            'group_number' =>
                $groupNumber,

            'minimum_students' =>
                10,

            'maximum_students' =>
                $maximumStudentsPerGroup,

            'status' =>
                1,

            'auto_created' =>
                true,

            'confirmed_at' =>
                null,

            'notes' =>
                $label,
        ]);
    }

    protected function bucketKey(
        int $courseId,
        ?int $moduleGroupId,
        ?int $moduleId,
        int $weeklyHours
    ): string {
        return implode(':', [
            $courseId,
            $moduleGroupId ?? 0,
            $moduleId ?? 0,
            $weeklyHours,
        ]);
    }

    protected function calculateGroupCount(
        int $studentCount,
        ?int $maximumStudentsPerGroup
    ): int {
        if ($studentCount <= 0) {
            return 0;
        }

        if (
            $maximumStudentsPerGroup === null
            ||
            $maximumStudentsPerGroup <= 0
        ) {
            return 1;
        }

        return max(
            1,
            (int) ceil(
                $studentCount
                /
                $maximumStudentsPerGroup
            )
        );
    }

    /**
     * Öğrencileri dengeli gruplara ayırır.
     *
     * 21 / 2 → 11 + 10
     * 31 / 2 → 16 + 15
     */
    protected function balancedChunks(
        Collection $rows,
        int $groupCount
    ): array {
        $total =
            $rows->count();

        if (
            $groupCount <= 1
            ||
            $total <= 1
        ) {
            return [
                $rows->values(),
            ];
        }

        $groupCount =
            min(
                $groupCount,
                $total
            );

        $base =
            intdiv(
                $total,
                $groupCount
            );

        $remainder =
            $total % $groupCount;

        $chunks = [];
        $offset = 0;

        for (
            $i = 0;
            $i < $groupCount;
            $i++
        ) {
            $size =
                $base
                +
                (
                    $i < $remainder
                        ? 1
                        : 0
                );

            $chunks[] =
                $rows
                    ->slice(
                        $offset,
                        $size
                    )
                    ->values();

            $offset += $size;
        }

        return $chunks;
    }

    /**
     * Aynı öğrencinin pending listesini tekilleştir.
     */
    protected function uniquePending(
        Collection $pending
    ): Collection {
        return $pending
            ->sortBy(
                fn ($item) =>
                    $item['failed_preference_order']
                    ??
                    99
            )
            ->groupBy(function ($item) {
                return
                    $item['student_id']
                    . ':'
                    . $item['category_id'];
            })
            ->map(
                fn ($items) =>
                    $items->sortByDesc(
                        'failed_preference_order'
                    )->first()
            )
            ->values();
    }

    /**
     * Grup notlarını güncelle.
     */
    protected function refreshGroupNotes(
        int $academicYearId
    ): void {
        $groups =
            StudentCourseGroup::query()
                ->where(
                    'academic_year_id',
                    $academicYearId
                )
                ->whereIn(
                    'status',
                    [1, 2]
                )
                ->get();

        foreach ($groups as $group) {
            $count =
                $group->placements()
                    ->whereIn(
                        'status',
                        [1, 2, 3]
                    )
                    ->count();

            $notes = [];

            if (
                $count <
                (int) $group->minimum_students
            ) {
                /*
                 * Normal koşulda bu oluşmamalı;
                 * güvenlik kontrolü.
                 */
                $notes[] =
                    'Minimum öğrenci sayısının altında.';
            }

            if (
                $group->maximum_students !== null
                &&
                $count >=
                (int) $group->maximum_students
            ) {
                $notes[] =
                    'Grup maksimum öğrenci sayısına ulaştı.';
            }

            $group->update([
                'notes' =>
                    empty($notes)
                        ? null
                        : implode(
                            ' ',
                            $notes
                        ),
            ]);
        }
    }

    protected function clearDraftData(
    int $academicYearId
    ): void {
    $draftGroupIds = StudentCourseGroup::query()
        ->where('academic_year_id', $academicYearId)
        ->where('status', 1)
        ->pluck('id');

    if ($draftGroupIds->isNotEmpty()) {
        StudentCoursePlacement::query()
            ->whereIn(
                'student_course_group_id',
                $draftGroupIds
            )
            ->whereIn('status', [1, 2])
            ->delete();

        StudentCourseGroup::query()
            ->whereIn('id', $draftGroupIds)
            ->delete();
    }

    /*
     * Gruplara bağlı olmayan eski taslak placementları da temizle.
     */
    StudentCoursePlacement::query()
        ->where('academic_year_id', $academicYearId)
        ->whereIn('status', [1, 2])
        ->delete();
    }

}