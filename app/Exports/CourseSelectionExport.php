<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CourseSelectionExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    ShouldAutoSize,
    WithStyles
{
    public function __construct(
        protected Collection $selections
    ) {
    }

    public function collection(): Collection
    {
        return $this->selections;
    }

    public function headings(): array
    {
        return [
            'Öğrenci No',
            'Ad Soyad',
            'Sınıf',
            'Şube',
            'Tercih Sırası',
            'Ders',
            'Ders Grubu',
            'Haftalık Saat',
            'Durum',
        ];
    }

    public function map($selection): array
    {
        $student = $selection->student;

        $studentYear = $student->studentYears->first();

        return [
            $student->student_number,

            trim(
                $student->first_name . ' ' .
                $student->last_name
            ),

            $studentYear?->grade,

            $studentYear?->section,

            $selection->preference_order,

            $selection->course->name,

            $selection->course->category->name,

            $selection->gradeOption->weekly_hours,

            $selection->status === 2
                ? 'Gönderildi'
                : 'Taslak',
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