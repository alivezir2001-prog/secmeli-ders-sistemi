<?php

namespace App\Services;

use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\CourseModuleGroup;
use App\Models\Student;
use App\Models\StudentCourseSelection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CourseSelectionService
{
    /**
     * Öğrencinin kendi sınıfında tercih edebileceği dersleri getirir.
     *
     * SINIF burada sadece ders/saat uygunluğunu belirlemek içindir.
     * Yerleştirme ve grup oluşturma sınıf bazlı değildir.
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

        $grade = (int) $studentYear->grade;

        return Course::query()
            ->with([
                'category',

                'gradeOptions' => function ($query) use ($grade) {
                    $query
                        ->where('active', true)
                        ->where('grade', $grade)
                        ->orderBy('weekly_hours');
                },

                'moduleGroups' => function ($query) {
                    $query
                        ->where('active', true)
                        ->orderBy('name');
                },
            ])
            ->where('active', true)
            ->where('offered', true)
            ->whereHas('gradeOptions', function ($query) use ($grade) {
                $query
                    ->where('active', true)
                    ->where('grade', $grade);
            })
            ->orderBy('course_category_id')
            ->orderBy('name')
            ->get()
            ->filter(function (Course $course) use ($student) {
                return $this->canTakeCourse(
                    $student,
                    $course
                );
            })
            ->values();
    }

    /**
     * Öğrenci bu dersi geçmişine göre alabilir mi?
     */
    public function canTakeCourse(
        Student $student,
        Course $course
    ): bool {
        return $this->timesTaken(
            $student,
            $course
        ) < (int) $course->max_selections;
    }

    /**
     * Öğrencinin bu dersi geçmişte kaç kez aldığı.
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
     * Kalan alma hakkı.
     */
    public function remainingAttempts(
        Student $student,
        Course $course
    ): int {
        return max(
            0,
            (int) $course->max_selections
            - $this->timesTaken($student, $course)
        );
    }

    /**
     * Bir ders için öğrencinin seçebileceği programı bulur.
     *
     * Modüler olmayan derslerde null.
     *
     * Tek aktif program varsa otomatik olarak o program.
     *
     * Birden fazla aktif program varsa program_id zorunludur.
     */
    public function resolveModuleGroupForSelection(
        Course $course,
        ?int $moduleGroupId
    ): ?CourseModuleGroup {
        if (! $course->is_modular) {
            return null;
        }

        $groups = $course->moduleGroups()
            ->where('active', true)
            ->orderBy('name')
            ->get();

        if ($groups->isEmpty()) {
            return null;
        }

        if ($moduleGroupId !== null) {
            return $groups->firstWhere(
                'id',
                (int) $moduleGroupId
            );
        }

        if ($groups->count() === 1) {
            return $groups->first();
        }

        return null;
    }

    /**
     * Öğrencinin tercihlerini doğrular.
     *
     * Her kategoride:
     *
     * 1 = Ders + Program + Saat
     * 2 = Ders + Program
     * 3 = Ders + Program
     *
     * Toplam saat yalnızca 1. tercihlerden hesaplanır.
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
                'resolved_selections' => [],
            ];
        }

        $grade = (int) $studentYear->grade;

        if (empty($selections)) {
            return [
                'valid' => false,
                'errors' => [
                    'En az bir tercih yapmalısınız.',
                ],
                'total_hours' => 0,
                'resolved_selections' => [],
            ];
        }

        /*
         * Girişteki kayıtları normalize ediyoruz.
         */
        $normalized = collect($selections)
            ->map(function ($selection) {
                return [
                    'course_id' =>
                        (int) ($selection['course_id'] ?? 0),

                    'course_module_group_id' =>
                        ! empty($selection['course_module_group_id'])
                            ? (int) $selection['course_module_group_id']
                            : null,

                    'course_grade_option_id' =>
                        ! empty($selection['course_grade_option_id'])
                            ? (int) $selection['course_grade_option_id']
                            : null,

                    'preference_order' =>
                        (int) ($selection['preference_order'] ?? 0),
                ];
            })
            ->values();

        /*
         * Geçersiz sıra değerleri.
         */
        foreach ($normalized as $selection) {
            if (! in_array(
                $selection['preference_order'],
                [1, 2, 3],
                true
            )) {
                $errors[] =
                    'Tercih sırası yalnızca 1, 2 veya 3 olabilir.';
            }
        }

        /*
         * Dersleri tek sorguda alıyoruz.
         */
        $courseIds = $normalized
            ->pluck('course_id')
            ->filter(fn ($id) => $id > 0)
            ->unique()
            ->values();

        $courses = Course::query()
            ->with([
                'category',

                'gradeOptions' => function ($query) use ($grade) {
                    $query
                        ->where('active', true)
                        ->where('grade', $grade)
                        ->orderBy('weekly_hours');
                },

                'moduleGroups' => function ($query) {
                    $query
                        ->where('active', true)
                        ->orderBy('name');
                },
            ])
            ->whereIn('id', $courseIds)
            ->get()
            ->keyBy('id');

        /*
         * Aynı kategori + aynı sıra bir kez kullanılabilir.
         */
        $categoryOrders = [];

        /*
         * Aynı kategori + aynı ders tekrar edemez.
         */
        $categoryCourses = [];

        /*
         * Aynı ders tekrar edemez.
         */
        $courseAlreadySelected = [];

        $resolvedSelections = [];

        $firstPreferenceHours = 0;

        foreach ($normalized as $selection) {
            $courseId = $selection['course_id'];
            $preferenceOrder = $selection['preference_order'];

            $course = $courses->get($courseId);

            if (! $course) {
                $errors[] =
                    'Seçilen derslerden biri bulunamadı.';

                continue;
            }

            if (! $course->active || ! $course->offered) {
                $errors[] =
                    "{$course->name} bu eğitim yılında sunulmuyor.";

                continue;
            }

            $categoryId =
                (int) $course->course_category_id;

            /*
             * Aynı ders iki kez seçilemez.
             */
            if (isset($courseAlreadySelected[$courseId])) {
                $errors[] =
                    "{$course->name} birden fazla kez seçilemez.";

                continue;
            }

            $courseAlreadySelected[$courseId] = true;

            /*
             * Aynı kategoride aynı sıra kullanılamaz.
             */
            $categoryOrders[$categoryId] ??= [];

            if (
                isset(
                    $categoryOrders[$categoryId][$preferenceOrder]
                )
            ) {
                $errors[] =
                    "{$course->category?->name} grubunda " .
                    "{$preferenceOrder}. tercih sırası " .
                    "birden fazla kullanılamaz.";

                continue;
            }

            $categoryOrders[$categoryId][$preferenceOrder] =
                true;

            /*
             * Aynı kategori içindeki dersleri takip et.
             */
            $categoryCourses[$categoryId] ??= [];

            $categoryCourses[$categoryId][] =
                $courseId;

            /*
             * 1. tercih için:
             * Saat zorunlu.
             */
            $gradeOption = null;

            if ($preferenceOrder === 1) {
                $optionId =
                    $selection['course_grade_option_id'];

                if (! $optionId) {
                    $errors[] =
                        "{$course->name} için 1. tercih saatini " .
                        "seçmelisiniz.";

                    continue;
                }

                $gradeOption =
                    $course->gradeOptions
                        ->firstWhere('id', $optionId);

                if (! $gradeOption) {
                    $errors[] =
                        "{$course->name} için seçilen ders saati " .
                        "bu öğrencinin sınıfı için geçerli değil.";

                    continue;
                }
            } else {
                /*
                 * 2. ve 3. tercihlerde saat yok.
                 *
                 * Varsa gelen saat/grade option verisini
                 * yok sayıyoruz.
                 */
                $gradeOption = null;
            }

            /*
             * Program çözümü.
             */
            $moduleGroup =
                $this->resolveModuleGroupForSelection(
                    $course,
                    $selection['course_module_group_id']
                );

            if (
                $course->is_modular
                &&
                ! $moduleGroup
            ) {
                $activeGroupCount =
                    $course->moduleGroups()
                        ->where('active', true)
                        ->count();

                if ($activeGroupCount > 1) {
                    $errors[] =
                        "{$course->name} için program / alan seçmelisiniz.";
                } else {
                    $errors[] =
                        "{$course->name} için aktif program bulunamadı.";
                }

                continue;
            }

            /*
             * 1. tercihin saati toplam saate girer.
             */
            $weeklyHours = null;

            if ($gradeOption) {
                $weeklyHours =
                    (int) $gradeOption->weekly_hours;

                $firstPreferenceHours +=
                    $weeklyHours;
            }

            $resolvedSelections[] = [
                'course' => $course,

                'module_group' => $moduleGroup,

                'grade_option' => $gradeOption,

                'course_id' => $course->id,

                'course_module_group_id' =>
                    $moduleGroup?->id,

                'course_grade_option_id' =>
                    $gradeOption?->id,

                'weekly_hours' =>
                    $weeklyHours,

                'preference_order' =>
                    $preferenceOrder,

                'category_id' =>
                    $categoryId,
            ];
        }

        /*
         * Her aktif kategoride 1. tercih olmak zorunda.
         */
        $requiredCategories = CourseCategory::query()
            ->where('active', true)
            ->orderBy('id')
            ->get();

        foreach ($requiredCategories as $category) {
            $orders =
                $categoryOrders[$category->id] ?? [];

            if (! isset($orders[1])) {
                $errors[] =
                    "{$category->name} grubunda 1. tercih yapmalısınız.";
            }
        }

        /*
         * 2. tercih varsa 1. tercih de olmalı.
         * 3. tercih varsa 2. tercih de olmalı.
         */
        foreach ($categoryOrders as $categoryId => $orders) {
            if (isset($orders[3]) && ! isset($orders[2])) {
                $category =
                    $requiredCategories->firstWhere(
                        'id',
                        $categoryId
                    );

                if ($category) {
                    $errors[] =
                        "{$category->name} grubunda 3. tercih "
                        . "yaptıysanız 2. tercihi de yapmalısınız.";
                }
            }
        }

        /*
         * 5-7. sınıf = 5 saat
         * 8. sınıf = 6 saat
         *
         * SADECE 1. TERCİHLER HESAPLANIR.
         */
        $requiredHours =
            $grade === 8 ? 6 : 5;

        if ($firstPreferenceHours !== $requiredHours) {
            $errors[] =
                "{$grade}. sınıf öğrencisinin 1. tercihleri " .
                "toplam {$requiredHours} saat olmalıdır. " .
                "Şu anda {$firstPreferenceHours} saat seçildi.";
        }

        return [
            'valid' =>
                empty($errors),

            'errors' =>
                array_values(
                    array_unique($errors)
                ),

            'total_hours' =>
                $firstPreferenceHours,

            'resolved_selections' =>
                $resolvedSelections,
        ];
    }

    /**
     * Doğrulanmış öğrenci tercihlerini kaydeder.
     *
     * 1. tercih:
     * ders + program + saat
     *
     * 2/3. tercih:
     * ders + program
     *
     * Modül burada kesinlikle oluşturulmaz.
     */
    public function saveSelections(
        Student $student,
        int $academicYearId,
        array $selections
    ): array {
        $validation =
            $this->validateSelections(
                $student,
                $academicYearId,
                $selections
            );

        if (! $validation['valid']) {
            return $validation;
        }

        return DB::transaction(
            function () use (
                $student,
                $academicYearId,
                $validation
            ) {
                /*
                 * Aynı eğitim yılına ait önceki
                 * öğrenci tercihlerini temizle.
                 *
                 * History'ye dokunulmaz.
                 */
                StudentCourseSelection::query()
                    ->where('student_id', $student->id)
                    ->where(
                        'academic_year_id',
                        $academicYearId
                    )
                    ->whereIn('status', [1, 2])
                    ->delete();

                foreach (
                    $validation['resolved_selections']
                    as $selection
                ) {
                    StudentCourseSelection::create([
                        'student_id' =>
                            $student->id,

                        'academic_year_id' =>
                            $academicYearId,

                        'course_id' =>
                            $selection['course_id'],

                        'course_module_group_id' =>
                            $selection['course_module_group_id'],

                        /*
                         * Yalnızca 1. tercih saat taşır.
                         */
                        'course_grade_option_id' =>
                            $selection['course_grade_option_id'],

                        'weekly_hours' =>
                            $selection['weekly_hours'],

                        'preference_order' =>
                            $selection['preference_order'],

                        'status' =>
                            2,

                        'submitted_at' =>
                            now(),

                        'notes' =>
                            null,
                    ]);
                }

                return [
                    'valid' => true,

                    'errors' => [],

                    'total_hours' =>
                        $validation['total_hours'],

                    'saved' => true,

                    'selection_count' =>
                        count(
                            $validation[
                                'resolved_selections'
                            ]
                        ),
                ];
            }
        );
    }
}