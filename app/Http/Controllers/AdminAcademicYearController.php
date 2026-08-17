<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use Illuminate\Http\Request;

class AdminAcademicYearController extends Controller
{
    public function index()
    {
        $academicYears = AcademicYear::orderByDesc('name')->get();

        return view('admin.academic-years.index', compact('academicYears'));
    }

    public function update(Request $request, AcademicYear $academicYear)
    {
        $validated = $request->validate([
            'active' => ['nullable', 'boolean'],
            'selection_enabled' => ['nullable', 'boolean'],
            'selection_start_at' => ['nullable', 'date'],
            'selection_end_at' => ['nullable', 'date', 'after_or_equal:selection_start_at'],
        ]);

        /*
         * Form checkbox gönderilmezse false kabul ediyoruz.
         */
        $active = $request->boolean('active');
        $selectionEnabled = $request->boolean('selection_enabled');

        /*
         * Aynı anda yalnızca bir eğitim yılı aktif olabilir.
         */
        if ($active) {
            AcademicYear::where('id', '!=', $academicYear->id)
                ->update([
                    'active' => false,
                    'selection_enabled' => false,
                ]);
        }

        /*
         * Pasif eğitim yılında tercih dönemi açık olamaz.
         */
        if (! $active) {
            $selectionEnabled = false;
        }

        $academicYear->update([
            'active' => $active,
            'selection_enabled' => $selectionEnabled,
            'selection_start_at' => $validated['selection_start_at'] ?? null,
            'selection_end_at' => $validated['selection_end_at'] ?? null,
        ]);

        return redirect()
            ->route('admin.academic-years.index')
            ->with('success', "{$academicYear->name} eğitim yılı güncellendi.");
    }
}