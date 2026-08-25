<?php

namespace App\Services;

use App\Models\StudentCourseGroup;
use App\Models\StudentCoursePlacement;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class GroupManagementService
{
    /**
     * Bir öğrenciyi manuel olarak başka bir gruba taşır.
     *
     * Hedef grup:
     * - aktif/taslak olmalı
     * - kapasitesi dolu olmamalı
     * - öğrenciyle aynı akademik yılda olmalı
     * - öğrencinin kategorisiyle uyumlu olmalı
     * - ders/saat/program/modül uyumsuzluğu varsa reddedilir
     */
    public function moveStudent(
        StudentCoursePlacement $placement,
        StudentCourseGroup $targetGroup
    ): StudentCoursePlacement {
        return DB::transaction(function () use (
            $placement,
            $targetGroup
        ) {

            if ((int) $placement->status === 3) {
                throw new RuntimeException(
                    'Kesinleştirilmiş bir öğrenci yerleşimi normal taşıma işlemiyle değiştirilemez.'
                );
            }
            $placement->load([
                'student',
                'selection.course.category',
                'course',
                'group',
            ]);

            $targetGroup->load([
                'course.category',
                'moduleGroup',
                'module',
            ]);

            /*
             * Hedef grup aktif veya taslak olmalı.
             */
            if (
                ! in_array(
                    (int) $targetGroup->status,
                    [1, 2],
                    true
                )
            ) {
                throw new RuntimeException(
                    'Hedef grup aktif değil.'
                );
            }

            /*
             * Aynı eğitim yılı.
             */
            if (
                (int) $targetGroup->academic_year_id
                !==
                (int) $placement->academic_year_id
            ) {
                throw new RuntimeException(
                    'Farklı eğitim yılına ait gruba öğrenci taşınamaz.'
                );
            }

            /*
             * Aynı gruba taşınmaya çalışılıyorsa
             * işlem yapma.
             */
            if (
                $placement->student_course_group_id !== null
                &&
                (int) $placement->student_course_group_id
                ===
                (int) $targetGroup->id
            ) {
                return $placement->fresh([
                    'group',
                    'student',
                    'course',
                    'module',
                ]);
            }

            /*
             * Öğrencinin tercih kategorisi.
             *
             * Placement'ın gerçek course'u fallback sebebiyle
             * değişmiş olabilir; bu yüzden selection üzerinden
             * öğrencinin asıl kategori bilgisini alıyoruz.
             */
            $selectionCategoryId =
                $placement
                ->selection
                ?->course
                ?->course_category_id;

            if (
                ! $selectionCategoryId
            ) {
                throw new RuntimeException(
                    'Öğrencinin tercih kategorisi belirlenemedi.'
                );
            }

            /*
             * Hedef grubun kategorisi.
             */
            $targetCategoryId =
                $targetGroup
                ->course
                ?->course_category_id;

            if (
                ! $targetCategoryId
            ) {
                throw new RuntimeException(
                    'Hedef grubun ders kategorisi belirlenemedi.'
                );
            }

            /*
             * Okul son kontrolde öğrenciyi başka
             * kategoriye taşıyamaz.
             */
            if (
                (int) $selectionCategoryId
                !==
                (int) $targetCategoryId
            ) {
                throw new RuntimeException(
                    'Öğrenci yalnızca aynı ders kategorisi içindeki gruba taşınabilir.'
                );
            }

            /*
             * Hedef grubun kapasitesi.
             */
            $targetCount =
                $targetGroup
                ->placements()
                ->whereIn(
                    'status',
                    [1, 2, 3]
                )
                ->where(
                    'id',
                    '!=',
                    $placement->id
                )
                ->count();

            if (
                $targetGroup->maximum_students !== null
                &&
                $targetCount >=
                (int) $targetGroup->maximum_students
            ) {
                throw new RuntimeException(
                    'Hedef grubun maksimum öğrenci sayısı dolu.'
                );
            }

            /*
             * Hedef grubun dersinin ilgili saat seçeneği
             * gerçekten var mı?
             */
            $targetGradeOption =
                $targetGroup
                ->course
                ?->gradeOptions()
                ->where(
                    'active',
                    true
                )
                ->where(
                    'weekly_hours',
                    $targetGroup->weekly_hours
                )
                ->first();

            if (
                ! $targetGradeOption
            ) {
                throw new RuntimeException(
                    'Hedef grubun haftalık ders saatine uygun ders seçeneği bulunamadı.'
                );
            }

            /*
             * Öğrencinin sınıfı ile hedef dersin
             * saat seçeneği uyumlu mu?
             */
            $studentGrade =
                $placement
                ->student
                ?->studentYears()
                ->where(
                    'academic_year_id',
                    $placement->academic_year_id
                )
                ->where(
                    'active',
                    true
                )
                ->value('grade');

            if (
                ! $studentGrade
                ||
                (int) $targetGradeOption->grade
                !==
                (int) $studentGrade
            ) {
                throw new RuntimeException(
                    'Hedef ders bu öğrencinin sınıfı için uygun değil.'
                );
            }

            /*
             * Modüler dersse program/modül bilgileri
             * tutarlı olmalı.
             *
             * Manuel taşımada ders değişmesine izin verdiğimiz
             * için eski placement bilgisini aynen korumuyoruz;
             * hedef grubun gerçek değerlerini kullanıyoruz.
             */
            $placement->update([
                'student_course_group_id' =>
                $targetGroup->id,

                'course_id' =>
                $targetGroup->course_id,

                'course_module_group_id' =>
                $targetGroup->course_module_group_id,

                'course_module_id' =>
                $targetGroup->course_module_id,

                'course_grade_option_id' =>
                $targetGradeOption->id,

                'weekly_hours' =>
                $targetGroup->weekly_hours,

                'status' =>
                2,

                'placed_at' =>
                now(),

                'confirmed_at' =>
                null,

                'notes' =>
                'Okul tarafından manuel olarak değiştirildi.',
            ]);

            return $placement->fresh([
                'group',
                'student',
                'course',
                'module',
                'selection.course',
            ]);
        });
    }

    /**
     * Bir grubu kapatır ve öğrencilerini yeniden dağıtır.
     *
     * Dağıtım sırası:
     *
     * 1. Öğrencinin 2. tercihi
     * 2. Öğrencinin 3. tercihi
     * 3. Aynı kategoride mevcut uygun grup
     *
     * Yeni 1-9 kişilik grup oluşturulmaz.
     */
    public function closeAndRedistribute(
        StudentCourseGroup $group
    ): array {
        return DB::transaction(function () use ($group) {

            $group->load([
                'course.category',
                'placements.student',
                'placements.selection.course',
            ]);

            if ($group->confirmed_at !== null) {
                throw new RuntimeException(
                    'Kesinleştirilmiş grup kapatılamaz.'
                );
            }

            if ($group->confirmed_at !== null) {
                throw new RuntimeException(
                    'Kesinleştirilmiş grup kapatılamaz.'
                );
            }

            if (
                (int) $group->status === 4
            ) {
                throw new RuntimeException(
                    'Grup zaten kapalı.'
                );
            }

            $placements =
                $group->placements()
                ->with([
                    'student',
                    'selection.course',
                ])
                ->whereIn(
                    'status',
                    [1, 2]
                )
                ->lockForUpdate()
                ->get();

            if ($placements->isEmpty()) {
                $group->update([
                    'status' => 4,
                ]);

                return [
                    'group' =>
                    $group->fresh(),

                    'moved' =>
                    0,

                    'unmoved' =>
                    [],

                    'success' =>
                    true,
                ];
            }

            $moved = 0;
            $unmoved = [];

            foreach ($placements as $placement) {
                $target =
                    $this->findRedistributionTarget(
                        $placement,
                        $group
                    );

                if (! $target) {
                    $unmoved[] =
                        $placement->id;

                    continue;
                }

                $this->moveStudent(
                    $placement,
                    $target
                );

                $moved++;
            }

            /*
             * Ancak bütün öğrenciler başka gruba taşındıysa
             * grubu kapat.
             */
            if (empty($unmoved)) {
                $group->update([
                    'status' => 4,
                ]);
            }

            return [
                'group' =>
                $group->fresh(),

                'moved' =>
                $moved,

                'unmoved' =>
                $unmoved,

                'success' =>
                empty($unmoved),
            ];
        });
    }

    /**
     * Kapatılan grubun öğrencisi için uygun
     * hedef grubu bulur.
     *
     * Öncelik:
     *
     * 2. tercih
     * 3. tercih
     * Aynı kategori mevcut grup
     */
    protected function findRedistributionTarget(
        StudentCoursePlacement $placement,
        StudentCourseGroup $closedGroup
    ): ?StudentCourseGroup {
        $selection =
            $placement->selection;

        if (! $selection) {
            return $this->findExistingCategoryGroup(
                $placement,
                $closedGroup
            );
        }

        $student =
            $placement->student;

        /*
         * Önce aynı öğrencinin 2. tercihi.
         */
        $second =
            $student
            ->courseSelections()
            ->with([
                'course.category',
                'course.gradeOptions',
                'moduleGroup',
            ])
            ->where(
                'academic_year_id',
                $placement->academic_year_id
            )
            ->where(
                'preference_order',
                2
            )
            ->where(
                'status',
                2
            )
            ->get();

        foreach ($second as $candidate) {
            $group =
                $this->findGroupForSelection(
                    $candidate,
                    $placement
                );

            if ($group) {
                return $group;
            }
        }

        /*
         * Sonra 3. tercih.
         */
        $third =
            $student
            ->courseSelections()
            ->with([
                'course.category',
                'course.gradeOptions',
                'moduleGroup',
            ])
            ->where(
                'academic_year_id',
                $placement->academic_year_id
            )
            ->where(
                'preference_order',
                3
            )
            ->where(
                'status',
                2
            )
            ->get();

        foreach ($third as $candidate) {
            $group =
                $this->findGroupForSelection(
                    $candidate,
                    $placement
                );

            if ($group) {
                return $group;
            }
        }

        /*
         * Son olarak aynı kategori içinde
         * mevcut uygun grup.
         */
        return $this->findExistingCategoryGroup(
            $placement,
            $closedGroup
        );
    }

    /**
     * Bir öğrenci tercihi için mevcut uygun grubu bulur.
     *
     * Yeni grup oluşturmaz.
     */
    protected function findGroupForSelection(
        $selection,
        StudentCoursePlacement $placement
    ): ?StudentCourseGroup {
        $categoryId =
            $selection
            ->course
            ?->course_category_id;

        if (!$categoryId) {
            return null;
        }

        /*
         * Öğrencinin ilk tercihindeki haftalık saat.
         */
        $weeklyHours =
            $placement->weekly_hours;

        if (!$weeklyHours) {
            return null;
        }

        /*
         * Alternatif dersin bu saatte grade option'ı
         * olmalı.
         */
        $gradeOption =
            $selection
            ->course
            ->gradeOptions
            ->where(
                'active',
                true
            )
            ->firstWhere(
                'weekly_hours',
                $weeklyHours
            );

        if (!$gradeOption) {
            return null;
        }

        $groups =
            StudentCourseGroup::query()
            ->with('course')
            ->where(
                'academic_year_id',
                $placement->academic_year_id
            )
            ->where(
                'course_id',
                $selection->course_id
            )
            ->where(
                'weekly_hours',
                $weeklyHours
            )
            ->whereIn(
                'status',
                [1, 2]
            )
            ->where(
                'id',
                '!=',
                $placement->student_course_group_id
            )
            ->orderBy('group_number')
            ->get();

        foreach ($groups as $group) {
            $count =
                $group
                ->placements()
                ->whereIn(
                    'status',
                    [1, 2, 3]
                )
                ->count();

            if (
                $group->maximum_students !== null
                &&
                $count >=
                (int) $group->maximum_students
            ) {
                continue;
            }

            return $group;
        }

        return null;
    }

    /**
     * Aynı kategori içinde mevcut uygun grup bul.
     *
     * Yeni grup oluşturulmaz.
     */
    protected function findExistingCategoryGroup(
        StudentCoursePlacement $placement,
        StudentCourseGroup $closedGroup
    ): ?StudentCourseGroup {
        $categoryId =
            $placement
            ->selection
            ?->course
            ?->course_category_id;

        if (!$categoryId) {
            return null;
        }

        $weeklyHours =
            $placement->weekly_hours;

        $groups =
            StudentCourseGroup::query()
            ->with('course')
            ->where(
                'academic_year_id',
                $placement->academic_year_id
            )
            ->where(
                'id',
                '!=',
                $closedGroup->id
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
            ->get();

        /*
         * En az dolu grubu tercih et.
         */
        $groups =
            $groups
            ->filter(
                function (
                    StudentCourseGroup $group
                ) {
                    $count =
                        $group
                        ->placements()
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

        return $groups->first();
    }
}
