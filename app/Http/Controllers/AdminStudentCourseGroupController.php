<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\StudentCourseGroup;
use App\Models\StudentCoursePlacement;
use App\Models\StudentCourseSelection;
use App\Services\GroupGenerationService;
use App\Services\GroupManagementService;
use App\Services\PlacementService;
use Illuminate\Http\Request;
use RuntimeException;

class AdminStudentCourseGroupController extends Controller
{
    public function index(Request $request)
    {
        $academicYears =
            AcademicYear::orderByDesc('name')->get();

        $academicYear = AcademicYear::where(
            'id',
            $request->input(
                'academic_year_id',
                AcademicYear::where(
                    'active',
                    true
                )->value('id')
            )
        )->firstOrFail();

        $groups = StudentCourseGroup::query()
            ->with([
                'course.category',
                'moduleGroup',
                'module',
                'placements.student',
                'placements.selection.course',
            ])
            ->where(
                'academic_year_id',
                $academicYear->id
            )
            ->orderBy('course_id')
            ->orderBy('course_module_group_id')
            ->orderBy('course_module_id')
            ->orderBy('weekly_hours')
            ->orderBy('group_number')
            ->get();

        /*
         * Manuel taşıma ekranında kullanılabilecek
         * aktif gruplar da Blade'e gönderiliyor.
         */
        $activeGroups = StudentCourseGroup::query()
            ->with([
                'course',
                'moduleGroup',
                'module',
            ])
            ->where(
                'academic_year_id',
                $academicYear->id
            )
            ->whereIn(
                'status',
                [1, 2]
            )
            ->orderBy('course_id')
            ->orderBy('course_module_group_id')
            ->orderBy('course_module_id')
            ->orderBy('weekly_hours')
            ->orderBy('group_number')
            ->get();

        return view(
            'admin.student-course-groups.index',
            compact(
                'academicYears',
                'academicYear',
                'groups',
                'activeGroups'
            )
        );
    }

    /**
     * Manuel yerleştirme ekranı.
     *
     * Yerleştirilmiş ancak kesinleşmemiş kayıtları ve
     * henüz yerleştirilmemiş öğrenci-kategori kayıtlarını gösterir.
     */
    public function manualPlacement(
        Request $request,
        PlacementService $placementService
    ) {
        $academicYears =
            AcademicYear::orderByDesc('name')
            ->get();

        $academicYear =
            AcademicYear::where(
                'id',
                $request->input(
                    'academic_year_id',
                    AcademicYear::where(
                        'active',
                        true
                    )->value('id')
                )
            )
            ->firstOrFail();

        $search =
            trim(
                (string) $request->input(
                    'search',
                    ''
                )
            );

        $selections =
            StudentCourseSelection::query()
            ->with([
                'student',
                'course.category',
            ])
            ->where(
                'academic_year_id',
                $academicYear->id
            )
            ->whereIn(
                'status',
                [1, 2, 3]
            )
            ->when(
                $search !== '',
                function ($query) use ($search) {
                    $query->whereHas(
                        'student',
                        function ($studentQuery) use ($search) {
                            $studentQuery
                                ->where(
                                    'first_name',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'last_name',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'student_number',
                                    'like',
                                    "%{$search}%"
                                );
                        }
                    );
                }
            )
            ->get();

        $placements =
            StudentCoursePlacement::query()
            ->with([
                'selection.course.category',
                'student',
                'course',
                'group',
                'module',
            ])
            ->where(
                'academic_year_id',
                $academicYear->id
            )
            ->whereIn(
                'status',
                [1, 2]
            )
            ->get()
            ->keyBy(
                'student_course_selection_id'
            );

        /*
     * Öğrenci + kategori bazında grupla.
     */
        $studentCategories =
            $selections
            ->groupBy(function ($selection) {
                return
                    $selection->student_id
                    . ':'
                    . (
                        $selection
                        ->course
                        ?->course_category_id
                    );
            })
            ->map(
                function ($categorySelections) use (
                    $placements
                ) {
                    $first =
                        $categorySelections->first();

                    $category =
                        $first
                        ->course
                        ?->category;

                    $selectionIds =
                        $categorySelections
                        ->pluck('id');

                    /*
                     * Bu kategori için mevcut
                     * herhangi bir yerleştirme var mı?
                     */
                    $placement =
                        $categorySelections
                        ->map(
                            fn($selection) =>
                            $placements->get(
                                $selection->id
                            )
                        )
                        ->filter()
                        ->sortByDesc('id')
                        ->first();

                    /*
                     * Tercihleri 1 → 2 → 3 sırala.
                     */
                    $sortedSelections =
                        $categorySelections
                        ->sortBy(
                            fn($selection) =>
                            (int)
                            $selection
                                ->preference_order
                        )
                        ->values();

                    return [
                        'student' =>
                        $first->student,

                        'student_id' =>
                        $first->student_id,

                        'category' =>
                        $category,

                        'category_id' =>
                        $category?->id,

                        'selections' =>
                        $sortedSelections,

                        'placement' =>
                        $placement,
                    ];
                }
            )
            ->sortBy(function ($row) {
                return sprintf(
                    '%010d-%010d',
                    (int) $row['student_id'],
                    (int) (
                        $row['category']
                        ?->sort_order
                        ?? 999
                    )
                );
            })
            ->values();

        /*
     * Manuel ekranda kullanılabilecek gruplar.
     */
        $activeGroups =
            StudentCourseGroup::query()
            ->with([
                'course.category',
                'moduleGroup',
                'module',
            ])
            ->where(
                'academic_year_id',
                $academicYear->id
            )
            ->whereIn(
                'status',
                [1, 2]
            )
            ->orderBy('course_id')
            ->orderBy('course_module_group_id')
            ->orderBy('course_module_id')
            ->orderBy('weekly_hours')
            ->orderBy('group_number')
            ->get();

        return view(
            'admin.student-course-groups.manual-placement',
            compact(
                'academicYears',
                'academicYear',
                'studentCategories',
                'activeGroups',
                'search'
            )
        );
    }
    /**
     * Öğrenci tercihlerinden otomatik grupları oluşturur.
     */
    public function generate(
        Request $request,
        GroupGenerationService $service
    ) {
        $validated = $request->validate([
            'academic_year_id' => [
                'required',
                'exists:academic_years,id',
            ],

            'maximum_students_per_group' => [
                'nullable',
                'integer',
                'min:10',
                'max:100',
            ],
        ]);

        $academicYearId =
            (int) $validated['academic_year_id'];

        /*
     * Bu eğitim yılında daha önce grup oluşturulmuşsa
     * yeniden otomatik dağıtım yapma.
     *
     * Böylece yönetici aynı butona yanlışlıkla
     * tekrar basarak mevcut dağılımı değiştiremez.
     */
        $existingGroupCount =
            StudentCourseGroup::query()
            ->where(
                'academic_year_id',
                $academicYearId
            )
            ->count();

        if ($existingGroupCount > 0) {
            return back()->withErrors([
                'Bu eğitim yılı için zaten ' .
                    $existingGroupCount .
                    ' grup oluşturulmuş. ' .
                    'Mevcut grupları silmeden yeniden grup oluşturamazsınız.',
            ]);
        }

        $result =
            $service->generate(
                $academicYearId,
                $validated['maximum_students_per_group'] !== null
                    ? (int) $validated['maximum_students_per_group']
                    : null
            );

        $message =
            $result['selection_count'] .
            ' birinci tercih analiz edildi. ' .
            $result['group_count'] .
            ' grup ve ' .
            $result['placement_count'] .
            ' öğrenci-kategori yerleştirmesi oluşturuldu.';

        if (
            isset($result['preference2_count'])
            &&
            $result['preference2_count'] > 0
        ) {
            $message .=
                ' ' .
                $result['preference2_count'] .
                ' öğrenci-kategori 2. tercihe yerleştirildi.';
        }

        if (
            isset($result['preference3_count'])
            &&
            $result['preference3_count'] > 0
        ) {
            $message .=
                ' ' .
                $result['preference3_count'] .
                ' öğrenci-kategori 3. tercihe yerleştirildi.';
        }

        if (
            isset($result['fallback_count'])
            &&
            $result['fallback_count'] > 0
        ) {
            $message .=
                ' ' .
                $result['fallback_count'] .
                ' öğrenci-kategori aynı kategori içindeki mevcut uygun gruba otomatik aktarıldı.';
        }

        if (
            isset($result['unresolved_count'])
            &&
            $result['unresolved_count'] > 0
        ) {
            $message .=
                ' ' .
                $result['unresolved_count'] .
                ' öğrenci-kategori hâlâ manuel yerleştirme gerektiriyor.';
        }

        return back()->with(
            'success',
            $message
        );
    }

    /**
     * Grup durumunu değiştirir.
     */
    public function updateStatus(
        Request $request,
        StudentCourseGroup $group
    ) {

        if ($group->confirmed_at !== null) {
            return back()->withErrors([
                'Kesinleştirilmiş grubun durumu değiştirilemez.',
            ]);
        }

        $validated = $request->validate([
            'status' => [
                'required',
                'integer',
                'in:1,2,4',
            ],
        ]);

        $group->update([
            'status' =>
            (int) $validated['status'],
        ]);

        if (
            (int) $validated['status'] === 4
        ) {
            $group->placements()
                ->whereIn(
                    'status',
                    [1, 2]
                )
                ->update([
                    'status' => 4,
                ]);
        }

        return back()->with(
            'success',
            'Grup durumu güncellendi.'
        );
    }

    /**
     * Grup notunu günceller.
     */
    public function updateNotes(
        Request $request,
        StudentCourseGroup $group
    ) {
        $validated = $request->validate([
            'notes' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ]);

        $group->update([
            'notes' =>
            $validated['notes'] ?? null,
        ]);

        return back()->with(
            'success',
            'Grup notu güncellendi.'
        );
    }

    /**
     * Öğrenciyi manuel olarak başka uygun gruba taşır.
     */
    public function moveStudent(
        Request $request,
        StudentCoursePlacement $placement,
        GroupManagementService $service
    ) {
        $validated = $request->validate([
            'target_group_id' => [
                'required',
                'integer',
                'exists:student_course_groups,id',
            ],
        ]);

        $targetGroup =
            StudentCourseGroup::findOrFail(
                (int) $validated['target_group_id']
            );

        try {
            $service->moveStudent(
                $placement,
                $targetGroup
            );
        } catch (RuntimeException $e) {
            return back()
                ->withInput()
                ->withErrors([
                    $e->getMessage(),
                ]);
        }

        return back()->with(
            'success',
            'Öğrenci yeni gruba taşındı.'
        );
    }

    /**
     * Grubu kapatır ve öğrencilerini otomatik olarak
     * yeniden dağıtır.
     */
    public function closeAndRedistribute(
        StudentCourseGroup $group,
        GroupManagementService $service
    ) {
        try {
            $result =
                $service->closeAndRedistribute(
                    $group
                );
        } catch (RuntimeException $e) {
            return back()
                ->withErrors([
                    $e->getMessage(),
                ]);
        }

        if (! $result['success']) {
            $unmovedCount =
                count($result['unmoved']);

            return back()
                ->withErrors([
                    "Grup kapatılamadı. " .
                        $result['moved'] .
                        " öğrenci başka gruplara taşındı; " .
                        $unmovedCount .
                        " öğrenci için uygun kapasite bulunamadı. " .
                        "Kalan öğrencileri başka gruplara manuel taşıdıktan sonra "
                        . "grubu tekrar kapatmayı deneyebilirsiniz.",
                ]);
        }

        return back()->with(
            'success',
            $result['moved'] .
                ' öğrenci diğer uygun gruplara dağıtıldı ve grup kapatıldı.'
        );
    }

    /**
     * Yerleştirmesi olmayan öğrenci-kategori için
     * manuel yerleştirme oluşturur.
     */
    public function manualPlace(
        Request $request,
        StudentCourseSelection $selection,
        PlacementService $placementService,
        GroupManagementService $groupManagementService
    ) {
        $validated =
            $request->validate([
                'target_group_id' => [
                    'required',
                    'integer',
                    'exists:student_course_groups,id',
                ],
            ]);

        $targetGroup =
            StudentCourseGroup::query()
            ->with([
                'course.category',
                'moduleGroup',
                'module',
            ])
            ->findOrFail(
                $validated['target_group_id']
            );

        if (
            $targetGroup->confirmed_at !== null
            ||
            ! in_array(
                (int) $targetGroup->status,
                [1, 2],
                true
            )
        ) {
            return back()->withErrors([
                'Seçilen grup aktif değildir veya kesinleşmiştir.',
            ]);
        }

        $selectionCategoryId =
            $selection
            ->course
            ?->course_category_id;

        $targetCategoryId =
            $targetGroup
            ->course
            ?->course_category_id;

        if (
            ! $selectionCategoryId
            ||
            ! $targetCategoryId
            ||
            (int) $selectionCategoryId
            !==
            (int) $targetCategoryId
        ) {
            return back()->withErrors([
                'Öğrenci yalnızca aynı kategori içindeki bir gruba yerleştirilebilir.',
            ]);
        }

        /*
     * Aynı öğrenci-kategori için zaten
     * bir yerleştirme varsa yeni kayıt oluşturma.
     */
        $existingPlacement =
            StudentCoursePlacement::query()
            ->where(
                'student_course_selection_id',
                $selection->id
            )
            ->whereIn(
                'status',
                [1, 2]
            )
            ->first();

        if ($existingPlacement) {
            try {
                $groupManagementService->moveStudent(
                    $existingPlacement,
                    $targetGroup
                );

                return back()->with(
                    'success',
                    "{$selection->student->first_name} " .
                        "{$selection->student->last_name} öğrencisinin yerleştirmesi güncellendi."
                );
            } catch (RuntimeException $e) {
                return back()->withErrors([
                    $e->getMessage(),
                ]);
            }
        }

        /*
     * Önce seçim üzerinden bir yerleştirme oluştur.
     *
     * Ardından mevcut taşıma servisini kullanarak
     * hedef grubun gerçek ders/program/modül/saat
     * bilgilerini placement'a aktar.
     */
        $placement =
            $placementService->placeSelection(
                $selection,
                null,
                'Manuel yerleştirme.'
            );

        try {

            $groupManagementService->moveStudent(
                $placement,
                $targetGroup
            );
        } catch (RuntimeException $e) {

            $placement->delete();

            return back()->withErrors([
                $e->getMessage(),
            ]);
        }

        return back()->with(
            'success',
            "{$selection->student->first_name} " .
                "{$selection->student->last_name} öğrencisi " .
                "{$targetGroup->course->name} / Grup " .
                "{$targetGroup->group_number} " .
                'grubuna manuel olarak yerleştirildi.'
        );
    }
}
