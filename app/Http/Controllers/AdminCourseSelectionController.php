<?php

namespace App\Http\Controllers;

use App\Exports\CourseSelectionExport;
use App\Exports\StudentCourseSummaryExport;
use App\Models\AcademicYear;
use App\Models\Course;
use App\Models\StudentCourseSelection;
use App\Models\StudentYear;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class AdminCourseSelectionController extends Controller
{
    public function index(Request $request)
    {
        $data = $this->getSelectionData($request);

        return view(
            'admin.course-selections.index',
            $data
        );
    }

    public function export(Request $request)
    {
        $data = $this->getSelectionData($request);

        return Excel::download(
            new CourseSelectionExport($data['filteredSelections']),
            'secmeli-ders-tercihleri-' .
            $data['academicYear']->name .
            '.xlsx'
        );
    }

    public function exportSummary(Request $request)
    {
        $data = $this->getSelectionData($request);

        return Excel::download(
            new StudentCourseSummaryExport(
                $data['selectionGroups']
            ),
            'secmeli-ders-ogrenci-ozet-' .
            $data['academicYear']->name .
            '.xlsx'
        );
    }

    private function getSelectionData(Request $request): array
    {
        $academicYears = AcademicYear::orderByDesc('name')->get();

        $activeAcademicYearId = AcademicYear::where(
            'active',
            true
        )->value('id');

        $academicYear = AcademicYear::where(
            'id',
            $request->input(
                'academic_year_id',
                $activeAcademicYearId
            )
        )->firstOrFail();

        $search = trim(
            (string) $request->input('search', '')
        );

        $grade = $request->input('grade');
        $section = $request->input('section');
        $courseId = $request->input('course_id');

        $query = StudentCourseSelection::query()
            ->with([
                'student.studentYears' => function ($query) use (
                    $academicYear
                ) {
                    $query
                        ->where(
                            'academic_year_id',
                            $academicYear->id
                        )
                        ->where('active', true);
                },
                'course.category',
                'gradeOption',
            ])
            ->where(
                'academic_year_id',
                $academicYear->id
            )
            ->where('status', 2);

        if ($search !== '') {
            $query->whereHas(
                'student',
                function ($studentQuery) use ($search) {
                    $studentQuery
                        ->where(
                            'student_number',
                            'like',
                            "%{$search}%"
                        )
                        ->orWhere(
                            'first_name',
                            'like',
                            "%{$search}%"
                        )
                        ->orWhere(
                            'last_name',
                            'like',
                            "%{$search}%"
                        );
                }
            );
        }

        if ($grade !== null && $grade !== '') {
            $query->whereHas(
                'student.studentYears',
                function ($studentYearQuery) use (
                    $academicYear,
                    $grade
                ) {
                    $studentYearQuery
                        ->where(
                            'academic_year_id',
                            $academicYear->id
                        )
                        ->where('active', true)
                        ->where('grade', $grade);
                }
            );
        }

        if ($section !== null && $section !== '') {
            $query->whereHas(
                'student.studentYears',
                function ($studentYearQuery) use (
                    $academicYear,
                    $section
                ) {
                    $studentYearQuery
                        ->where(
                            'academic_year_id',
                            $academicYear->id
                        )
                        ->where('active', true)
                        ->where('section', $section);
                }
            );
        }

        if ($courseId !== null && $courseId !== '') {
            $query->where(
                'course_id',
                $courseId
            );
        }

        $filteredSelections = $query
            ->orderBy('student_id')
            ->orderBy('preference_order')
            ->get();

        $selectionGroups = $filteredSelections
            ->groupBy('student_id');

        $studentYears = StudentYear::query()
            ->where(
                'academic_year_id',
                $academicYear->id
            )
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

        $courses = Course::query()
            ->where('active', true)
            ->orderBy('course_category_id')
            ->orderBy('name')
            ->get();

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

        return [
            'academicYears' => $academicYears,
            'academicYear' => $academicYear,
            'selectionGroups' => $selectionGroups,
            'filteredSelections' => $filteredSelections,
            'grades' => $grades,
            'sections' => $sections,
            'courses' => $courses,
            'courseCounts' => $courseCounts,
            'search' => $search,
            'grade' => $grade,
            'section' => $section,
            'courseId' => $courseId,
        ];
    }
}