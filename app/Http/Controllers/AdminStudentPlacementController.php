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

        return view(
            'admin.student-placements.index',
            compact(
                'academicYears',
                'academicYear',
                'rows'
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
                ->where('status', 2)
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
                 * Kesin yerleştirme için modül zorunlu.
                 */
                if (! $placement->course_module_id) {
                    abort(
                        422,
                        "{$placement->student->first_name} " .
                        "{$placement->student->last_name} / " .
                        "{$placement->course->name} için " .
                        "modül belirlenmemiş."
                    );
                }

                /*
                 * Aynı yerleştirme daha önce history'ye
                 * aktarılmışsa tekrar oluşturma.
                 */
                $historyExists =
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
                        ->exists();

                if ($historyExists) {
                    continue;
                }

                $grade =
                    $placement->selection
                        ?->gradeOption
                        ?->grade;

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
                        $placement->course_grade_option_id,

                    'grade' =>
                        $placement
                            ->selection
                            ?->gradeOption
                            ?->grade,

                    'weekly_hours' =>
                        $placement->weekly_hours,

                    /*
                     * Kesinleşmiş geçmiş.
                     *
                     * Mevcut history status yapısını
                     * koruyoruz.
                     */
                    'status' => 2,

                    'notes' =>
                        $placement->notes,
                ]);

                $placement->update([
                    'status' => 3,
                    'confirmed_at' => now(),
                ]);

                /*
                 * Öğrencinin tercihini de kesinleşmiş kabul et.
                 */
                if ($placement->selection) {
                    $placement->selection->update([
                        'status' => 3,
                    ]);
                }
            }
        });

        return back()->with(
            'success',
            "{$academicYear->name} yerleştirmeleri kesinleştirildi."
        );
    }
}