<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CourseSelectionController;
use App\Http\Controllers\AuthController;

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
