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
                'offerings' => function ($query) use ($academicYear) {
                    $query->where('academic_year_id', $academicYear->id);
                },
            ])
            ->where('active', true)
            ->where('offered', true)
            ->orderBy('course_category_id')
            ->orderBy('name')
            ->get();

        $selectionCounts = StudentCourseSelection::query()
            ->selectRaw('course_id, COUNT(*) as total')
            ->where('academic_year_id', $academicYear->id)
            ->where('status', 2)
            ->groupBy('course_id')
            ->pluck('total', 'course_id');

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
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'maximum_students' => ['nullable', 'integer', 'min:1'],
            'allow_multiple_classes' => ['nullable', 'boolean'],
            'maximum_classes' => ['required', 'integer', 'min:1', 'max:20'],
            'active' => ['nullable', 'boolean'],
        ]);

        $academicYear = AcademicYear::findOrFail(
            $validated['academic_year_id']
        );

        $maximumStudents = $validated['maximum_students'] ?? null;
        $maximumClasses = (int) $validated['maximum_classes'];
        $allowMultipleClasses = $request->boolean(
            'allow_multiple_classes'
        );
        $active = $request->boolean('active');

        if (! $allowMultipleClasses) {
            $maximumClasses = 1;
        }

        CourseOffering::updateOrCreate(
            [
                'academic_year_id' => $academicYear->id,
                'course_id' => $course->id,
            ],
            [
                /*
                 * Sistem kuralı: minimum 10.
                 * Kullanıcıdan gelen minimum değeri hiçbir zaman kullanmıyoruz.
                 */
                'minimum_students' => 10,
                'maximum_students' => $maximumStudents,
                'allow_multiple_classes' => $allowMultipleClasses,
                'maximum_classes' => $maximumClasses,
                'active' => $active,
            ]
        );

        return redirect()
            ->route(
                'admin.course-offerings.index',
                ['academic_year_id' => $academicYear->id]
            )
            ->with(
                'success',
                "{$course->name} kontenjan ayarları kaydedildi."
            );
    }
}