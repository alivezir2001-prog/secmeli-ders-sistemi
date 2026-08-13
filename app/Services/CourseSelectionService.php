<?php

namespace App\Services;

use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\Student;
use Illuminate\Support\Collection;

class CourseSelectionService
{
    /**
     * Öğrencinin mevcut eğitim yılında seçebileceği dersleri getirir.
     */
    public function availableCourses(
        Student $student,
        int $academicYearId
    ): Collection {
        $studentYear = $student->studentYears()
            ->where('academic_year_id', $academicYearId)
            ->where('active', true)
            ->first();

        if (! $studentYear) {
            return collect();
        }

        $grade = $studentYear->grade;

        return Course::query()
            ->with([
                'category',
                'gradeOptions' => function ($query) use ($grade) {
                    $query->where('grade', $grade);
                },
            ])
            ->where('active', true)
            ->where('offered', true)
            ->whereHas('gradeOptions', function ($query) use ($grade) {
                $query->where('grade', $grade);
            })
            ->orderBy('course_category_id')
            ->orderBy('name')
            ->get()
            ->filter(function (Course $course) use ($student) {
                return $this->canTakeCourse($student, $course);
            })
            ->values();
    }

    /**
     * Öğrenci bu dersi geçmiş kayıtlarına göre alabilir mi?
     */
    public function canTakeCourse(
        Student $student,
        Course $course
    ): bool {
        $timesTaken = $student->courseHistories()
            ->where('course_id', $course->id)
            ->whereIn('status', [1, 2])
            ->count();

        return $timesTaken < $course->max_selections;
    }

    /**
     * Öğrencinin bu dersi kaç kez aldığını döndürür.
     */
    public function timesTaken(
        Student $student,
        Course $course
    ): int {
        return $student->courseHistories()
            ->where('course_id', $course->id)
            ->whereIn('status', [1, 2])
            ->count();
    }

    /**
     * Öğrencinin bu ders için kalan alma hakkını döndürür.
     */
    public function remainingAttempts(
        Student $student,
        Course $course
    ): int {
        return max(
            0,
            $course->max_selections -
            $this->timesTaken($student, $course)
        );
    }

    /**
     * Öğrencinin yaptığı ders seçimlerini doğrular.
     */
    public function validateSelections(
        Student $student,
        int $academicYearId,
        array $selections
    ): array {
        $errors = [];

        $studentYear = $student->studentYears()
            ->where('academic_year_id', $academicYearId)
            ->where('active', true)
            ->first();

        if (! $studentYear) {
            return [
                'valid' => false,
                'errors' => [
                    'Öğrencinin bu eğitim yılı için sınıf kaydı bulunamadı.',
                ],
                'total_hours' => 0,
            ];
        }

        $grade = $studentYear->grade;

        if (empty($selections)) {
            return [
                'valid' => false,
                'errors' => [
                    'En az bir ders seçmelisiniz.',
                ],
                'total_hours' => 0,
            ];
        }

        $courseIds = collect($selections)
            ->pluck('course_id');

        if ($courseIds->duplicates()->isNotEmpty()) {
            $errors[] = 'Aynı ders birden fazla kez seçilemez.';
        }

        $courses = Course::query()
            ->with([
                'category',
                'gradeOptions' => function ($query) use ($grade) {
                    $query->where('grade', $grade);
                },
            ])
            ->whereIn('id', $courseIds)
            ->get()
            ->keyBy('id');

        $selectedCategories = collect();

        $totalHours = 0;

        foreach ($selections as $selection) {
            $courseId = (int) ($selection['course_id'] ?? 0);
            $optionId = (int) ($selection['course_grade_option_id'] ?? 0);

            $course = $courses->get($courseId);

            if (! $course) {
                $errors[] = 'Seçilen derslerden biri bulunamadı.';
                continue;
            }

            if (! $course->offered || ! $course->active) {
                $errors[] =
                    "{$course->name} bu eğitim yılında sunulmuyor.";
                continue;
            }

            $categoryId = $course->course_category_id;

            /*
            * Aynı gruptan yalnızca bir ders seçilebilir.
            */
            if ($selectedCategories->contains($categoryId)) {
                $categoryName = $course->category?->name ?? 'Bu grup';

                $errors[] =
                    "{$categoryName} grubundan yalnızca bir ders seçebilirsiniz.";

                continue;
            }

            $selectedCategories->push($categoryId);

            if (! $this->canTakeCourse($student, $course)) {
                $errors[] =
                    "{$course->name} dersini maksimum alma sayısına " .
                    "ulaştığınız için seçemezsiniz.";
                continue;
            }

            $option = $course->gradeOptions
                ->firstWhere('id', $optionId);

            if (! $option) {
                $errors[] =
                    "{$course->name} için seçilen ders saati " .
                    "bu sınıf için geçerli değil.";
                continue;
            }


            $totalHours += $option->weekly_hours;
        }

        /*
         * 5, 6 ve 7. sınıflar: 5 saat
         * 8. sınıf: 6 saat
         */
        $requiredHours = $grade === 8 ? 6 : 5;

        if ($totalHours !== $requiredHours) {
            $errors[] =
                "{$grade}. sınıf öğrencileri toplam {$requiredHours} " .
                "saat seçmelidir. Şu anda {$totalHours} saat seçtiniz.";
        }

        /*
         * Üç ders grubunun her birinden seçim yapılmalı.
         */
        $categoryIds = CourseCategory::query()
            ->where('active', true)
            ->pluck('id');

        foreach ($categoryIds as $categoryId) {
            if (! $selectedCategories->contains($categoryId)) {
                $category = CourseCategory::find($categoryId);

                if ($category) {
                    $errors[] =
                        "{$category->name} grubundan en az bir ders " .
                        "seçmelisiniz.";
                }
            }
        }

        return [
            'valid' => empty($errors),
            'errors' => array_values(array_unique($errors)),
            'total_hours' => $totalHours,
        ];
    }

    /**
 * Öğrencinin tercihlerini doğrular ve kaydeder.
 */
public function saveSelections(
    Student $student,
    int $academicYearId,
    array $selections
): array {
    $validation = $this->validateSelections(
        $student,
        $academicYearId,
        $selections
    );

    if (! $validation['valid']) {
        return $validation;
    }

    return \DB::transaction(function () use (
        $student,
        $academicYearId,
        $selections,
        $validation
    ) {
        /*
         * Bu eğitim yılına ait eski taslak/gönderilmiş
         * tercihleri temizliyoruz.
         *
         * Geçmiş ders kayıtlarına dokunmuyoruz.
         */
        \App\Models\StudentCourseSelection::query()
            ->where('student_id', $student->id)
            ->where('academic_year_id', $academicYearId)
            ->whereIn('status', [1, 2])
            ->delete();

        foreach ($selections as $index => $selection) {
            \App\Models\StudentCourseSelection::create([
                'student_id' => $student->id,
                'academic_year_id' => $academicYearId,
                'course_id' => $selection['course_id'],
                'course_grade_option_id' =>
                    $selection['course_grade_option_id'],
                'preference_order' => $index + 1,
                'status' => 2,
                'submitted_at' => now(),
            ]);
        }

        return [
            'valid' => true,
            'errors' => [],
            'total_hours' => $validation['total_hours'],
            'saved' => true,
            'selection_count' => count($selections),
        ];
    });
}
}