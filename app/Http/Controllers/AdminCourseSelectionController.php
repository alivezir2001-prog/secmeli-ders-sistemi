<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Course;
use App\Models\StudentCourseSelection;
use Illuminate\Http\Request;

class AdminCourseSelectionController extends Controller
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

        $search = trim((string) $request->input('search', ''));
        $grade = $request->input('grade');
        $section = $request->input('section');
        $courseId = $request->input('course_id');

        $query = StudentCourseSelection::query()
            ->with([
                'student.studentYears' => function ($query) use ($academicYear) {
                    $query
                        ->where('academic_year_id', $academicYear->id)
                        ->where('active', true);
                },
                'course.category',
                'gradeOption',
            ])
            ->where('academic_year_id', $academicYear->id)
            ->where('status', 2);

        if ($search !== '') {
            $query->whereHas('student', function ($studentQuery) use ($search) {
                $studentQuery
                    ->where('student_number', 'like', "%{$search}%")
                    ->orWhere('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%");
            });
        }

        if ($grade !== null && $grade !== '') {
            $query->whereHas('student.studentYears', function ($studentYearQuery) use ($academicYear, $grade) {
                $studentYearQuery
                    ->where('academic_year_id', $academicYear->id)
                    ->where('active', true)
                    ->where('grade', $grade);
            });
        }

        if ($section !== null && $section !== '') {
            $query->whereHas('student.studentYears', function ($studentYearQuery) use ($academicYear, $section) {
                $studentYearQuery
                    ->where('academic_year_id', $academicYear->id)
                    ->where('active', true)
                    ->where('section', $section);
            });
        }

        if ($courseId !== null && $courseId !== '') {
            $query->where('course_id', $courseId);
        }

        $selectionGroups = $query
            ->orderBy('student_id')
            ->orderBy('preference_order')
            ->get()
            ->groupBy('student_id');

        /*
         * Filtrelerde kullanılacak sınıf ve şube seçenekleri.
         */
        $studentYears = \App\Models\StudentYear::query()
            ->where('academic_year_id', $academicYear->id)
            ->where('active', true)
            ->orderBy('grade')
            ->orderBy('section')
            ->get();

        $grades = $studentYears
            ->pluck('grade')
            ->unique()
            ->sort()
            ->values();

        $sections = $studentYears
            ->pluck('section')
            ->filter()
            ->unique()
            ->sort()
            ->values();

        /*
         * Ders filtresi.
         */
        $courses = Course::query()
            ->where('active', true)
            ->orderBy('course_category_id')
            ->orderBy('name')
            ->get();

        /*
         * Filtrelenmiş sonuçlar üzerinden ders dağılımı.
         */
        $filteredSelections = $selectionGroups->flatten(1);

        $courseCounts = $filteredSelections
            ->groupBy('course_id')
            ->map(function ($selections) {
                $first = $selections->first();

                return [
                    'name' => $first->course->name,
                    'category' => $first->course->category->name,
                    'count' => $selections->count(),
                ];
            })
            ->sortByDesc('count')
            ->values();

        return view(
            'admin.course-selections.index',
            compact(
                'academicYears',
                'academicYear',
                'selectionGroups',
                'grades',
                'sections',
                'courses',
                'courseCounts',
                'search',
                'grade',
                'section',
                'courseId'
            )
        );
    }
}