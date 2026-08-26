<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Yerleştirme Kontrol Merkezi</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Inter, Arial, sans-serif;
            background: #f1f5f9;
            color: #0f172a;
        }

        button,
        input,
        select,
        textarea {
            font: inherit;
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
            max-width: 1500px;
            margin: 30px auto;
            padding: 0 20px 60px;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: end;
            gap: 20px;
            margin-bottom: 20px;
        }

        .title-block h2 {
            margin: 0 0 5px;
            font-size: 21px;
        }

        .title-block p {
            margin: 0;
            color: #64748b;
            font-size: 13px;
        }

        .year-select {
            min-width: 230px;
            padding: 10px 12px;
            border: 1px solid #cbd5e1;
            border-radius: 9px;
            background: #fff;
        }

        .success,
        .error {
            padding: 14px 16px;
            border-radius: 10px;
            margin-bottom: 18px;
            font-size: 13px;
        }

        .success {
            background: #ecfdf5;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .error {
            background: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(6, minmax(0, 1fr));
            gap: 12px;
            margin: 20px 0;
        }

        .summary-card {
            background: #fff;
            border: 1px solid #dbe3ec;
            border-radius: 12px;
            padding: 15px;
            box-shadow: 0 4px 15px rgba(15, 23, 42, .04);
        }

        .summary-label {
            color: #64748b;
            font-size: 10px;
            font-weight: 800;
            margin-bottom: 6px;
        }

        .summary-value {
            color: #0f172a;
            font-size: 21px;
            font-weight: 800;
        }

        .summary-card.warning .summary-value {
            color: #b45309;
        }

        .summary-card.success-card .summary-value {
            color: #15803d;
        }

        .summary-card.info-card .summary-value {
            color: #1d4ed8;
        }

        .filters {
            background: #fff;
            border: 1px solid #dbe3ec;
            border-radius: 14px;
            padding: 16px;
            margin-bottom: 18px;
            box-shadow: 0 4px 15px rgba(15, 23, 42, .04);
        }

        .filter-form {
            display: grid;
            grid-template-columns: 1.5fr 1fr 1fr auto;
            gap: 10px;
            align-items: end;
        }

        .filter-field label {
            display: block;
            margin-bottom: 5px;
            color: #64748b;
            font-size: 10px;
            font-weight: 800;
        }

        .filter-field input,
        .filter-field select {
            width: 100%;
            padding: 10px 11px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            background: #fff;
        }

        .filter-button {
            border: 0;
            border-radius: 8px;
            padding: 10px 15px;
            background: #245b91;
            color: #fff;
            font-weight: 700;
            cursor: pointer;
        }

        .reset-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 40px;
            padding: 0 14px;
            border-radius: 8px;
            background: #e2e8f0;
            color: #334155;
            text-decoration: none;
            font-size: 13px;
            font-weight: 700;
        }

        .table-card {
            background: #fff;
            border: 1px solid #dbe3ec;
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(15, 23, 42, .04);
        }

        .table-wrap {
            overflow-x: auto;
        }

        table {
            width: 100%;
            min-width: 1050px;
            border-collapse: collapse;
        }

        th {
            padding: 12px 14px;
            background: #f8fafc;
            border-bottom: 1px solid #dbe3ec;
            text-align: left;
            color: #64748b;
            font-size: 10px;
            font-weight: 800;
            white-space: nowrap;
        }

        td {
            padding: 13px 14px;
            border-bottom: 1px solid #edf2f7;
            vertical-align: middle;
            font-size: 12px;
        }

        tr:last-child td {
            border-bottom: 0;
        }

        .student-name {
            font-weight: 800;
            color: #0f172a;
        }

        .student-number {
            margin-top: 3px;
            color: #64748b;
            font-size: 10px;
        }

        .category-name {
            font-weight: 800;
        }

        .preference-list {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
            max-width: 430px;
        }

        .preference-chip {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 5px 8px;
            border-radius: 7px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            color: #334155;
            font-size: 10px;
        }

        .preference-chip strong {
            color: #64748b;
        }

        .final-course {
            font-weight: 800;
            color: #0f172a;
        }

        .final-detail {
            margin-top: 4px;
            color: #64748b;
            font-size: 10px;
        }

        .status {
            display: inline-flex;
            align-items: center;
            border-radius: 999px;
            padding: 6px 9px;
            font-size: 10px;
            font-weight: 800;
            white-space: nowrap;
        }

        .status-complete {
            background: #dcfce7;
            color: #166534;
        }

        .status-missing {
            background: #fef3c7;
            color: #92400e;
        }

        .status-auto {
            background: #ffedd5;
            color: #c2410c;
        }

        .status-manual {
            background: #e2e8f0;
            color: #334155;
        }

        .status-confirmed {
            background: #dbeafe;
            color: #1e40af;
        }

        .detail-row {
            display: none;
            background: #f8fafc;
        }

        .detail-content {
            padding: 18px 20px;
            border-top: 1px solid #e2e8f0;
        }

        .detail-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
        }

        .detail-title {
            margin-bottom: 8px;
            color: #64748b;
            font-size: 10px;
            font-weight: 800;
        }

        .detail-preference {
            display: flex;
            align-items: flex-start;
            gap: 9px;
            padding: 8px 0;
            border-bottom: 1px dashed #cbd5e1;
        }

        .detail-preference:last-child {
            border-bottom: 0;
        }

        .pref-number {
            flex: 0 0 auto;
            width: 24px;
            height: 24px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: #e2e8f0;
            color: #334155;
            font-size: 10px;
            font-weight: 800;
        }

        .detail-note {
            color: #64748b;
            font-size: 11px;
            line-height: 1.5;
        }

        .toggle-button {
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 7px 10px;
            background: #fff;
            color: #334155;
            font-size: 11px;
            font-weight: 700;
            cursor: pointer;
        }

        .toggle-button:hover {
            background: #f8fafc;
        }

        .empty {
            padding: 35px;
            text-align: center;
            color: #64748b;
        }

        .bottom-bar {
            margin-top: 18px;
            background: #fff;
            border: 1px solid #dbe3ec;
            border-radius: 14px;
            padding: 18px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
        }

        .bottom-info {
            color: #475569;
            font-size: 13px;
            line-height: 1.6;
        }

        .confirm-button {
            border: 0;
            border-radius: 10px;
            padding: 13px 20px;
            background: #166534;
            color: #fff;
            font-weight: 800;
            cursor: pointer;
        }

        .confirm-button:disabled {
            background: #cbd5e1;
            color: #64748b;
            cursor: not-allowed;
        }

        @media (max-width: 1100px) {
            .summary-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }

            .filter-form {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 700px) {
            .container {
                padding-left: 12px;
                padding-right: 12px;
            }

            .topbar,
            .bottom-bar {
                flex-direction: column;
                align-items: stretch;
            }

            .summary-grid {
                grid-template-columns: 1fr 1fr;
            }

            .filter-form {
                grid-template-columns: 1fr;
            }

            .detail-grid {
                grid-template-columns: 1fr;
            }

            .confirm-button {
                width: 100%;
            }
        }
    </style>
</head>

<body>

    <header class="header">
        <h1>Yerleştirme Kontrol Merkezi</h1>
        <p>{{ $academicYear->name }} Eğitim ve Öğretim Yılı</p>
    </header>

    <main class="container">

        @if(session('success'))
        <div class="success">
            {{ session('success') }}
        </div>
        @endif

        @if($errors->any())
        <div class="error">
            @foreach($errors->all() as $error)
            <div>{{ $error }}</div>
            @endforeach
        </div>
        @endif

        <div class="topbar">

            <div class="title-block">
                <h2>Yerleştirme Kontrolü</h2>
                <p>
                    Öğrencilerin kategori bazındaki nihai yerleşimlerini
                    kontrol edin ve yalnızca gerekli durumlarda müdahale edin.
                </p>
            </div>

            <form method="GET">

                @if($search !== '')
                <input
                    type="hidden"
                    name="search"
                    value="{{ $search }}">
                @endif

                @if($filter !== 'all')
                <input
                    type="hidden"
                    name="filter"
                    value="{{ $filter }}">
                @endif

                @if($categoryId)
                <input
                    type="hidden"
                    name="category_id"
                    value="{{ $categoryId }}">
                @endif

                <select
                    name="academic_year_id"
                    class="year-select"
                    onchange="this.form.submit()">
                    @foreach($academicYears as $year)

                    <option
                        value="{{ $year->id }}"
                        {{ (int) $year->id === (int) $academicYear->id ? 'selected' : '' }}>
                        {{ $year->name }}
                        {{ $year->active ? ' (Aktif)' : '' }}
                    </option>

                    @endforeach
                </select>

            </form>

        </div>

        <section class="summary-grid">

            <div class="summary-card">
                <div class="summary-label">
                    ÖĞRENCİ
                </div>

                <div class="summary-value">
                    {{ $totalStudents }}
                </div>
            </div>

            <div class="summary-card">
                <div class="summary-label">
                    KATEGORİ
                </div>

                <div class="summary-value">
                    {{ $totalCategories }}
                </div>
            </div>

            <div class="summary-card">
                <div class="summary-label">
                    TAMAMLANAN
                </div>

                <div class="summary-value">
                    {{ $placedRows }}
                </div>
            </div>

            <div class="summary-card warning">
                <div class="summary-label">
                    EKSİK
                </div>

                <div class="summary-value">
                    {{ $missingRows }}
                </div>
            </div>

            <div class="summary-card">
                <div class="summary-label">
                    OTOMATİK ALTERNATİF
                </div>

                <div class="summary-value">
                    {{ $automaticRows }}
                </div>
            </div>

            <div class="summary-card info-card">
                <div class="summary-label">
                    MANUEL DEĞİŞİKLİK
                </div>

                <div class="summary-value">
                    {{ $manualRows }}
                </div>
            </div>

        </section>

        <section class="filters">

            <form
                method="GET"
                class="filter-form">

                <input
                    type="hidden"
                    name="academic_year_id"
                    value="{{ $academicYear->id }}">

                <div class="filter-field">

                    <label>
                        ÖĞRENCİ ARA
                    </label>

                    <input
                        type="text"
                        name="search"
                        value="{{ $search }}"
                        placeholder="Ad, soyad veya öğrenci no...">

                </div>

                <div class="filter-field">

                    <label>
                        DURUM
                    </label>

                    <select name="filter">

                        <option
                            value="all"
                            {{ $filter === 'all' ? 'selected' : '' }}>
                            Tümü
                        </option>

                        <option
                            value="missing"
                            {{ $filter === 'missing' ? 'selected' : '' }}>
                            Eksikler
                        </option>

                        <option
                            value="automatic"
                            {{ $filter === 'automatic' ? 'selected' : '' }}>
                            Otomatik alternatif
                        </option>

                        <option
                            value="manual"
                            {{ $filter === 'manual' ? 'selected' : '' }}>
                            Manuel değişiklik
                        </option>

                        <option
                            value="confirmed"
                            {{ $filter === 'confirmed' ? 'selected' : '' }}>
                            Kesinleşmiş
                        </option>

                    </select>

                </div>

                <div class="filter-field">

                    <label>
                        KATEGORİ
                    </label>

                    <select name="category_id">

                        <option value="">
                            Tüm kategoriler
                        </option>

                        @foreach($categories as $category)

                        <option
                            value="{{ $category->id }}"
                            {{ $categoryId === (int) $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>

                        @endforeach

                    </select>

                </div>

                <div>

                    <button
                        type="submit"
                        class="filter-button">
                        Filtrele
                    </button>

                    <a
                        href="{{ route(
                        'admin.student-placements.index',
                        ['academic_year_id' => $academicYear->id]
                    ) }}"
                        class="reset-button">
                        Temizle
                    </a>

                </div>

            </form>

        </section>

        <section class="table-card">

            @if($categoryRows->isEmpty())

            <div class="empty">
                Bu filtrelere uygun yerleştirme kaydı bulunmuyor.
            </div>

            @else

            <div class="table-wrap">

                <table>

                    <thead>

                        <tr>

                            <th>
                                ÖĞRENCİ
                            </th>

                            <th>
                                KATEGORİ
                            </th>

                            <th>
                                TERCİHLER
                            </th>

                            <th>
                                NİHAİ YERLEŞİM
                            </th>

                            <th>
                                DURUM
                            </th>

                            <th>
                                DETAY
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        @foreach($categoryRows as $index => $row)

                        @php
                        $placement =
                        $row['placement'];

                        $placementCourse =
                        $placement?->course;

                        $rowId =
                        'detail-' . $index;

                        $statusLabel =
                        'Eksik';

                        $statusClass =
                        'status-missing';

                        if ($row['isConfirmed']) {
                        $statusLabel =
                        'Kesinleşmiş';

                        $statusClass =
                        'status-confirmed';

                        } elseif ($row['isManualChange']) {
                        $statusLabel =
                        'Manuel değişiklik';

                        $statusClass =
                        'status-manual';

                        } elseif ($row['isAutomaticAlternative']) {
                        $statusLabel =
                        'Otomatik alternatif';

                        $statusClass =
                        'status-auto';

                        } elseif ($placement) {
                        $statusLabel =
                        'Yerleştirildi';

                        $statusClass =
                        'status-complete';
                        }
                        @endphp

                        <tr>

                            <td>

                                <div class="student-name">
                                    {{ $row['student']->first_name }}
                                    {{ $row['student']->last_name }}
                                </div>

                                <div class="student-number">
                                    Öğrenci No:
                                    {{ $row['student']->student_number ?? '-' }}
                                </div>

                            </td>

                            <td>

                                <div class="category-name">
                                    {{ $row['category']?->name ?? 'Kategori' }}
                                </div>

                            </td>

                            <td>

                                <div class="preference-list">

                                    @foreach($row['selections'] as $selection)

                                    <span class="preference-chip">

                                        <strong>
                                            {{ $selection->preference_order }}.
                                        </strong>

                                        {{ $selection->course->name }}

                                    </span>

                                    @endforeach

                                </div>

                            </td>

                            <td>

                                @if($placementCourse)

                                <div class="final-course">
                                    {{ $placementCourse->name }}
                                </div>

                                <div class="final-detail">

                                    @if($row['placement_preference'])

                                    {{ $row['placement_preference'] }}.
                                    tercih

                                    @endif

                                    @if($placement->weekly_hours)
                                    ·
                                    {{ $placement->weekly_hours }} saat
                                    @endif

                                </div>

                                @else

                                <span class="status status-missing">
                                    Yerleşim yok
                                </span>

                                @endif

                            </td>

                            <td>

                                <span class="status {{ $statusClass }}">
                                    {{ $statusLabel }}
                                </span>

                            </td>

                            <td>

                                <button
                                    type="button"
                                    class="toggle-button"
                                    onclick="toggleDetail('{{ $rowId }}', this)">
                                    Detay
                                </button>

                            </td>

                        </tr>

                        <tr
                            id="{{ $rowId }}"
                            class="detail-row">

                            <td colspan="6">

                                <div class="detail-content">

                                    <div class="detail-grid">

                                        <div>

                                            <div class="detail-title">
                                                ÖĞRENCİNİN TERCİHLERİ
                                            </div>

                                            @foreach($row['selections'] as $selection)

                                            <div class="detail-preference">

                                                <span class="pref-number">
                                                    {{ $selection->preference_order }}
                                                </span>

                                                <div>

                                                    <div class="value">
                                                        {{ $selection->course->name }}
                                                    </div>

                                                    <div class="detail-note">
                                                        {{ $selection->weekly_hours }}
                                                        saat
                                                    </div>

                                                </div>

                                            </div>

                                            @endforeach

                                        </div>

                                        <div>

                                            <div class="detail-title">
                                                NİHAİ SONUÇ
                                            </div>

                                            @if($placementCourse)

                                            <div class="final-course">
                                                {{ $placementCourse->name }}
                                            </div>

                                            <div class="detail-note">
                                                Program:
                                                {{ $placement?->moduleGroup?->name ?? '—' }}
                                            </div>

                                            @if($placement?->module)

                                            <div class="detail-note">
                                                Modül:
                                                {{ $placement->module->name }}
                                            </div>

                                            @endif

                                            <div class="detail-note">

                                                @if($row['isAutomaticAlternative'])

                                                Sistem tarafından aynı kategori
                                                içinde otomatik alternatif
                                                yerleştirme yapıldı.

                                                @elseif($row['isManualChange'])

                                                Okul tarafından manuel olarak
                                                değiştirildi.

                                                @elseif($row['placement_preference'])

                                                Öğrenci
                                                {{ $row['placement_preference'] }}.
                                                tercihinden yerleştirildi.

                                                @else

                                                Nihai yerleşim oluşturuldu.

                                                @endif

                                            </div>

                                            @else

                                            <span class="status status-missing">
                                                Henüz yerleştirme yok.
                                            </span>

                                            @if($row['suggestedModule'])

                                            <div
                                                class="detail-note"
                                                style="margin-top:10px;">
                                                Sistem önerisi:
                                                Modül
                                                {{ $row['suggestedModule']->module_number }}
                                                —
                                                {{ $row['suggestedModule']->name }}
                                            </div>

                                            @endif

                                            @endif

                                        </div>

                                    </div>

                                </div>

                            </td>

                        </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

            @endif

        </section>

        <div class="bottom-bar">

            <div class="bottom-info">

                <strong>{{ $placedCategoryKeys }}</strong>
                /
                <strong>{{ $requiredPlacementCount }}</strong>

                öğrenci-kategori yerleştirmesi tamamlandı.

                <br>

                @if($confirmReady)

                Tüm gerekli yerleştirmeler hazır.
                Kesinleştirme yapılabilir.

                @else

                Eksik yerleştirmeler bulunduğu için
                kesinleştirme yapılamaz.

                @endif

            </div>

            <form
                method="POST"
                action="{{ route(
                'admin.student-placements.confirm',
                $academicYear
            ) }}">

                @csrf

                <button
                    type="submit"
                    class="confirm-button"
                    {{ ! $confirmReady ? 'disabled' : '' }}
                    onclick="
                    return confirm(
                        'Tüm nihai yerleştirmeleri kesinleştirmek istediğinize emin misiniz?'
                    );
                ">
                    Yerleştirmeleri Kesinleştir
                </button>

            </form>

        </div>

    </main>

    <script>
        function toggleDetail(id, button) {
            const row = document.getElementById(id);

            if (!row) {
                return;
            }

            const isOpen =
                row.style.display === 'table-row';

            row.style.display =
                isOpen ?
                'none' :
                'table-row';

            button.textContent =
                isOpen ?
                'Detay' :
                'Detayı Kapat';
        }
    </script>

</body>

</html>