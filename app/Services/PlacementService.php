<?php

namespace App\Services;

use App\Models\CourseModule;
use App\Models\CourseModuleGroup;
use App\Models\Student;
use App\Models\StudentCourseHistory;
use App\Models\StudentCoursePlacement;
use App\Models\StudentCourseSelection;
use Illuminate\Support\Collection;

class PlacementService
{
    /**
     * Bir öğrencinin yaptığı tercihe göre
     * sistem tarafından önerilecek modülü bulur.
     *
     * DİKKAT:
     * Burada sadece geçmişte kesinleşmiş dersler
     * dikkate alınır.
     *
     * Mevcut placement kayıtları history değildir.
     */
    public function suggestedModule(
        Student $student,
        StudentCourseSelection $selection
    ): ?CourseModule {
        $course = $selection->course;

        /*
         * Modüler olmayan derslerde module_group
         * bulunmayabilir.
         *
         * Bu durumda doğrudan null döndürürüz.
         */
        if (! $course->is_modular) {
            return null;
        }

        $group = $selection->moduleGroup;

        if (! $group) {
            return null;
        }

        /*
         * Programın aktif modülleri.
         *
         * Örneğin:
         * Modül 1
         * Modül 2
         * Modül 3
         * Modül 4
         */
        $modules = $group->modules()
            ->where('active', true)
            ->orderBy('module_number')
            ->get();

        if ($modules->isEmpty()) {
            return null;
        }

        /*
         * Öğrencinin bu program/ders için
         * geçmişte kesinleşmiş modülleri.
         *
         * status:
         * 1 = devam ediyor
         * 2 = tamamlandı
         *
         * Mevcut yerleştirme kayıtlarını
         * kesinlikle hesaba katmıyoruz.
         */
        $takenModuleIds = StudentCourseHistory::query()
            ->where('student_id', $student->id)
            ->where('course_id', $course->id)
            ->whereNotNull('course_module_id')
            ->whereIn('status', [1, 2])
            ->pluck('course_module_id')
            ->unique();

        /*
         * İlk alınmamış modül.
         */
        return $modules->first(
            fn (CourseModule $module) =>
                ! $takenModuleIds->contains($module->id)
        );
    }

    /**
     * Bir öğrenci tercihi için placement oluşturur veya günceller.
     *
     * Burada henüz history oluşturulmaz.
     */
    public function placeSelection(
        StudentCourseSelection $selection,
        ?int $moduleId = null,
        ?string $notes = null
    ): StudentCoursePlacement {
        $student = $selection->student;

        /*
         * Manuel modül verilmemişse
         * sistem önerisini kullan.
         */
        if ($moduleId === null) {
            $module = $this->suggestedModule(
                $student,
                $selection
            );

            $moduleId = $module?->id;
        }

        $module = $moduleId
            ? CourseModule::query()
                ->where('id', $moduleId)
                ->where('active', true)
                ->where(
                    'course_id',
                    $selection->course_id
                )
                ->when(
                    $selection->course_module_group_id,
                    fn ($query) =>
                        $query->where(
                            'course_module_group_id',
                            $selection->course_module_group_id
                        )
                )
                ->firstOrFail()
            : null;

        return StudentCoursePlacement::updateOrCreate(
            [
                'student_course_selection_id' =>
                    $selection->id,
            ],
            [
                'student_id' =>
                    $selection->student_id,

                'academic_year_id' =>
                    $selection->academic_year_id,

                'course_id' =>
                    $selection->course_id,

                'course_module_group_id' =>
                    $selection->course_module_group_id,

                'course_module_id' =>
                    $module?->id,

                'weekly_hours' =>
                    $selection->weekly_hours,

                'status' => 2,

                'placed_at' => now(),

                'confirmed_at' => null,

                'notes' => $notes,
            ]
        );
    }

    /**
     * Bir öğrencinin mevcut placement kaydını getirir.
     */
    public function placementForSelection(
        StudentCourseSelection $selection
    ): ?StudentCoursePlacement {
        return StudentCoursePlacement::query()
            ->where(
                'student_course_selection_id',
                $selection->id
            )
            ->first();
    }

    /**
     * Bir eğitim yılındaki bütün öğrenci tercihleri
     * için sistem önerilerini üretir.
     */
    public function suggestions(
        int $academicYearId
    ): Collection {
        $selections = StudentCourseSelection::query()
            ->with([
                'student',
                'course',
                'moduleGroup.modules',
            ])
            ->where(
                'academic_year_id',
                $academicYearId
            )
            ->whereIn('status', [1, 2])
            ->get();

        return $selections->map(
            function (StudentCourseSelection $selection) {
                return [
                    'selection' => $selection,
                    'module' => $this->suggestedModule(
                        $selection->student,
                        $selection
                    ),
                ];
            }
        );
    }
}