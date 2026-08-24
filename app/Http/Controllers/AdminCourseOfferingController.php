<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Course;
use App\Models\CourseOffering;
use App\Models\StudentCourseSelection;
use Illuminate\Http\Request;

class AdminCourseOfferingController extends Controller
{
    public function index(Request $request)
    {
        $academicYears = AcademicYear::orderByDesc('name')->get();

        $academicYear = AcademicYear::where(
            'id',
            $request->input(
                'academic_year_id',
                AcademicYear::where('active', true)->value('id')
            )
        )->firstOrFail();

        $courses = Course::query()
            ->with([
                'category',

                'moduleGroups' => function ($query) {
                    $query
                        ->where('active', true)
                        ->with([
                            'modules' => function ($moduleQuery) {
                                $moduleQuery
                                    ->where('active', true)
                                    ->with([
                                        'hourOptions' => function ($hourQuery) {
                                            $hourQuery
                                                ->where('active', true)
                                                ->orderBy('weekly_hours');
                                        },
                                    ])
                                    ->orderBy('module_number');
                            },
                        ])
                        ->orderBy('name');
                },

                'offerings' => function ($query) use ($academicYear) {
                    $query->where(
                        'academic_year_id',
                        $academicYear->id
                    );
                },
            ])
            ->where('active', true)
            ->where('offered', true)
            ->orderBy('course_category_id')
            ->orderBy('name')
            ->get();

        /*
         * Öğrenci sayısı artık sınıfa göre değil,
         * doğrudan modül + haftalık saat varyantına göre hesaplanır.
         */
        $selectionCounts = StudentCourseSelection::query()
            ->selectRaw(
                'course_module_id, weekly_hours, COUNT(*) as total'
            )
            ->where(
                'academic_year_id',
                $academicYear->id
            )
            ->where('status', 2)
            ->whereNotNull('course_module_id')
            ->whereNotNull('weekly_hours')
            ->groupBy(
                'course_module_id',
                'weekly_hours'
            )
            ->get()
            ->keyBy(function ($row) {
                return $row->course_module_id . ':' .
                    $row->weekly_hours;
            });

        return view(
            'admin.course-offerings.index',
            compact(
                'academicYears',
                'academicYear',
                'courses',
                'selectionCounts'
            )
        );
    }

    public function update(
        Request $request,
        Course $course
    ) {
        $validated = $request->validate([
            'academic_year_id' => [
                'required',
                'exists:academic_years,id',
            ],

            'course_module_id' => [
                'required',
                'exists:course_modules,id',
            ],

            'weekly_hours' => [
                'required',
                'integer',
                'min:1',
                'max:10',
            ],

            'maximum_students' => [
                'nullable',
                'integer',
                'min:1',
            ],

            'allow_multiple_classes' => [
                'nullable',
                'boolean',
            ],

            'maximum_classes' => [
                'required',
                'integer',
                'min:1',
                'max:20',
            ],

            'active' => [
                'nullable',
                'boolean',
            ],
        ]);

        $module = $course->modules()
            ->where('id', $validated['course_module_id'])
            ->where('active', true)
            ->firstOrFail();

        /*
         * Haftalık saat gerçekten bu modül için
         * tanımlanmış mı?
         */
        $hourOptionExists = $module->hourOptions()
            ->where('weekly_hours', $validated['weekly_hours'])
            ->where('active', true)
            ->exists();

        abort_unless($hourOptionExists, 422);

        $academicYear = AcademicYear::findOrFail(
            $validated['academic_year_id']
        );

        $allowMultipleClasses = $request->boolean(
            'allow_multiple_classes'
        );

        $maximumClasses =
            (int) $validated['maximum_classes'];

        if (! $allowMultipleClasses) {
            $maximumClasses = 1;
        }

        CourseOffering::updateOrCreate(
            [
                'academic_year_id' => $academicYear->id,
                'course_module_id' => $module->id,
                'weekly_hours' =>
                    (int) $validated['weekly_hours'],
            ],
            [
                'course_id' => $course->id,

                /*
                 * Sistem kuralı:
                 * Minimum 10 öğrenci.
                 */
                'minimum_students' => 10,

                'maximum_students' =>
                    $validated['maximum_students'] ?? null,

                'allow_multiple_classes' =>
                    $allowMultipleClasses,

                'maximum_classes' =>
                    $maximumClasses,

                'active' =>
                    $request->boolean('active'),
            ]
        );

        return redirect()
            ->route(
                'admin.course-offerings.index',
                [
                    'academic_year_id' =>
                        $academicYear->id,
                ]
            )
            ->with(
                'success',
                "{$course->name} / {$module->name} / " .
                "{$validated['weekly_hours']} saat " .
                "kontenjan ayarı kaydedildi."
            );
    }
}