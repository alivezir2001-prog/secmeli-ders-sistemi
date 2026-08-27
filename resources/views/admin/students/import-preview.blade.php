<!DOCTYPE html>
<html lang="tr">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>
        Öğrenci Listesi Önizleme
    </title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f1f5f9;
            color: #0f172a;
        }

        .header {
            background: #0f172a;
            color: #fff;
            padding: 22px 30px;
        }

        .header h1 {
            margin: 0;
            font-size: 22px;
        }

        .header p {
            margin: 5px 0 0;
            color: #cbd5e1;
            font-size: 13px;
        }

        .container {
            max-width: 1250px;
            margin: 30px auto;
            padding: 0 20px 50px;
        }

        .back {
            display: inline-block;
            margin-bottom: 18px;
            color: #245b91;
            text-decoration: none;
            font-size: 13px;
            font-weight: 700;
        }

        .summary {
            display: grid;
            grid-template-columns:
                repeat(5, minmax(0, 1fr));
            gap: 12px;
            margin-bottom: 20px;
        }

        .summary-card {
            background: #fff;
            border: 1px solid #dbe3ec;
            border-radius: 12px;
            padding: 16px;
        }

        .summary-label {
            color: #64748b;
            font-size: 10px;
            font-weight: 800;
        }

        .summary-value {
            margin-top: 6px;
            font-size: 24px;
            font-weight: 800;
        }

        .summary-new {
            border-color: #bbf7d0;
            background: #f0fdf4;
        }

        .summary-change {
            border-color: #bfdbfe;
            background: #eff6ff;
        }

        .summary-warning {
            border-color: #fed7aa;
            background: #fff7ed;
        }

        .card {
            background: #fff;
            border: 1px solid #dbe3ec;
            border-radius: 14px;
            overflow: hidden;
            margin-bottom: 18px;
        }

        .card-header {
            padding: 15px 18px;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
        }

        .card-header h2 {
            margin: 0;
            font-size: 17px;
        }

        .card-header p {
            margin: 5px 0 0;
            color: #64748b;
            font-size: 12px;
        }

        .table-wrap {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 800px;
        }

        th {
            padding: 11px 13px;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            color: #64748b;
            text-align: left;
            font-size: 10px;
            font-weight: 800;
        }

        td {
            padding: 11px 13px;
            border-bottom: 1px solid #edf2f7;
            font-size: 12px;
        }

        tr:last-child td {
            border-bottom: 0;
        }

        .number {
            font-weight: 800;
        }

        .badge {
            display: inline-flex;
            padding: 4px 8px;
            border-radius: 999px;
            background: #e0f2fe;
            color: #075985;
            font-size: 10px;
            font-weight: 800;
        }

        .duplicate {
            background: #fff7ed;
        }

        .duplicate-badge {
            display: inline-flex;
            padding: 4px 8px;
            border-radius: 999px;
            background: #ffedd5;
            color: #9a3412;
            font-size: 10px;
            font-weight: 800;
        }

        .info {
            padding: 14px 16px;
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 10px;
            margin-bottom: 18px;
            color: #1e3a8a;
            font-size: 12px;
            line-height: 1.5;
        }

        .warning {
            padding: 14px 16px;
            background: #fff7ed;
            border: 1px solid #fed7aa;
            border-radius: 10px;
            margin-bottom: 18px;
            color: #9a3412;
            font-size: 12px;
            line-height: 1.5;
        }

        .actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            margin-top: 20px;
        }

        .button {
            border: 0;
            border-radius: 9px;
            padding: 11px 16px;
            font-size: 13px;
            font-weight: 800;
            cursor: pointer;
            text-decoration: none;
        }

        .button-primary {
            background: #166534;
            color: #fff;
        }

        .button-secondary {
            background: #e2e8f0;
            color: #334155;
        }

        .button-disabled {
            opacity: .5;
            cursor: not-allowed;
        }

        .empty {
            padding: 35px;
            text-align: center;
            color: #64748b;
        }

        .small-note {
            margin-top: 6px;
            color: #64748b;
            font-size: 11px;
        }

        @media (max-width: 1000px) {

            .summary {
                grid-template-columns:
                    repeat(3, minmax(0, 1fr));
            }

        }

        @media (max-width: 650px) {

            .summary {
                grid-template-columns:
                    repeat(2, minmax(0, 1fr));
            }

        }
    </style>

</head>

<body>

    <header class="header">

        <h1>
            Öğrenci Listesi Önizleme
        </h1>

        <p>
            {{ $academicYear->name }} Eğitim ve Öğretim Yılı
        </p>

    </header>

    <main class="container">

        <a
            href="{{ route(
            'admin.students.import',
            [
                'academic_year_id' =>
                    $academicYear->id
            ]
        ) }}"
            class="back">
            ← İçe Aktarma Ekranına Dön
        </a>


        <div class="summary">

            <div class="summary-card">

                <div class="summary-label">
                    OKUNAN ÖĞRENCİ
                </div>

                <div class="summary-value">
                    {{ $totalCount }}
                </div>

            </div>


            <div class="summary-card summary-new">

                <div class="summary-label">
                    YENİ ÖĞRENCİ
                </div>

                <div class="summary-value">
                    {{ $newCount }}
                </div>

            </div>


            <div class="summary-card">

                <div class="summary-label">
                    MEVCUT ÖĞRENCİ
                </div>

                <div class="summary-value">
                    {{ $existingCount }}
                </div>

            </div>


            <div class="summary-card summary-change">

                <div class="summary-label">
                    SINIF / ŞUBE DEĞİŞECEK
                </div>

                <div class="summary-value">
                    {{ $changeCount }}
                </div>

            </div>


            <div class="summary-card">

                <div class="summary-label">
                    DEĞİŞİKLİK YOK
                </div>

                <div class="summary-value">
                    {{ $unchangedCount }}
                </div>

            </div>

        </div>


        <div class="summary">

            <div class="summary-card summary-warning">

                <div class="summary-label">
                    ANA SINIFI
                </div>

                <div class="summary-value">
                    {{ $ignoredCount }}
                </div>

                <div class="small-note">
                    İçe aktarılmayacak
                </div>

            </div>


            <div class="summary-card">

                <div class="summary-label">
                    PDF TEKRARI
                </div>

                <div class="summary-value">
                    {{ $duplicates->count() }}
                </div>

            </div>


            <div class="summary-card summary-warning">

                <div class="summary-label">
                    DB ÇAKIŞMASI
                </div>

                <div class="summary-value">
                    {{ $ambiguousCount }}
                </div>

                <div class="small-note">
                    Aynı no birden fazla öğrencide
                </div>

            </div>

        </div>


        <div class="info">

            <strong>
                PDF başarıyla analiz edildi.
            </strong>

            Henüz veritabanında değişiklik yapılmadı.
            İçe aktarma düğmesine bastığınızda yeni öğrenciler
            oluşturulacak ve mevcut öğrencilerin bu eğitim yılı
            sınıf/şube kayıtları güncellenecektir.

        </div>


        @if(
        $duplicates->isNotEmpty()
        ||
        $ambiguousCount > 0
        )

        <div class="warning">

            <strong>
                İçe aktarma yapılamaz.
            </strong>

            @if($duplicates->isNotEmpty())

            PDF içinde
            {{ $duplicates->count() }}
            tekrar eden öğrenci numarası bulundu.

            @endif

            @if($ambiguousCount > 0)

            Veritabanında
            {{ $ambiguousCount }}
            öğrenci numarası birden fazla öğrenciye ait.

            @endif

            Önce bu kayıtlar düzeltilmelidir.

        </div>

        @endif


        <section class="card">

            <div class="card-header">

                <h2>
                    Sınıf / Şube Dağılımı
                </h2>

                <p>
                    PDF'den okunan öğrenci sayıları
                </p>

            </div>


            <div class="table-wrap">

                <table>

                    <thead>

                        <tr>

                            <th>
                                SINIF
                            </th>

                            <th>
                                ŞUBE
                            </th>

                            <th>
                                ÖĞRENCİ SAYISI
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        @foreach(
                        $gradeSectionCounts
                        as $key => $count
                        )

                        @php
                        [$grade, $section] =
                        explode('|', $key, 2);
                        @endphp

                        <tr>

                            <td>
                                {{ $grade }}. Sınıf
                            </td>

                            <td>
                                {{ $section }}
                            </td>

                            <td>
                                <strong>
                                    {{ $count }}
                                </strong>
                            </td>

                        </tr>

                        @endforeach

                        <tr>

                            <td colspan="2">

                                <strong>
                                    5–8. Sınıflar Toplamı
                                </strong>

                            </td>

                            <td>

                                <strong>
                                    {{ $totalCount }}
                                </strong>

                            </td>

                        </tr>

                        <tr>

                            <td colspan="2">

                                <strong>
                                    Ana Sınıfı — İçe Aktarılmayacak
                                </strong>

                            </td>

                            <td>

                                <strong>
                                    {{ $ignoredCount }}
                                </strong>

                            </td>

                        </tr>

                    </tbody>

                </table>

            </div>

        </section>


        <section class="card">

            <div class="card-header">

                <h2>
                    Okunan Öğrenciler
                </h2>

                <p>
                    Toplam {{ $totalCount }} kayıt
                </p>

            </div>


            @if($students->isEmpty())

            <div class="empty">

                PDF'den öğrenci kaydı okunamadı.

            </div>

            @else

            <div class="table-wrap">

                <table>

                    <thead>

                        <tr>

                            <th>
                                SINIF
                            </th>

                            <th>
                                ŞUBE
                            </th>

                            <th>
                                ÖĞRENCİ NO
                            </th>

                            <th>
                                AD
                            </th>

                            <th>
                                SOYAD
                            </th>

                            <th>
                                CİNSİYET
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        @foreach($students as $student)

                        @php
                        $isDuplicate =
                        $duplicates->contains(
                        $student['student_number']
                        );
                        @endphp

                        <tr
                            class="{{ $isDuplicate ? 'duplicate' : '' }}">

                            <td>

                                <span class="badge">
                                    {{ $student['grade'] }}. Sınıf
                                </span>

                            </td>

                            <td>
                                {{ $student['section'] }}
                            </td>

                            <td class="number">

                                {{ $student['student_number'] }}

                                @if($isDuplicate)

                                <div>

                                    <span class="duplicate-badge">
                                        Tekrar
                                    </span>

                                </div>

                                @endif

                            </td>

                            <td>
                                {{ $student['first_name'] }}
                            </td>

                            <td>
                                {{ $student['last_name'] }}
                            </td>

                            <td>
                                {{ $student['gender'] }}
                            </td>

                        </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

            @endif

        </section>


        <div class="actions">

            <a
                href="{{ route(
                'admin.students.import',
                [
                    'academic_year_id' =>
                        $academicYear->id
                ]
            ) }}"
                class="button button-secondary">
                Vazgeç
            </a>


            @if(
            $students->isNotEmpty()
            &&
            $duplicates->isEmpty()
            &&
            $ambiguousCount === 0
            )

            <form
                method="POST"
                action="{{ route(
                    'admin.students.import.execute'
                ) }}">

                @csrf

                <input
                    type="hidden"
                    name="academic_year_id"
                    value="{{ $academicYear->id }}">

                <input
                    type="hidden"
                    name="token"
                    value="{{ $importToken }}">

                <button
                    type="submit"
                    class="button button-primary"
                    onclick="
                        return confirm(
                            'Önizlemede görünen öğrenci listesi veritabanına aktarılacaktır. Devam etmek istiyor musunuz?'
                        );
                    ">
                    {{ $newCount + $existingCount }}
                    Öğrenciyi İçe Aktar
                </button>

            </form>

            @else

            <button
                type="button"
                class="button button-secondary button-disabled"
                disabled>
                İçe Aktarılamıyor
            </button>

            @endif

        </div>

    </main>

</body>

</html>