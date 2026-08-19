<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CourseSelectionController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminAcademicYearController;
use App\Http\Controllers\AdminCourseSelectionController;

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
