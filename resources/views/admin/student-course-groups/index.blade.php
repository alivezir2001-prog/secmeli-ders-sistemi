<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Seçmeli Ders Grupları</title>

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
            color: white;
            padding: 22px 30px;
        }

        .header h1 {
            margin: 0;
            font-size: 22px;
        }

        .header p {
            margin: 5px 0 0;
            color: #cbd5e1;
        }

        .container {
            max-width: 1450px;
            margin: 30px auto;
            padding: 0 20px 60px;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: end;
            gap: 20px;
            margin-bottom: 22px;
        }

        .title-block h2 {
            margin: 0 0 5px;
        }

        .title-block p {
            margin: 0;
            color: #64748b;
            font-size: 13px;
        }

        .success,
        .error {
            padding: 14px 16px;
            border-radius: 10px;
            margin-bottom: 18px;
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

        .generate-panel,
        .summary-panel {
            background: white;
            border-radius: 14px;
            padding: 18px;
            margin-bottom: 22px;
            box-shadow: 0 4px 15px rgba(15, 23, 42, .05);
        }

        .generate-grid {
            display: grid;
            grid-template-columns: 180px 220px 1fr;
            gap: 14px;
            align-items: end;
        }

        .field label {
            display: block;
            margin-bottom: 5px;
            font-size: 11px;
            color: #475569;
            font-weight: 800;
        }

        select,
        input,
        textarea {
            width: 100%;
            padding: 9px 10px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            background: white;
        }

        textarea {
            resize: vertical;
        }

        .button {
            border: 0;
            border-radius: 9px;
            padding: 10px 14px;
            font-weight: 700;
            cursor: pointer;
        }

        .button-primary {
            background: #245b91;
            color: white;
        }

        .button-primary:hover {
            background: #194a78;
        }

        .button-secondary {
            background: #e2e8f0;
            color: #334155;
        }

        .button-danger {
            background: #b91c1c;
            color: white;
        }

        .button-warning {
            background: #f59e0b;
            color: #111827;
        }

        .button-small {
            padding: 7px 10px;
            font-size: 11px;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(6, minmax(0, 1fr));
            gap: 10px;
        }

        .summary-item {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 12px;
        }

        .summary-label {
            color: #64748b;
            font-size: 11px;
            margin-bottom: 4px;
        }

        .summary-value {
            font-size: 20px;
            font-weight: 800;
        }

        .course-block {
            margin-bottom: 30px;
        }

        .course-heading {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            margin-bottom: 10px;
        }

        .course-heading h3 {
            margin: 0;
            font-size: 18px;
        }

        .course-heading span {
            color: #64748b;
            font-size: 12px;
        }

        .group-block {
            background: white;
            border-radius: 14px;
            margin-bottom: 16px;
            box-shadow: 0 4px 15px rgba(15, 23, 42, .05);
            overflow: hidden;
        }

        .group-block.closed {
            opacity: .65;
        }

        .group-header {
            padding: 15px 18px;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
        }

        .group-title {
            font-weight: 800;
            font-size: 16px;
        }

        .group-meta {
            margin-top: 5px;
            color: #64748b;
            font-size: 12px;
        }

        .group-status {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            border-radius: 999px;
            padding: 6px 10px;
            font-size: 11px;
            font-weight: 800;
        }

        .badge-draft {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-active {
            background: #dcfce7;
            color: #166534;
        }

        .badge-closed {
            background: #fee2e2;
            color: #991b1b;
        }

        .badge-ok {
            background: #dcfce7;
            color: #166534;
        }

        .badge-warning {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-auto {
            background: #e0e7ff;
            color: #3730a3;
        }

        .badge-manual {
            background: #e2e8f0;
            color: #334155;
        }

        .badge-pref2 {
            background: #ede9fe;
            color: #6d28d9;
        }

        .badge-pref3 {
            background: #fce7f3;
            color: #be185d;
        }

        .badge-fallback {
            background: #fff7ed;
            color: #c2410c;
        }

        .badge-primary-pref {
            background: #dcfce7;
            color: #166534;
        }

        .group-body {
            padding: 16px 18px;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 12px;
            margin-bottom: 16px;
        }

        .stat {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 12px;
        }

        .stat-label {
            color: #64748b;
            font-size: 11px;
            margin-bottom: 4px;
        }

        .stat-value {
            font-size: 18px;
            font-weight: 800;
        }

        .student-list {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
        }

        .student {
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            background: white;
            padding: 11px;
        }

        .student-main {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            align-items: flex-start;
        }

        .student-name {
            font-size: 13px;
            font-weight: 700;
        }

        .student-number {
            color: #64748b;
            margin-right: 5px;
            font-weight: 400;
        }

        .student-meta {
            margin-top: 6px;
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
        }

        .student-actions {
            margin-top: 9px;
            display: grid;
            grid-template-columns: 1fr;
        }

        .move-box {
            margin-top: 8px;
            padding-top: 8px;
            border-top: 1px dashed #cbd5e1;
        }

        .actions {
            margin-top: 16px;
            display: grid;
            grid-template-columns: 1fr 190px 150px;
            gap: 10px;
            align-items: end;
        }

        .danger-area {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #e2e8f0;
            display: flex;
            justify-content: flex-end;
        }

        .empty {
            background: white;
            border-radius: 14px;
            padding: 30px;
            text-align: center;
            color: #64748b;
        }

        .modal-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, .55);
            display: none;
            align-items: center;
            justify-content: center;
            padding: 20px;
            z-index: 1000;
        }

        .modal-backdrop.open {
            display: flex;
        }

        .modal {
            width: 100%;
            max-width: 520px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 50px rgba(15, 23, 42, .25);
            padding: 22px;
        }

        .modal h3 {
            margin: 0 0 8px;
        }

        .modal p {
            color: #64748b;
            font-size: 13px;
            line-height: 1.5;
        }

        .modal-actions {
            margin-top: 18px;
            display: flex;
            justify-content: flex-end;
            gap: 8px;
        }

        @media (max-width: 1100px) {
            .summary-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }

            .stats {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }

            .student-list {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 900px) {

            .generate-grid,
            .actions {
                grid-template-columns: 1fr;
            }

            .topbar,
            .group-header,
            .course-heading {
                flex-direction: column;
                align-items: stretch;
            }

            .group-status {
                justify-content: flex-start;
            }
        }

        @media (max-width: 650px) {

            .summary-grid,
            .stats {
                grid-template-columns: 1fr 1fr;
            }
        }
    </style>
</head>

<body>

    <header class="header">
        <h1>Seçmeli Ders Grupları</h1>
        <p>{{ $academicYear->name }} Eğitim ve Öğretim Yılı</p>
    </header>

    <main class="container">

        @php
        $hasGroups = $groups->isNotEmpty();
        @endphp

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
                <h2>Grup Yönetimi</h2>

                <p>
                    Otomatik oluşan grupları inceleyin,
                    öğrencileri gerektiğinde taşıyın ve
                    grupları yeniden dağıtın.
                </p>
            </div>

            <form method="GET">
                <select
                    name="academic_year_id"
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

        <section class="generate-panel">

            <form
                method="POST"
                action="{{ route('admin.student-course-groups.generate') }}">
                @csrf

                <input
                    type="hidden"
                    name="academic_year_id"
                    value="{{ $academicYear->id }}">

                <div class="generate-grid">

                    <div class="field">
                        <label>MAKSİMUM GRUP MEVCUDU</label>

                        <input
                            type="number"
                            name="maximum_students_per_group"
                            min="10"
                            max="100"
                            value="{{ old('maximum_students_per_group', 20) }}"
                            {{ $hasGroups ? 'disabled' : '' }}>
                    </div>

                    <div class="field">
                        <label>EĞİTİM YILI</label>

                        <div style="
                        padding:10px;
                        background:#f8fafc;
                        border:1px solid #e2e8f0;
                        border-radius:8px;
                        font-weight:700;
                    ">
                            {{ $academicYear->name }}
                        </div>
                    </div>

                    <div style="text-align:right;">
                        @if($hasGroups)

                        <button
                            type="button"
                            class="button button-secondary"
                            disabled
                            style="opacity:.7;cursor:not-allowed;">
                            Gruplar Oluşturuldu
                        </button>

                        @else

                        <button
                            type="submit"
                            class="button button-primary"
                            onclick="
            return confirm(
                'Bu eğitim yılı için otomatik öğrenci grupları oluşturulacak. Devam etmek istiyor musunuz?'
            );
        ">
                            Grupları Oluştur
                        </button>

                        @endif
                    </div>

                </div>
            </form>

            <div style="
            margin-top:10px;
            color:#64748b;
            font-size:12px;
        ">
                Sistem gönderilmiş öğrenci tercihlerini analiz eder.
                Yeni grup oluşturulabilmesi için en az
                <strong>10 öğrenci</strong> gerekir.
            </div>

        </section>

        @php
        $activeGroups =
        $groups->whereIn('status', [1,2]);

        $allPlacements =
        $groups->flatMap(
        fn ($group) =>
        $group->placements
        );

        $totalStudents =
        $allPlacements
        ->whereIn('status', [1,2,3])
        ->pluck('student_id')
        ->unique()
        ->count();

        $autoAlternativeCount =
        $allPlacements
        ->whereIn('status', [1,2,3])
        ->filter(function ($placement) {
        return str_contains(
        (string) $placement->notes,
        'otomatik'
        );
        })
        ->count();

        $minimumProblemGroups =
        $activeGroups
        ->filter(function ($group) {
        $count =
        $group->placements
        ->whereIn('status', [1,2,3])
        ->count();

        return
        $count < (int) $group->minimum_students;
            })
            ->count();

            $closedGroups =
            $groups
            ->where('status', 4)
            ->count();
            @endphp

            <section class="summary-panel">

                <div class="summary-grid">

                    <div class="summary-item">
                        <div class="summary-label">
                            AKTİF GRUP
                        </div>

                        <div class="summary-value">
                            {{ $activeGroups->count() }}
                        </div>
                    </div>

                    <div class="summary-item">
                        <div class="summary-label">
                            YERLEŞEN ÖĞRENCİ
                        </div>

                        <div class="summary-value">
                            {{ $totalStudents }}
                        </div>
                    </div>

                    <div class="summary-item">
                        <div class="summary-label">
                            OTOMATİK ALTERNATİF
                        </div>

                        <div class="summary-value">
                            {{ $autoAlternativeCount }}
                        </div>
                    </div>

                    <div class="summary-item">
                        <div class="summary-label">
                            MİNİMUM ALTI GRUP
                        </div>

                        <div class="summary-value">
                            {{ $minimumProblemGroups }}
                        </div>
                    </div>

                    <div class="summary-item">
                        <div class="summary-label">
                            KAPALI GRUP
                        </div>

                        <div class="summary-value">
                            {{ $closedGroups }}
                        </div>
                    </div>

                    <div class="summary-item">
                        <div class="summary-label">
                            KONTROL
                        </div>

                        <div class="summary-value">
                            {{ $unresolvedCount ?? 0 }}
                        </div>
                    </div>

                </div>

            </section>

            @php
            $groupsByCourse =
            $groups->groupBy(
            fn ($group) =>
            $group->course?->name ?? 'Ders'
            );
            @endphp

            @forelse($groupsByCourse as $courseName => $courseGroups)

            <section class="course-block">

                <div class="course-heading">

                    <h3>
                        {{ $courseName }}
                    </h3>

                    <span>
                        {{ $courseGroups->count() }} grup
                    </span>

                </div>

                @foreach($courseGroups as $group)

                @php
                $placements =
                $group->placements
                ->whereIn('status', [1,2,3])
                ->sortBy(
                fn ($placement) =>
                $placement->student?->first_name
                );

                $studentCount =
                $placements->count();

                $minimum =
                (int) $group->minimum_students;

                $maximum =
                $group->maximum_students;

                $belowMinimum =
                $studentCount < $minimum;

                    $full=$maximum !==null
                    &&
                    $studentCount>= $maximum;

                    $status =
                    (int) $group->status;

                    $confirmed =
                    $group->confirmed_at !== null;
                    @endphp

                    <article
                        class="group-block {{ $status === 4 ? 'closed' : '' }}">

                        <div class="group-header">

                            <div>

                                <div class="group-title">
                                    Grup {{ $group->group_number }}
                                </div>

                                <div class="group-meta">

                                    @if($group->moduleGroup)
                                    {{ $group->moduleGroup->name }}
                                    ·
                                    @endif

                                    @if($group->module)
                                    Modül {{ $group->module->module_number }}
                                    ·
                                    @endif

                                    {{ $group->weekly_hours }} saat

                                </div>

                            </div>

                            <div class="group-status">

                                @if($status === 1)

                                <span class="badge badge-draft">
                                    Taslak
                                </span>

                                @elseif($status === 2)

                                <span class="badge badge-active">
                                    Aktif
                                </span>

                                @elseif($status === 4)

                                <span class="badge badge-closed">
                                    Kapalı
                                </span>

                                @endif

                                @if($belowMinimum)

                                <span class="badge badge-warning">
                                    Minimum altı
                                </span>

                                @else

                                <span class="badge badge-ok">
                                    Minimum sağlandı
                                </span>

                                @endif
                                @if($confirmed)

                                <span class="badge badge-closed">
                                    🔒 Kesinleştirildi
                                </span>

                                @endif

                            </div>

                        </div>

                        <div class="group-body">

                            <div class="stats">

                                <div class="stat">
                                    <div class="stat-label">
                                        ÖĞRENCİ
                                    </div>

                                    <div class="stat-value">
                                        {{ $studentCount }}
                                    </div>
                                </div>

                                <div class="stat">
                                    <div class="stat-label">
                                        MİNİMUM
                                    </div>

                                    <div class="stat-value">
                                        {{ $minimum }}
                                    </div>
                                </div>

                                <div class="stat">
                                    <div class="stat-label">
                                        MAKSİMUM
                                    </div>

                                    <div class="stat-value">
                                        {{ $maximum ?? '—' }}
                                    </div>
                                </div>

                                <div class="stat">
                                    <div class="stat-label">
                                        DURUM
                                    </div>

                                    <div class="stat-value">

                                        @if($full)
                                        Dolu
                                        @elseif($belowMinimum)
                                        Eksik
                                        @else
                                        Açılabilir
                                        @endif

                                    </div>
                                </div>

                                <div class="stat">
                                    <div class="stat-label">
                                        OLUŞTURMA
                                    </div>

                                    <div class="stat-value">
                                        {{ $group->auto_created ? 'Otomatik' : 'Manuel' }}
                                    </div>
                                </div>

                            </div>

                            <div class="student-list">

                                @forelse($placements as $placement)

                                @php
                                $selection =
                                $placement->selection;

                                $preferenceOrder =
                                $selection?->preference_order;

                                $notes =
                                (string) $placement->notes;

                                $isFallback =
                                str_contains(
                                $notes,
                                'aynı kategori'
                                );

                                $isAutomatic =
                                str_contains(
                                $notes,
                                'otomatik'
                                );
                                @endphp

                                <div class="student">

                                    <div class="student-main">

                                        <div>

                                            <div class="student-name">

                                                <span class="student-number">
                                                    {{ $loop->iteration }}.
                                                </span>

                                                {{ $placement->student?->first_name }}
                                                {{ $placement->student?->last_name }}

                                            </div>

                                            <div class="student-meta">

                                                @if($preferenceOrder === 1)

                                                <span class="badge badge-primary-pref">
                                                    1. tercih
                                                </span>

                                                @elseif($preferenceOrder === 2)

                                                <span class="badge badge-pref2">
                                                    2. tercih
                                                </span>

                                                @elseif($preferenceOrder === 3)

                                                <span class="badge badge-pref3">
                                                    3. tercih
                                                </span>

                                                @endif

                                                @if($isFallback)

                                                <span class="badge badge-fallback">
                                                    Otomatik alternatif
                                                </span>

                                                @elseif(
                                                str_contains(
                                                $notes,
                                                'manuel olarak değiştirildi'
                                                )
                                                )

                                                <span class="badge badge-manual">
                                                    Manuel değiştirildi
                                                </span>

                                                @elseif($preferenceOrder === 2)

                                                <span class="badge badge-auto">
                                                    2. tercihten
                                                </span>

                                                @elseif($preferenceOrder === 3)

                                                <span class="badge badge-auto">
                                                    3. tercihten
                                                </span>

                                                @else

                                                <span class="badge badge-primary-pref">
                                                    1. tercihten
                                                </span>

                                                @endif

                                            </div>

                                        </div>

                                        @if($status !== 4 && (int) $placement->status !== 3)

                                        <button
                                            type="button"
                                            class="button button-secondary button-small"
                                            onclick="toggleMoveBox({{ $placement->id }})">
                                            Taşı
                                        </button>

                                        @elseif((int) $placement->status === 3)

                                        <span class="badge badge-closed">
                                            🔒 Kesinleşmiş
                                        </span>

                                        @endif

                                    </div>

                                    @if($isFallback)

                                    <div style="
                                            margin-top:7px;
                                            color:#c2410c;
                                            font-size:11px;
                                        ">
                                        İlk üç tercihte uygun grup oluşmadığı
                                        için aynı kategorideki mevcut gruba
                                        otomatik aktarıldı.
                                    </div>

                                    @endif

                                    <div
                                        class="move-box"
                                        id="move-box-{{ $placement->id }}"
                                        style="display:none;">

                                        <form
                                            method="POST"
                                            action="{{ route(
                                                'admin.student-course-groups.move-student',
                                                $placement
                                            ) }}">
                                            @csrf

                                            <div class="field">

                                                <label>
                                                    YENİ GRUP
                                                </label>

                                                <select
                                                    name="target_group_id"
                                                    required>

                                                    <option value="">
                                                        Uygun grup seçiniz
                                                    </option>

                                                    @foreach($activeGroups as $targetGroup)

                                                    @if($targetGroup->id !== $group->id)

                                                    @php
                                                    $targetCount =
                                                    $targetGroup
                                                    ->placements
                                                    ->whereIn(
                                                    'status',
                                                    [1,2,3]
                                                    )
                                                    ->count();
                                                    @endphp

                                                    <option
                                                        value="{{ $targetGroup->id }}"
                                                        {{ $targetCount >= ($targetGroup->maximum_students ?? PHP_INT_MAX) ? 'disabled' : '' }}>
                                                        {{ $targetGroup->course?->name }}
                                                        -
                                                        Grup {{ $targetGroup->group_number }}
                                                        -
                                                        {{ $targetGroup->weekly_hours }} saat
                                                        @if($targetGroup->moduleGroup)
                                                        -
                                                        {{ $targetGroup->moduleGroup->name }}
                                                        @endif
                                                        ({{ $targetCount }}/{{ $targetGroup->maximum_students ?? '∞' }})
                                                    </option>

                                                    @endif

                                                    @endforeach

                                                </select>

                                            </div>

                                            <button
                                                type="submit"
                                                class="button button-primary button-small"
                                                style="margin-top:7px;">
                                                Öğrenciyi Taşı
                                            </button>

                                        </form>

                                    </div>

                                </div>

                                @empty

                                <div style="
                                    grid-column:1/-1;
                                    color:#64748b;
                                    padding:10px 0;
                                ">
                                    Bu grupta öğrenci bulunmuyor.
                                </div>

                                @endforelse

                            </div>

                            <div class="actions">

                                <form
                                    method="POST"
                                    action="{{ route(
                                    'admin.student-course-groups.notes',
                                    $group
                                ) }}">
                                    @csrf
                                    @method('PUT')

                                    <textarea
                                        name="notes"
                                        rows="2"
                                        placeholder="Grup notu...">{{ $group->notes }}</textarea>

                                    <button
                                        type="submit"
                                        class="button button-secondary"
                                        style="margin-top:7px;">
                                        Notu Kaydet
                                    </button>
                                </form>

                                @if(!$confirmed)

                                <form
                                    method="POST"
                                    action="{{ route(
                                        'admin.student-course-groups.status',
                                        $group
                                        ) }}">
                                    @csrf
                                    @method('PUT')

                                    <div class="field">
                                        <label>
                                            GRUP DURUMU
                                        </label>

                                        <select name="status">
                                            <option
                                                value="1"
                                                {{ $status === 1 ? 'selected' : '' }}>
                                                Taslak
                                            </option>

                                            <option
                                                value="2"
                                                {{ $status === 2 ? 'selected' : '' }}>
                                                Aktif
                                            </option>

                                            <option
                                                value="4"
                                                {{ $status === 4 ? 'selected' : '' }}>
                                                Kapalı
                                            </option>
                                        </select>
                                    </div>

                                    <button
                                        type="submit"
                                        class="button button-primary"
                                        style="margin-top:7px;width:100%;">
                                        Durumu Kaydet
                                    </button>
                                </form>

                                @else

                                <div>
                                    <span class="badge badge-closed">
                                        🔒 Durum kilitli
                                    </span>
                                </div>

                                @endif
                                @csrf
                                @method('PUT')

                                <div class="field">
                                    <label>
                                        GRUP DURUMU
                                    </label>

                                    <select name="status">

                                        <option
                                            value="1"
                                            {{ $status === 1 ? 'selected' : '' }}>
                                            Taslak
                                        </option>

                                        <option
                                            value="2"
                                            {{ $status === 2 ? 'selected' : '' }}>
                                            Aktif
                                        </option>

                                        <option
                                            value="4"
                                            {{ $status === 4 ? 'selected' : '' }}>
                                            Kapalı
                                        </option>

                                    </select>

                                </div>

                                <button
                                    type="submit"
                                    class="button button-primary"
                                    style="margin-top:7px;width:100%;">
                                    Durumu Kaydet
                                </button>

                                </form>

                                <div>

                                    @if($confirmed)

                                    <span class="badge badge-closed">
                                        🔒 Grup kesinleştirildi
                                    </span>

                                    @elseif($status !== 4)

                                    <button
                                        type="button"
                                        class="button button-danger"
                                        style="width:100%;"
                                        onclick="openCloseModal({{ $group->id }}, {{ $studentCount }})">
                                        Kapat ve Yeniden Dağıt
                                    </button>

                                    @else

                                    <span class="badge badge-closed">
                                        Grup kapalı
                                    </span>

                                    @endif

                                </div>

                            </div>

                        </div>

                    </article>

                    <form
                        id="close-form-{{ $group->id }}"
                        method="POST"
                        action="{{ route(
                        'admin.student-course-groups.close-redistribute',
                        $group
                    ) }}"
                        style="display:none;">
                        @csrf
                    </form>

                    @endforeach

            </section>

            @empty

            <div class="empty">
                Henüz grup oluşturulmadı.
                Yukarıdaki
                <strong>Grupları Oluştur</strong>
                düğmesini kullanabilirsiniz.
            </div>

            @endforelse

    </main>

    <div
        class="modal-backdrop"
        id="closeModal">
        <div class="modal">

            <h3>
                Grubu kapat?
            </h3>

            <p id="closeModalText">
                Bu işlem grubun öğrencilerini yeniden dağıtmayı
                deneyecek.
            </p>

            <div class="modal-actions">

                <button
                    type="button"
                    class="button button-secondary"
                    onclick="closeModal()">
                    İptal
                </button>

                <button
                    type="button"
                    class="button button-danger"
                    id="confirmCloseButton">
                    Kapat ve Dağıt
                </button>

            </div>

        </div>
    </div>

    <script>
        function toggleMoveBox(placementId) {
            const box =
                document.getElementById(
                    `move-box-${placementId}`
                );

            if (!box) {
                return;
            }

            box.style.display =
                box.style.display === 'none' ?
                'block' :
                'none';
        }

        let pendingCloseGroupId = null;

        function openCloseModal(
            groupId,
            studentCount
        ) {
            pendingCloseGroupId =
                groupId;

            const text =
                document.getElementById(
                    'closeModalText'
                );

            text.textContent =
                `${studentCount} öğrencinin uygun gruplara yeniden dağıtılması denenecek. ` +
                `Tüm öğrenciler taşınamazsa grup kapatılmayacaktır.`;

            document
                .getElementById('closeModal')
                .classList.add('open');
        }

        function closeModal() {
            pendingCloseGroupId = null;

            document
                .getElementById('closeModal')
                .classList.remove('open');
        }

        document
            .getElementById('confirmCloseButton')
            .addEventListener(
                'click',
                function() {

                    if (!pendingCloseGroupId) {
                        return;
                    }

                    const form =
                        document.getElementById(
                            `close-form-${pendingCloseGroupId}`
                        );

                    if (!form) {
                        return;
                    }

                    form.submit();
                }
            );
    </script>

</body>

</html>