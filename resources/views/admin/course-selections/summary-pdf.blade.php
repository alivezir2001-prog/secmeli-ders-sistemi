<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">

    <style>
        @page {
            margin: 25px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 9px;
            color: #111827;
        }

        h1 {
            font-size: 18px;
            margin: 0 0 5px;
        }

        .subtitle {
            color: #64748b;
            margin-bottom: 15px;
        }

        .summary {
            margin-bottom: 18px;
        }

        .summary span {
            margin-right: 20px;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #245b91;
            color: white;
            font-weight: bold;
            padding: 7px 6px;
            border: 1px solid #cbd5e1;
        }

        td {
            padding: 7px 6px;
            border: 1px solid #cbd5e1;
            vertical-align: middle;
        }

        .center {
            text-align: center;
        }

        .footer {
            margin-top: 18px;
            font-size: 8px;
            color: #64748b;
        }
    </style>
</head>

<body>

<h1>Seçmeli Ders Öğrenci Özet Raporu</h1>

<div class="subtitle">
    Eğitim Yılı: {{ $academicYear->name }}
</div>

<div class="summary">
    <span>Öğrenci: {{ $selectionGroups->count() }}</span>
    <span>Toplam Tercih: {{ $filteredSelections->count() }}</span>
</div>

<table>
    <thead>
        <tr>
            <th>Öğrenci No</th>
            <th>Ad Soyad</th>
            <th>Sınıf</th>
            <th>Şube</th>
            <th>1. Tercih</th>
            <th>2. Tercih</th>
            <th>3. Tercih</th>
            <th>Toplam Saat</th>
            <th>Durum</th>
        </tr>
    </thead>

    <tbody>

        @foreach($selectionGroups as $selections)

            @php
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
            @endphp

            <tr>

                <td>
                    {{ $student->student_number }}
                </td>

                <td>
                    {{ $student->first_name }}
                    {{ $student->last_name }}
                </td>

                <td class="center">
                    {{ $studentYear?->grade }}
                </td>

                <td class="center">
                    {{ $studentYear?->section }}
                </td>

                <td>
                    {{ $courses->get(0, '') }}
                </td>

                <td>
                    {{ $courses->get(1, '') }}
                </td>

                <td>
                    {{ $courses->get(2, '') }}
                </td>

                <td class="center">
                    {{ $totalHours }}
                </td>

                <td class="center">
                    Gönderildi
                </td>

            </tr>

        @endforeach

    </tbody>
</table>

<div class="footer">
    Seçmeli Ders Sistemi —
    {{ now()->format('d.m.Y H:i') }}
</div>

</body>
</html>