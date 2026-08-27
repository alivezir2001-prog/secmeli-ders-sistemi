<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CourseSelectionController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminAcademicYearController;
use App\Http\Controllers\AdminCourseSelectionController;
use App\Http\Controllers\AdminCourseOfferingController;
use App\Http\Controllers\AdminCourseController;
use App\Http\Controllers\AdminStudentPlacementController;
use App\Http\Controllers\AdminStudentCourseGroupController;
use App\Http\Controllers\AdminStudentController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/tercihler', [CourseSelectionController::class, 'index'])
    ->middleware('auth')
    ->name('course-selections.index');

Route::post('/tercihler', [CourseSelectionController::class, 'store'])
    ->middleware('auth')
    ->name('course-selections.store');

Route::get('/giris', [AuthController::class, 'showLogin'])
    ->name('login');

Route::post('/giris', [AuthController::class, 'login'])
    ->name('login.post');

Route::post('/cikis', [AuthController::class, 'logout'])
    ->name('logout');

Route::get('/yonetim/giris', [AdminAuthController::class, 'showLogin'])
    ->name('admin.login');

Route::post('/yonetim/giris', [AdminAuthController::class, 'login'])
    ->name('admin.login.post');

Route::get('/yonetim', [AdminController::class, 'index'])
    ->middleware('admin')
    ->name('admin.dashboard');

Route::get('/yonetim/egitim-yillari', [AdminAcademicYearController::class, 'index'])
    ->middleware('admin')
    ->name('admin.academic-years.index');

Route::put('/yonetim/egitim-yillari/{academicYear}', [AdminAcademicYearController::class, 'update'])
    ->middleware('admin')
    ->name('admin.academic-years.update');

Route::get('/yonetim/tercihler', [AdminCourseSelectionController::class, 'index'])
    ->middleware('admin')
    ->name('admin.course-selections.index');

Route::get('/yonetim/tercihler/excel', [
    AdminCourseSelectionController::class,
    'export'
])
    ->middleware('admin')
    ->name('admin.course-selections.export');

Route::get('/yonetim/tercihler/ozet-excel', [
    AdminCourseSelectionController::class,
    'exportSummary',
])
    ->middleware('admin')
    ->name('admin.course-selections.export-summary');

Route::get('/yonetim/tercihler/pdf', [
    AdminCourseSelectionController::class,
    'exportPdf',
])
    ->middleware('admin')
    ->name('admin.course-selections.export-pdf');

Route::get('/yonetim/tercihler/ozet-pdf', [
    AdminCourseSelectionController::class,
    'exportSummaryPdf',
])
    ->middleware('admin')
    ->name('admin.course-selections.export-summary-pdf');

Route::get('/yonetim/ders-kontenjanlari', [
    AdminCourseOfferingController::class,
    'index',
])
    ->middleware('admin')
    ->name('admin.course-offerings.index');

Route::put('/yonetim/ders-kontenjanlari/{course}', [
    AdminCourseOfferingController::class,
    'update',
])
    ->middleware('admin')
    ->name('admin.course-offerings.update');

Route::get('/yonetim/dersler', [
    AdminCourseController::class,
    'index',
])
    ->middleware('admin')
    ->name('admin.courses.index');

Route::post('/yonetim/dersler', [
    AdminCourseController::class,
    'store',
])
    ->middleware('admin')
    ->name('admin.courses.store');

Route::put('/yonetim/dersler/{course}', [
    AdminCourseController::class,
    'update',
])
    ->middleware('admin')
    ->name('admin.courses.update');

Route::post('/yonetim/dersler/{course}/modul-gruplari', [
    AdminCourseController::class,
    'storeModuleGroup',
])
    ->middleware('admin')
    ->name('admin.courses.module-groups.store');

Route::put('/yonetim/dersler/{course}/modul-gruplari/{group}/toggle', [
    AdminCourseController::class,
    'toggleModuleGroup',
])
    ->middleware('admin')
    ->name('admin.courses.module-groups.toggle');

Route::put('/yonetim/dersler/{course}/moduller/{module}', [
    AdminCourseController::class,
    'updateModule',
])
    ->middleware('admin')
    ->name('admin.courses.modules.update');

Route::get('/yonetim/ogrenciler/import', [
    AdminStudentController::class,
    'importForm',
])
    ->middleware('admin')
    ->name('admin.students.import');

Route::post('/yonetim/ogrenciler/import/preview', [
    AdminStudentController::class,
    'importPreview',
])
    ->middleware('admin')
    ->name('admin.students.import.preview');

Route::post('/yonetim/ogrenciler/import/execute', [
    AdminStudentController::class,
    'importExecute',
])
    ->middleware('admin')
    ->name('admin.students.import.execute');

Route::get(
    '/yonetim/ogrenci-yerlestirmeleri',
    [AdminStudentPlacementController::class, 'index']
)->name('admin.student-placements.index');

Route::put(
    '/yonetim/ogrenci-yerlestirmeleri/tercihler/{selection}',
    [AdminStudentPlacementController::class, 'place']
)->name('admin.student-placements.place');

Route::post(
    '/yonetim/ogrenci-yerlestirmeleri/akademik-yil/{academicYear}/kesinlestir',
    [AdminStudentPlacementController::class, 'confirm']
)->name('admin.student-placements.confirm');

Route::get(
    '/yonetim/manuel-yerlestirme',
    [
        AdminStudentCourseGroupController::class,
        'manualPlacement',
    ]
)
    ->middleware('admin')
    ->name(
        'admin.student-course-groups.manual-placement'
    );

Route::post(
    '/yonetim/manuel-yerlestirme/{selection}',
    [
        AdminStudentCourseGroupController::class,
        'manualPlace',
    ]
)
    ->middleware('admin')
    ->name(
        'admin.student-course-groups.manual-place'
    );

Route::get(
    '/yonetim/ogrenci-gruplari',
    [AdminStudentCourseGroupController::class, 'index']
)->name('admin.student-course-groups.index');

Route::post(
    '/yonetim/ogrenci-gruplari/olustur',
    [AdminStudentCourseGroupController::class, 'generate']
)->name('admin.student-course-groups.generate');

Route::put(
    '/yonetim/ogrenci-gruplari/{group}/durum',
    [AdminStudentCourseGroupController::class, 'updateStatus']
)->name('admin.student-course-groups.status');

Route::put(
    '/yonetim/ogrenci-gruplari/{group}/not',
    [AdminStudentCourseGroupController::class, 'updateNotes']
)->name('admin.student-course-groups.notes');

Route::post(
    'yonetim/ogrenci-gruplari/ogrenci/{placement}/tasi',
    [\App\Http\Controllers\AdminStudentCourseGroupController::class, 'moveStudent']
)->name('admin.student-course-groups.move-student');

Route::post(
    'yonetim/ogrenci-gruplari/{group}/kapat-ve-dagit',
    [\App\Http\Controllers\AdminStudentCourseGroupController::class, 'closeAndRedistribute']
)->name('admin.student-course-groups.close-redistribute');

Route::get('/yonetim/ogrenciler', [
    AdminStudentController::class,
    'index',
])
    ->middleware('admin')
    ->name('admin.students.index');

Route::post('/yonetim/ogrenciler', [
    AdminStudentController::class,
    'store',
])
    ->middleware('admin')
    ->name('admin.students.store');

Route::put('/yonetim/ogrenciler/{student}', [
    AdminStudentController::class,
    'update',
])
    ->middleware('admin')
    ->name('admin.students.update');

Route::put('/yonetim/ogrenciler/{student}/durum', [
    AdminStudentController::class,
    'updateStatus',
])
    ->middleware('admin')
    ->name('admin.students.status');
