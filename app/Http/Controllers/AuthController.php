<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'student_number' => ['required', 'string', 'max:50'],
            'password' => ['required', 'string'],
        ]);

        $studentNumber = trim($credentials['student_number']);

        $throttleKey = Str::lower($studentNumber) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            return back()
                ->withErrors([
                    'student_number' => "Çok fazla başarısız giriş denemesi. Lütfen {$seconds} saniye sonra tekrar deneyin.",
                ])
                ->onlyInput('student_number');
        }

        $student = Student::where('student_number', $studentNumber)
            ->where('active', true)
            ->first();

        if (!$student || !$student->user) {
            RateLimiter::hit($throttleKey, 600);

            return back()
                ->withErrors([
                    'student_number' => 'Öğrenci numarası veya şifre hatalı.',
                ])
                ->onlyInput('student_number');
        }

        $user = $student->user;

        if (!Hash::check($credentials['password'], $user->password)) {
            RateLimiter::hit($throttleKey, 600);

            return back()
                ->withErrors([
                    'student_number' => 'Öğrenci numarası veya şifre hatalı.',
                ])
                ->onlyInput('student_number');
        }

        RateLimiter::clear($throttleKey);

        Auth::login($user);

        $request->session()->regenerate();

        return redirect()->route('course-selections.index');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}