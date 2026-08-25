<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\StudentCourseGroup;
use App\Models\StudentCoursePlacement;
use App\Services\GroupGenerationService;
use App\Services\GroupManagementService;
use Illuminate\Http\Request;
use RuntimeException;

class AdminStudentCourseGroupController extends Controller
{
    public function index(Request $request)
    {
        $academicYears =
            AcademicYear::orderByDesc('name')->get();

        $academicYear = AcademicYear::where(
            'id',
            $request->input(
                'academic_year_id',
                AcademicYear::where(
                    'active',
                    true
                )->value('id')
            )
        )->firstOrFail();

        $groups = StudentCourseGroup::query()
            ->with([
                'course.category',
                'moduleGroup',
                'module',
                'placements.student',
                'placements.selection.course',
            ])
            ->where(
                'academic_year_id',
                $academicYear->id
            )
            ->orderBy('course_id')
            ->orderBy('course_module_group_id')
            ->orderBy('course_module_id')
            ->orderBy('weekly_hours')
            ->orderBy('group_number')
            ->get();

        /*
         * Manuel taşıma ekranında kullanılabilecek
         * aktif gruplar da Blade'e gönderiliyor.
         */
        $activeGroups = StudentCourseGroup::query()
            ->with([
                'course',
                'moduleGroup',
                'module',
            ])
            ->where(
                'academic_year_id',
                $academicYear->id
            )
            ->whereIn(
                'status',
                [1, 2]
            )
            ->orderBy('course_id')
            ->orderBy('course_module_group_id')
            ->orderBy('course_module_id')
            ->orderBy('weekly_hours')
            ->orderBy('group_number')
            ->get();

        return view(
            'admin.student-course-groups.index',
            compact(
                'academicYears',
                'academicYear',
                'groups',
                'activeGroups'
            )
        );
    }

    /**
     * Öğrenci tercihlerinden otomatik grupları oluşturur.
     */
    public function generate(
        Request $request,
        GroupGenerationService $service
    ) {
        $validated = $request->validate([
            'academic_year_id' => [
                'required',
                'exists:academic_years,id',
            ],

            'maximum_students_per_group' => [
                'nullable',
                'integer',
                'min:10',
                'max:100',
            ],
        ]);

        $result = $service->generate(
            (int) $validated['academic_year_id'],
            $validated['maximum_students_per_group'] !== null
                ? (int) $validated['maximum_students_per_group']
                : null
        );

        $message =
            $result['selection_count'] .
            ' birinci tercih analiz edildi. ' .
            $result['group_count'] .
            ' grup ve ' .
            $result['placement_count'] .
            ' öğrenci yerleştirmesi oluşturuldu.';

        if (
            isset($result['preference2_count'])
            &&
            $result['preference2_count'] > 0
        ) {
            $message .=
                ' ' .
                $result['preference2_count'] .
                ' öğrenci 2. tercihe yerleştirildi.';
        }

        if (
            isset($result['preference3_count'])
            &&
            $result['preference3_count'] > 0
        ) {
            $message .=
                ' ' .
                $result['preference3_count'] .
                ' öğrenci 3. tercihe yerleştirildi.';
        }

        if (
            isset($result['fallback_count'])
            &&
            $result['fallback_count'] > 0
        ) {
            $message .=
                ' ' .
                $result['fallback_count'] .
                ' öğrenci aynı kategori içindeki mevcut uygun gruba otomatik aktarıldı.';
        }

        if (
            isset($result['unresolved_count'])
            &&
            $result['unresolved_count'] > 0
        ) {
            $message .=
                ' ' .
                $result['unresolved_count'] .
                ' öğrenci hâlâ manuel kontrol gerektiriyor.';
        }

        return back()->with(
            'success',
            $message
        );
    }

    /**
     * Grup durumunu değiştirir.
     */
    public function updateStatus(
        Request $request,
        StudentCourseGroup $group
    ) {

        if ($group->confirmed_at !== null) {
            return back()->withErrors([
                'Kesinleştirilmiş grubun durumu değiştirilemez.',
            ]);
        }

        $validated = $request->validate([
            'status' => [
                'required',
                'integer',
                'in:1,2,4',
            ],
        ]);

        $group->update([
            'status' =>
            (int) $validated['status'],
        ]);

        if (
            (int) $validated['status'] === 4
        ) {
            $group->placements()
                ->whereIn(
                    'status',
                    [1, 2]
                )
                ->update([
                    'status' => 4,
                ]);
        }

        return back()->with(
            'success',
            'Grup durumu güncellendi.'
        );
    }

    /**
     * Grup notunu günceller.
     */
    public function updateNotes(
        Request $request,
        StudentCourseGroup $group
    ) {
        $validated = $request->validate([
            'notes' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ]);

        $group->update([
            'notes' =>
            $validated['notes'] ?? null,
        ]);

        return back()->with(
            'success',
            'Grup notu güncellendi.'
        );
    }

    /**
     * Öğrenciyi manuel olarak başka uygun gruba taşır.
     */
    public function moveStudent(
        Request $request,
        StudentCoursePlacement $placement,
        GroupManagementService $service
    ) {
        $validated = $request->validate([
            'target_group_id' => [
                'required',
                'integer',
                'exists:student_course_groups,id',
            ],
        ]);

        $targetGroup =
            StudentCourseGroup::findOrFail(
                (int) $validated['target_group_id']
            );

        try {
            $service->moveStudent(
                $placement,
                $targetGroup
            );
        } catch (RuntimeException $e) {
            return back()
                ->withInput()
                ->withErrors([
                    $e->getMessage(),
                ]);
        }

        return back()->with(
            'success',
            'Öğrenci yeni gruba taşındı.'
        );
    }

    /**
     * Grubu kapatır ve öğrencilerini otomatik olarak
     * yeniden dağıtır.
     */
    public function closeAndRedistribute(
        StudentCourseGroup $group,
        GroupManagementService $service
    ) {
        try {
            $result =
                $service->closeAndRedistribute(
                    $group
                );
        } catch (RuntimeException $e) {
            return back()
                ->withErrors([
                    $e->getMessage(),
                ]);
        }

        if (! $result['success']) {
            $unmovedCount =
                count($result['unmoved']);

            return back()
                ->withErrors([
                    "Grup kapatılamadı. " .
                        $result['moved'] .
                        " öğrenci başka gruplara taşındı; " .
                        $unmovedCount .
                        " öğrenci için uygun kapasite bulunamadı. " .
                        "Kalan öğrencileri başka gruplara manuel taşıdıktan sonra "
                        . "grubu tekrar kapatmayı deneyebilirsiniz.",
                ]);
        }

        return back()->with(
            'success',
            $result['moved'] .
                ' öğrenci diğer uygun gruplara dağıtıldı ve grup kapatıldı.'
        );
    }
}
