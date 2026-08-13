<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Services\CourseSelectionService;
use Illuminate\Http\Request;

class CourseSelectionController extends Controller
{
    public function index(
        Request $request,
        CourseSelectionService $service
    ) {
        /*
         * Oturum açmış kullanıcının bağlı olduğu öğrenci.
         */
        $student = auth()->user()->student;

        abort_unless(
            $student,
            403,
            'Bu kullanıcı bir öğrenci hesabına bağlı değil.'
        );

       $academicYear = AcademicYear::where('active', true)
        ->firstOrFail();

        /*
         * Tercih dönemi açık mı?
         *
         * Bu değer Blade'e gönderiliyor.
         */
        $selectionOpen = $academicYear->selectionsAreOpen();

        $courses = $service->availableCourses(
            $student,
            $academicYear->id
        );

        $selections = $student->courseSelections()
            ->where(
                'academic_year_id',
                $academicYear->id
            )
            ->with('course', 'gradeOption')
            ->orderBy('preference_order')
            ->get();

        return view(
            'course-selections.index',
            compact(
                'student',
                'academicYear',
                'courses',
                'selections',
                'service',
                'selectionOpen'
            )
        );
    }

    public function store(
        Request $request,
        CourseSelectionService $service
    ) {
        /*
         * Öğrenciyi kesinlikle POST verisinden almıyoruz.
         * Her zaman giriş yapan kullanıcının öğrencisi.
         */
        $student = auth()->user()->student;

        abort_unless(
            $student,
            403,
            'Bu kullanıcı bir öğrenci hesabına bağlı değil.'
        );

        $academicYear = AcademicYear::where('active', true)
            ->firstOrFail();

        /*
         * TERCIH DÖNEMİ KONTROLÜ
         *
         * Dönem kapalıysa hiçbir POST isteği kaydedilmez.
         */
        if (! $academicYear->selectionsAreOpen()) {
            return back()
                ->withErrors([
                    'Tercih dönemi şu anda kapalıdır. Tercihleriniz kaydedilmedi.',
                ]);
        }

        $result = $service->saveSelections(
            $student,
            $academicYear->id,
            $request->input('selections', [])
        );

        if (! $result['valid']) {
            return back()
                ->withInput()
                ->withErrors($result['errors']);
        }

        return redirect()
            ->route('course-selections.index')
            ->with(
                'success',
                'Tercihleriniz başarıyla kaydedildi.'
            );
    }
}