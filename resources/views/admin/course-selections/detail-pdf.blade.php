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
            font-size: 10px;
            color: #111827;
        }

        h1 {
            font-size: 18px;
            margin: 0 0 5px;
        }

        .subtitle {
            color: #64748b;
            margin-bottom: 18px;
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
            padding: 7px;
            border: 1px solid #cbd5e1;
        }

        td {
            padding: 6px;
            border: 1px solid #cbd5e1;
        }

        .center {
            text-align: center;
        }

        .footer {
            margin-top: 18px;
            font-size: 9px;
            color: #64748b;
        }
    </style>
</head>

<body>

<h1>Seçmeli Ders Tercihleri</h1>

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
            <th>Sıra</th>
            <th>Ders</th>
            <th>Ders Grubu</th>
            <th>Saat</th>
            <th>Durum</th>
        </tr>
    </thead>

    <tbody>
        @foreach($filteredSelections as $selection)

            @php
                $student = $selection->student;
                $studentYear = $student->studentYears->first();
            @endphp

            <tr>
                <td>{{ $student->student_number }}</td>

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

                <td class="center">
                    {{ $selection->preference_order }}
                </td>

                <td>
                    {{ $selection->course->name }}
                </td>

                <td>
                    {{ $selection->course->category->name }}
                </td>

                <td class="center">
                    {{ $selection->gradeOption->weekly_hours }}
                </td>

                <td class="center">
                    {{ $selection->status === 2 ? 'Gönderildi' : 'Taslak' }}
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