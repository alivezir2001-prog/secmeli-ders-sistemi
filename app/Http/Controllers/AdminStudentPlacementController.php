<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\CourseModule;
use App\Models\StudentCoursePlacement;
use App\Models\StudentCourseSelection;
use App\Services\PlacementService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminStudentPlacementController extends Controller
{
    public function index(
        Request $request,
        PlacementService $placementService
    ) {
        $academicYears = AcademicYear::orderByDesc('name')->get();

        $academicYear = AcademicYear::where(
            'id',
            $request->input(
                'academic_year_id',
                AcademicYear::where('active', true)->value('id')
            )
        )->firstOrFail();

        $selections = StudentCourseSelection::query()
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
            ->whereIn('status', [1, 2])
            ->orderBy('student_id')
            ->orderBy('course_id')
            ->get();

        $placements = StudentCoursePlacement::query()
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
            ->get()
            ->keyBy('student_course_selection_id');

        $rows = $selections->map(
            function (StudentCourseSelection $selection) use (
                $placementService,
                $placements
            ) {
                $placement =
                    $placements->get($selection->id);

                $suggestedModule =
                    $placement?->module
                    ??
                    $placementService->suggestedModule(
                        $selection->student,
                        $selection
                    );

                return [
                    'selection' => $selection,
                    'placement' => $placement,
                    'suggestedModule' => $suggestedModule,
                ];
            }
        );

        $requiredPlacementCount =
            $selections
            ->where('preference_order', 1)
            ->count();

        $placedCategoryKeys =
            $placements
            ->filter(
                fn($placement) =>
                (int) $placement->status === 2
            )
            ->map(
                function ($placement) {
                    $categoryId =
                        $placement
                        ->selection
                        ?->course
                        ?->course_category_id
                        ??
                        $placement
                        ->course
                        ?->course_category_id;

                    return
                        $placement->student_id
                        . ':'
                        . $categoryId;
                }
            )
            ->unique()
            ->count();

        $confirmReady =
            $requiredPlacementCount > 0
            &&
            $placedCategoryKeys === $requiredPlacementCount;

        return view(
            'admin.student-placements.index',
            compact(
                'academicYears',
                'academicYear',
                'rows',
                'confirmReady',
                'requiredPlacementCount',
                'placedCategoryKeys'
            )
        );
    }

    /**
     * Öğrencinin tercihine göre placement oluşturur/günceller.
     *
     * Burada history oluşturulmaz.
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

        $moduleId =
            $validated['course_module_id']
            ?? null;

        /*
         * Eğer okul manuel modül seçmediyse
         * sistem önerisini kullanır.
         */
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
     *
     * Bu işlem yalnızca placement'ın tamamı
     * doğrulandıktan sonra yapılır.
     */
    public function confirm(
        Request $request,
        AcademicYear $academicYear
    ) {
        DB::transaction(function () use ($academicYear) {

            $placements = StudentCoursePlacement::query()
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

                /*
             * Kesinleştirme için gerçek yerleşimin
             * temel alanları mutlaka dolu olmalı.
             */
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

                /*
             * History'deki sınıf bilgisi placement'ın
             * gerçek course_grade_option_id'sinden alınır.
             *
             * Burada artık selection->gradeOption kullanılmaz.
             */
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

                /*
             * Aynı öğrencinin bu eğitim yılında aynı
             * gerçek ders + modül geçmişi zaten varsa
             * ikinci kez history oluşturma.
             */
                $history =
                    \App\Models\StudentCourseHistory::query()
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

                    /*
                 * Aynı yerleşimin tekrar kesinleştirilmesi
                 * durumunda mevcut history'yi güncelle.
                 *
                 * Böylece duplicate history oluşmaz.
                 */
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

                    \App\Models\StudentCourseHistory::create([
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

                /*
             * Placement artık kesinleşmiş.
             */
                $placement->update([
                    'status' =>
                    3,

                    'confirmed_at' =>
                    now(),
                ]);

                /*
             * Placement hangi selection'dan geldiyse
             * onu da kesinleşmiş kabul ediyoruz.
             */
                if ($placement->selection) {
                    $placement->selection->update([
                        'status' =>
                        3,
                    ]);
                }
            }

            /*
         * Kesinleşmiş gruplar artık aktif/kesinleşmiş
         * durumda tutulur.
         *
         * Öğrencisi olmayan taslak gruplara dokunmuyoruz.
         */
            \App\Models\StudentCourseGroup::query()
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
