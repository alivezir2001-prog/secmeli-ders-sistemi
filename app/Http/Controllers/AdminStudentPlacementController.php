<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\StudentCourseHistory;
use App\Models\StudentCoursePlacement;
use App\Models\StudentCourseSelection;
use App\Services\PlacementService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Models\StudentCourseGroup;

class AdminStudentPlacementController extends Controller
{
    public function index(
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
                    AcademicYear::where('active', true)->value('id')
                )
            )
            ->firstOrFail();

        $filter =
            $request->input(
                'filter',
                'all'
            );

        $search =
            trim(
                (string) $request->input(
                    'search',
                    ''
                )
            );

        $categoryId =
            $request->filled('category_id')
            ? (int) $request->input('category_id')
            : null;

        /*
         * Öğrencilerin bütün seçimleri.
         *
         * Status 3 dahil:
         * 3 = kesinleşmiş seçim.
         *
         * Böylece kesinleşmiş öğrenciler ekrandan
         * kaybolmaz.
         */
        $selections =
            StudentCourseSelection::query()
            ->with([
                'student',
                'course.category',
                'moduleGroup',
                'gradeOption',
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
            ->when(
                $categoryId !== null,
                function ($query) use ($categoryId) {
                    $query->whereHas(
                        'course',
                        function ($courseQuery) use ($categoryId) {
                            $courseQuery->where(
                                'course_category_id',
                                $categoryId
                            );
                        }
                    );
                }
            )
            ->get();

        /*
         * Nihai placement kayıtları.
         *
         * Selection yerine placement sonuç
         * ekranın esas verisidir.
         */
        $placements =
            StudentCoursePlacement::query()
            ->with([
                'selection.course.category',
                'student',
                'course.category',
                'moduleGroup',
                'module',
            ])
            ->where(
                'academic_year_id',
                $academicYear->id
            )
            ->get();

        /*
         * Öğrenci + kategori bazında grupla.
         *
         * Böylece:
         *
         * Mehmet + ITB
         * Mehmet + DAD
         * Mehmet + KSS
         *
         * ayrı birer kontrol kaydı olur.
         */
        $categoryRows =
            $selections
            ->groupBy(function ($selection) {
                $categoryId =
                    $selection->course
                    ?->course_category_id;

                return
                    $selection->student_id
                    . ':'
                    . $categoryId;
            })
            ->map(
                function (Collection $categorySelections) use (
                    $placements,
                    $placementService
                ) {
                    $first =
                        $categorySelections->first();

                    $student =
                        $first->student;

                    $category =
                        $first->course->category;

                    /*
                         * Tercih sırası kesin olarak:
                         * 1 → 2 → 3
                         */
                    $categorySelections =
                        $categorySelections
                        ->sortBy(
                            fn($selection) =>
                            (int) $selection->preference_order
                        )
                        ->values();

                    /*
                         * Bu öğrenci + kategoriye ait
                         * placement'ları bul.
                         *
                         * Normal durumda placement,
                         * tercihlerden birine bağlıdır.
                         */
                    $selectionIds =
                        $categorySelections
                        ->pluck('id')
                        ->map(
                            fn($id) => (int) $id
                        );

                    $categoryPlacements =
                        $placements
                        ->filter(
                            function ($placement) use (
                                $selectionIds
                            ) {
                                return
                                    $selectionIds->contains(
                                        (int) $placement
                                            ->student_course_selection_id
                                    );
                            }
                        )
                        ->values();

                    /*
                         * Normalde yalnızca bir nihai placement
                         * bulunması gerekir.
                         *
                         * Birden fazla varsa en son kayıt,
                         * tek sonuç kabul edilir.
                         */
                    $placement =
                        $categoryPlacements
                        ->sortByDesc('id')
                        ->first();

                    /*
                         * Nihai placement bulunmuşsa,
                         * hangi tercih üzerinden geldiğini bul.
                         */
                    $placementPreference =
                        null;

                    if ($placement) {
                        $matchedSelection =
                            $categorySelections->first(
                                fn($selection) =>
                                (int) $selection->id
                                    ===
                                    (int) $placement
                                        ->student_course_selection_id
                            );

                        if ($matchedSelection) {
                            $placementPreference =
                                (int) $matchedSelection
                                    ->preference_order;
                        }
                    }

                    /*
                         * Placement yoksa 1. tercih için
                         * sistem modül önerisini göster.
                         */
                    $firstPreference =
                        $categorySelections->first(
                            fn($selection) =>
                            (int) $selection->preference_order === 1
                        );

                    $suggestedModule =
                        null;

                    if (
                        ! $placement
                        &&
                        $firstPreference
                    ) {
                        $suggestedModule =
                            $placementService->suggestedModule(
                                $student,
                                $firstPreference
                            );
                    }

                    $notes =
                        (string) (
                            $placement?->notes
                            ?? ''
                        );

                    $isConfirmed =
                        $placement
                        &&
                        (int) $placement->status === 3;

                    $isAutomaticAlternative =
                        $placement
                        &&
                        str_contains(
                            $notes,
                            'otomatik'
                        );

                    $isManualChange =
                        $placement
                        &&
                        str_contains(
                            $notes,
                            'manuel olarak değiştirildi'
                        );

                    /*
                         * Placement var ama placement'ın dersi,
                         * öğrencinin ilgili tercihlerindeki dersten
                         * farklıysa alternatif yerleşimdir.
                         */
                    $isAlternative =
                        false;

                    if ($placement) {
                        $isAlternative =
                            ! $categorySelections->contains(
                                fn($selection) =>
                                (int) $selection->course_id
                                    ===
                                    (int) $placement->course_id
                            );
                    }

                    return [
                        'student' =>
                        $student,

                        'student_id' =>
                        $student->id,

                        'category' =>
                        $category,

                        'category_id' =>
                        $category?->id,

                        'selections' =>
                        $categorySelections,

                        'placement' =>
                        $placement,

                        'placement_preference' =>
                        $placementPreference,

                        'suggestedModule' =>
                        $suggestedModule,

                        'isConfirmed' =>
                        $isConfirmed,

                        'isAutomaticAlternative' =>
                        $isAutomaticAlternative,

                        'isManualChange' =>
                        $isManualChange,

                        'isAlternative' =>
                        $isAlternative,

                        'complete' =>
                        $placement !== null,
                    ];
                }
            )
            ->sortBy(function ($row) {
                return sprintf(
                    '%010d-%010d',
                    (int) $row['student_id'],
                    (int) ($row['category']?->sort_order ?? 999)
                );
            })
            ->values();

        /*
         * Filtreler.
         */
        $categoryRows =
            match ($filter) {
                'missing' =>
                $categoryRows
                    ->filter(
                        fn($row) =>
                        ! $row['placement']
                    )
                    ->values(),

                'automatic' =>
                $categoryRows
                    ->filter(
                        fn($row) =>
                        $row['isAutomaticAlternative']
                    )
                    ->values(),

                'manual' =>
                $categoryRows
                    ->filter(
                        fn($row) =>
                        $row['isManualChange']
                    )
                    ->values(),

                'confirmed' =>
                $categoryRows
                    ->filter(
                        fn($row) =>
                        $row['isConfirmed']
                    )
                    ->values(),

                default =>
                $categoryRows,
            };

        /*
         * Genel özet.
         */
        $totalStudents =
            $categoryRows
            ->pluck('student_id')
            ->unique()
            ->count();

        $totalCategories =
            $categoryRows
            ->pluck('category_id')
            ->filter()
            ->unique()
            ->count();

        $totalRows =
            $categoryRows->count();

        $placedRows =
            $categoryRows
            ->filter(
                fn($row) =>
                $row['placement'] !== null
            )
            ->count();

        $missingRows =
            $categoryRows
            ->whereNull('placement')
            ->count();

        $automaticRows =
            $categoryRows
            ->filter(
                fn($row) =>
                $row['isAutomaticAlternative']
            )
            ->count();

        $manualRows =
            $categoryRows
            ->filter(
                fn($row) =>
                $row['isManualChange']
            )
            ->count();

        $confirmedRows =
            $categoryRows
            ->filter(
                fn($row) =>
                $row['isConfirmed']
            )
            ->count();

        /*
         * Tercih dağılımı.
         *
         * Placement hangi tercihe bağlıysa
         * ona göre say.
         */
        $preference1Count =
            $categoryRows
            ->filter(
                fn($row) =>
                $row['placement']
                    &&
                    (int) $row['placement_preference'] === 1
            )
            ->count();

        $preference2Count =
            $categoryRows
            ->filter(
                fn($row) =>
                $row['placement']
                    &&
                    (int) $row['placement_preference'] === 2
            )
            ->count();

        $preference3Count =
            $categoryRows
            ->filter(
                fn($row) =>
                $row['placement']
                    &&
                    (int) $row['placement_preference'] === 3
            )
            ->count();

        /*
         * Kesinleştirme için gereken toplam öğrenci-kategori.
         *
         * Filtrelenmiş sonuç üzerinden değil,
         * bütün kategori kayıtlarından hesaplanmalı.
         */
        $allCategoryRows =
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
            });

        $requiredPlacementCount =
            $allCategoryRows->count();

        $placedCategoryKeys =
            $allCategoryRows
            ->filter(
                function (Collection $categorySelections) use (
                    $placements
                ) {
                    $selectionIds =
                        $categorySelections
                        ->pluck('id')
                        ->map(
                            fn($id) => (int) $id
                        );

                    return $placements->contains(
                        function ($placement) use (
                            $selectionIds
                        ) {
                            return
                                in_array(
                                    (int) $placement->status,
                                    [2, 3],
                                    true
                                )
                                &&
                                $selectionIds->contains(
                                    (int) $placement
                                        ->student_course_selection_id
                                );
                        }
                    );
                }
            )
            ->count();

        $confirmReady =
            $requiredPlacementCount > 0
            &&
            $placedCategoryKeys ===
            $requiredPlacementCount;

        /*
         * Filtrelerde kullanılacak kategoriler.
         */
        $categories =
            $selections
            ->map(
                fn($selection) =>
                $selection->course->category
            )
            ->filter()
            ->unique('id')
            ->sortBy('sort_order')
            ->values();

        return view(
            'admin.student-placements.index',
            compact(
                'academicYears',
                'academicYear',
                'categoryRows',
                'categories',
                'filter',
                'search',
                'categoryId',
                'totalStudents',
                'totalCategories',
                'totalRows',
                'placedRows',
                'missingRows',
                'automaticRows',
                'manualRows',
                'confirmedRows',
                'preference1Count',
                'preference2Count',
                'preference3Count',
                'requiredPlacementCount',
                'placedCategoryKeys',
                'confirmReady'
            )
        );
    }

    /**
     * Yalnızca gerçek bir placement gerektiğinde
     * manuel olarak placement oluşturur/günceller.
     */
    public function place(
        Request $request,
        StudentCourseSelection $selection,
        PlacementService $placementService
    ) {
        $validated = $request->validate([
            'course_module_id' => [
                'nullable',
                'integer',
                'exists:course_modules,id',
            ],

            'notes' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ]);

        $placement =
            StudentCoursePlacement::query()
            ->where(
                'student_course_selection_id',
                $selection->id
            )
            ->first();

        if (
            $placement
            &&
            (int) $placement->status === 3
        ) {
            return back()->withErrors([
                'Kesinleştirilmiş bir yerleşim değiştirilemez.',
            ]);
        }

        $moduleId =
            $validated['course_module_id']
            ?? null;

        $placement =
            $placementService->placeSelection(
                $selection,
                $moduleId,
                $validated['notes'] ?? null
            );

        return back()->with(
            'success',
            "{$selection->student->first_name} " .
                "{$selection->student->last_name} için " .
                "{$selection->course->name} yerleştirmesi kaydedildi."
        );
    }

    /**
     * Kesinleştirme:
     *
     * Placement kayıtlarını history'ye aktarır.
     */
    public function confirm(
        Request $request,
        AcademicYear $academicYear
    ) {
        DB::transaction(function () use ($academicYear) {

            $placements =
                StudentCoursePlacement::query()
                ->with([
                    'selection',
                    'student',
                    'course',
                    'moduleGroup',
                    'module',
                ])
                ->where(
                    'academic_year_id',
                    $academicYear->id
                )
                ->where(
                    'status',
                    2
                )
                ->lockForUpdate()
                ->get();

            if ($placements->isEmpty()) {
                abort(
                    422,
                    'Kesinleştirilecek yerleştirme bulunamadı.'
                );
            }

            foreach ($placements as $placement) {

                if (! $placement->course_id) {
                    abort(
                        422,
                        "{$placement->student->first_name} " .
                            "{$placement->student->last_name} için " .
                            "yerleşim dersi belirlenmemiş."
                    );
                }

                if (! $placement->course_module_id) {
                    abort(
                        422,
                        "{$placement->student->first_name} " .
                            "{$placement->student->last_name} / " .
                            "{$placement->course->name} için " .
                            "modül belirlenmemiş."
                    );
                }

                if (! $placement->student_course_group_id) {
                    abort(
                        422,
                        "{$placement->student->first_name} " .
                            "{$placement->student->last_name} / " .
                            "{$placement->course->name} için grup belirlenmemiş."
                    );
                }

                if (! $placement->course_grade_option_id) {
                    abort(
                        422,
                        "{$placement->student->first_name} " .
                            "{$placement->student->last_name} / " .
                            "{$placement->course->name} için " .
                            "ders saati seçeneği belirlenmemiş."
                    );
                }

                if ($placement->weekly_hours === null) {
                    abort(
                        422,
                        "{$placement->student->first_name} " .
                            "{$placement->student->last_name} / " .
                            "{$placement->course->name} için " .
                            "haftalık ders saati belirlenmemiş."
                    );
                }

                $gradeOption =
                    \App\Models\CourseGradeOption::query()
                    ->where(
                        'id',
                        $placement->course_grade_option_id
                    )
                    ->where(
                        'course_id',
                        $placement->course_id
                    )
                    ->first();

                if (! $gradeOption) {
                    abort(
                        422,
                        "{$placement->student->first_name} " .
                            "{$placement->student->last_name} / " .
                            "{$placement->course->name} için " .
                            "geçerli ders saati seçeneği bulunamadı."
                    );
                }

                $history =
                    StudentCourseHistory::query()
                    ->where(
                        'student_id',
                        $placement->student_id
                    )
                    ->where(
                        'academic_year_id',
                        $placement->academic_year_id
                    )
                    ->where(
                        'course_id',
                        $placement->course_id
                    )
                    ->where(
                        'course_module_id',
                        $placement->course_module_id
                    )
                    ->first();

                if ($history) {

                    $history->update([
                        'course_grade_option_id' =>
                        $placement
                            ->course_grade_option_id,

                        'grade' =>
                        $gradeOption->grade,

                        'weekly_hours' =>
                        $placement->weekly_hours,

                        'status' =>
                        2,

                        'notes' =>
                        $placement->notes,
                    ]);
                } else {

                    StudentCourseHistory::create([
                        'student_id' =>
                        $placement->student_id,

                        'academic_year_id' =>
                        $placement->academic_year_id,

                        'course_id' =>
                        $placement->course_id,

                        'course_module_id' =>
                        $placement->course_module_id,

                        'course_grade_option_id' =>
                        $placement
                            ->course_grade_option_id,

                        'grade' =>
                        $gradeOption->grade,

                        'weekly_hours' =>
                        $placement->weekly_hours,

                        'status' =>
                        2,

                        'notes' =>
                        $placement->notes,
                    ]);
                }

                $placement->update([
                    'status' =>
                    3,

                    'confirmed_at' =>
                    now(),
                ]);

                if ($placement->selection) {
                    $placement->selection->update([
                        'status' =>
                        3,
                    ]);
                }
            }

            StudentCourseGroup::query()
                ->where(
                    'academic_year_id',
                    $academicYear->id
                )
                ->whereIn(
                    'status',
                    [1, 2]
                )
                ->whereHas(
                    'placements',
                    function ($query) {
                        $query->where(
                            'status',
                            3
                        );
                    }
                )
                ->update([
                    'status' =>
                    2,

                    'confirmed_at' =>
                    now(),
                ]);
        });

        return back()->with(
            'success',
            "{$academicYear->name} yerleştirmeleri kesinleştirildi."
        );
    }
}
