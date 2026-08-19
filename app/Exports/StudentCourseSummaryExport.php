<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StudentCourseSummaryExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    ShouldAutoSize,
    WithStyles
{
    public function __construct(
        protected Collection $selectionGroups
    ) {
    }

    public function collection(): Collection
    {
        return $this->selectionGroups->values();
    }

    public function headings(): array
    {
        return [
            'Öğrenci No',
            'Ad Soyad',
            'Sınıf',
            'Şube',
            '1. Tercih',
            '2. Tercih',
            '3. Tercih',
            'Toplam Saat',
            'Durum',
        ];
    }

    public function map($selections): array
    {
        $selections = $selections
            ->sortBy('preference_order')
            ->values();

        $first = $selections->first();

        $student = $first->student;
        $studentYear = $student->studentYears->first();

        $courses = $selections
            ->map(fn ($selection) => $selection->course->name)
            ->values();

        $totalHours = $selections->sum(
            fn ($selection) =>
                $selection->gradeOption->weekly_hours
        );

        return [
            $student->student_number,

            trim(
                $student->first_name . ' ' .
                $student->last_name
            ),

            $studentYear?->grade,

            $studentYear?->section,

            $courses->get(0),

            $courses->get(1),

            $courses->get(2),

            $totalHours,

            'Gönderildi',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => [
                        'rgb' => 'FFFFFF',
                    ],
                ],
                'fill' => [
                    'fillType' => 'solid',
                    'startColor' => [
                        'rgb' => '245B91',
                    ],
                ],
                'alignment' => [
                    'horizontal' => 'center',
                    'vertical' => 'center',
                ],
            ],
        ];
    }
}