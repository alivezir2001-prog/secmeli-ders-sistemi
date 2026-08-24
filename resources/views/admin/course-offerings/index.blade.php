<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Ders Kontenjanları - Yönetim</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: #f1f5f9;
            color: #0f172a;
            font-family: Arial, sans-serif;
        }

        .header {
            background: #0f172a;
            color: white;
            padding: 22px 32px;
        }

        .header h1 {
            margin: 0;
        }

        .header p {
            margin: 6px 0 0;
            color: #cbd5e1;
        }

        .container {
            max-width: 1400px;
            margin: 30px auto;
            padding: 0 20px 60px;
        }

        .back {
            display: inline-block;
            margin-bottom: 20px;
            color: #245b91;
            text-decoration: none;
            font-weight: 700;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: end;
            gap: 20px;
            margin-bottom: 24px;
        }

        .topbar h2 {
            margin: 0 0 6px;
        }

        .topbar p {
            margin: 0;
            color: #64748b;
        }

        .year-select {
            padding: 10px 12px;
            border: 1px solid #cbd5e1;
            border-radius: 9px;
            background: white;
            min-width: 180px;
        }

        .success {
            background: #ecfdf5;
            border: 1px solid #bbf7d0;
            color: #166534;
            padding: 14px 16px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-weight: 600;
        }

        .error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
            padding: 14px 16px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .course-card {
            background: white;
            border-radius: 16px;
            padding: 22px;
            margin-bottom: 22px;
            box-shadow: 0 4px 15px rgba(0,0,0,.05);
        }

        .course-header {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            align-items: flex-start;
            margin-bottom: 18px;
        }

        .course-name {
            font-size: 21px;
            font-weight: 800;
        }

        .category {
            margin-top: 5px;
            color: #64748b;
            font-size: 13px;
        }

        .badges {
            display: flex;
            flex-wrap: wrap;
            gap: 7px;
        }

        .badge {
            padding: 6px 10px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 700;
        }

        .badge-green {
            background: #dcfce7;
            color: #166534;
        }

        .badge-red {
            background: #fee2e2;
            color: #991b1b;
        }

        .badge-blue {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .badge-gray {
            background: #e2e8f0;
            color: #475569;
        }

        .group {
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            margin-top: 14px;
            overflow: hidden;
            background: #f8fafc;
        }

        .group-header {
            padding: 14px 16px;
            background: white;
            border-bottom: 1px solid #e2e8f0;
        }

        .group-name {
            font-size: 16px;
            font-weight: 800;
        }

        .module {
            margin: 12px;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            background: white;
            overflow: hidden;
        }

        .module-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            padding: 12px 14px;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
        }

        .module-name {
            font-weight: 800;
        }

        .option-list {
            padding: 10px;
            display: grid;
            gap: 9px;
        }

        .option-card {
            border: 1px solid #e2e8f0;
            border-radius: 11px;
            padding: 12px;
            background: white;
        }

        .option-card.open {
            border-left: 4px solid #16a34a;
        }

        .option-card.closed {
            border-left: 4px solid #cbd5e1;
        }

        .option-main {
            display: grid;
            grid-template-columns: 150px 120px 120px 1fr;
            gap: 14px;
            align-items: center;
        }

        .option-title {
            font-weight: 800;
        }

        .option-subtitle {
            color: #64748b;
            font-size: 12px;
            margin-top: 4px;
        }

        .stat-label {
            font-size: 11px;
            color: #64748b;
            margin-bottom: 4px;
        }

        .stat-value {
            font-size: 18px;
            font-weight: 800;
        }

        .status-right {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .button {
            border: 0;
            border-radius: 9px;
            padding: 9px 14px;
            background: #245b91;
            color: white;
            font-weight: 700;
            cursor: pointer;
        }

        .button-secondary {
            background: #e2e8f0;
            color: #334155;
        }

        .button-danger {
            background: #b91c1c;
        }

        .settings {
            display: none;
            margin-top: 12px;
            padding-top: 12px;
            border-top: 1px solid #e2e8f0;
        }

        .settings.open {
            display: block;
        }

        .settings-grid {
            display: grid;
            grid-template-columns: 170px 170px 190px 160px 1fr;
            gap: 12px;
            align-items: end;
        }

        .field label {
            display: block;
            font-size: 11px;
            font-weight: 700;
            color: #475569;
            margin-bottom: 5px;
        }

        .field input {
            width: 100%;
            padding: 9px 10px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            background: white;
        }

        .info-box {
            background: #eff6ff;
            color: #1d4ed8;
            border-radius: 9px;
            padding: 9px 10px;
            font-size: 12px;
            font-weight: 700;
        }

        .empty {
            padding: 18px;
            color: #64748b;
            text-align: center;
        }

        .note {
            margin-top: 12px;
            padding: 12px;
            border-radius: 10px;
            background: #f8fafc;
            color: #64748b;
            font-size: 12px;
        }

        @media (max-width: 1100px) {
            .option-main,
            .settings-grid {
                grid-template-columns: 1fr 1fr;
            }

            .status-right {
                justify-content: flex-start;
            }
        }

        @media (max-width: 700px) {
            .topbar,
            .course-header,
            .module-header {
                flex-direction: column;
                align-items: stretch;
            }

            .option-main,
            .settings-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

<header class="header">
    <h1>Seçmeli Ders Sistemi</h1>
    <p>Yönetim → Ders Kontenjanları</p>
</header>

<main class="container">

    <a
        href="{{ route('admin.dashboard') }}"
        class="back"
    >
        ← Yönetim Paneli
    </a>

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
        <div>
            <h2>Ders Kontenjanları</h2>
            <p>{{ $academicYear->name }} eğitim yılı</p>
        </div>

        <form method="GET">

            <select
                name="academic_year_id"
                class="year-select"
                onchange="this.form.submit()"
            >
                @foreach($academicYears as $year)

                    <option
                        value="{{ $year->id }}"
                        {{ (int) $year->id === (int) $academicYear->id ? 'selected' : '' }}
                    >
                        {{ $year->name }}
                        {{ $year->active ? ' (Aktif)' : '' }}
                    </option>

                @endforeach
            </select>

        </form>
    </div>

    @foreach($courses as $course)

        <section class="course-card">

            <div class="course-header">

                <div>
                    <div class="course-name">
                        {{ $course->name }}
                    </div>

                    <div class="category">
                        {{ $course->category->name }}
                    </div>
                </div>

                <div class="badges">

                    @if($course->is_modular)
                        <span class="badge badge-blue">
                            {{ $course->max_selections }} modül
                        </span>

                        <span class="badge badge-green">
                            Modüler
                        </span>
                    @else
                        <span class="badge badge-gray">
                            Tek aşamalı
                        </span>
                    @endif

                </div>

            </div>

            @if($course->is_modular)

                @forelse($course->moduleGroups as $group)

                    <div class="group">

                        <div class="group-header">

                            <div class="group-name">
                                {{ $group->name }}
                            </div>

                            <div style="margin-top:6px;">

                                @if($group->active)
                                    <span class="badge badge-green">
                                        Program aktif
                                    </span>
                                @else
                                    <span class="badge badge-red">
                                        Program pasif
                                    </span>
                                @endif

                                <span class="badge badge-gray">
                                    {{ $group->modules->count() }} modül
                                </span>

                            </div>

                        </div>

                        @foreach($group->modules as $module)

                            <div class="module">

                                <div class="module-header">

                                    <div class="module-name">
                                        {{ $module->name }}
                                    </div>

                                    <span class="badge badge-gray">
                                        {{ $module->hourOptions->count() }}
                                        saat seçeneği
                                    </span>

                                </div>

                                <div class="option-list">

                                    @forelse($module->hourOptions as $hourOption)

                                        @php
                                            $count =
                                                (int) (
                                                    $selectionCounts[
                                                        $module->id . ':' . $hourOption->weekly_hours
                                                    ]->total
                                                    ?? 0
                                                );

                                            $offering =
                                                $course->offerings->first(
                                                    fn ($item) =>
                                                        (int) $item->course_module_id === (int) $module->id
                                                        &&
                                                        (int) $item->weekly_hours === (int) $hourOption->weekly_hours
                                                );

                                            $isOpen =
                                                $offering?->active ?? false;

                                            $maximum =
                                                $offering?->maximum_students;

                                            $isFull =
                                                $maximum !== null
                                                && $count >= $maximum;
                                        @endphp

                                        <div
                                            class="option-card {{ $isOpen ? 'open' : 'closed' }}"
                                        >

                                            <div class="option-main">

                                                <div>
                                                    <div class="option-title">
                                                        {{ $hourOption->weekly_hours }}
                                                        ders saati
                                                    </div>

                                                    <div class="option-subtitle">
                                                        Modül {{ $module->module_number }}
                                                    </div>
                                                </div>

                                                <div>
                                                    <div class="stat-label">
                                                        Tercih eden
                                                    </div>

                                                    <div class="stat-value">
                                                        {{ $count }}
                                                    </div>
                                                </div>

                                                <div>
                                                    <div class="stat-label">
                                                        Sistem minimumu
                                                    </div>

                                                    <div class="stat-value">
                                                        10
                                                    </div>
                                                </div>

                                                <div class="status-right">

                                                    @if($isOpen)

                                                        @if($isFull)

                                                            <span class="badge badge-red">
                                                                Kontenjan dolu
                                                            </span>

                                                        @elseif($count >= 10)

                                                            <span class="badge badge-green">
                                                                Açılabilir
                                                            </span>

                                                        @else

                                                            <span class="badge badge-gray">
                                                                10 öğrenci bekleniyor
                                                            </span>

                                                        @endif

                                                    @else

                                                        <span class="badge badge-gray">
                                                            Açılmamış
                                                        </span>

                                                    @endif

                                                    <button
                                                        type="button"
                                                        class="button button-secondary"
                                                        onclick="document.getElementById('settings-{{ $module->id }}-{{ $hourOption->weekly_hours }}').classList.toggle('open')"
                                                    >
                                                        {{ $isOpen ? 'Ayarlar' : 'Aç' }}
                                                    </button>

                                                </div>

                                            </div>

                                            <div
                                                id="settings-{{ $module->id }}-{{ $hourOption->weekly_hours }}"
                                                class="settings"
                                            >

                                                <form
                                                    method="POST"
                                                    action="{{ route(
                                                        'admin.course-offerings.update',
                                                        $course
                                                    ) }}"
                                                >
                                                    @csrf
                                                    @method('PUT')

                                                    <input
                                                        type="hidden"
                                                        name="academic_year_id"
                                                        value="{{ $academicYear->id }}"
                                                    >

                                                    <input
                                                        type="hidden"
                                                        name="course_module_id"
                                                        value="{{ $module->id }}"
                                                    >

                                                    <input
                                                        type="hidden"
                                                        name="weekly_hours"
                                                        value="{{ $hourOption->weekly_hours }}"
                                                    >

                                                    <div class="settings-grid">

                                                        <div>
                                                            <div class="field">
                                                                <label>
                                                                    Modül
                                                                </label>

                                                                <div class="info-box">
                                                                    {{ $module->name }}
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div>
                                                            <div class="field">
                                                                <label>
                                                                    Haftalık saat
                                                                </label>

                                                                <div class="info-box">
                                                                    {{ $hourOption->weekly_hours }}
                                                                    saat
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="field">
                                                            <label>
                                                                Maksimum öğrenci
                                                            </label>

                                                            <input
                                                                type="number"
                                                                name="maximum_students"
                                                                min="1"
                                                                value="{{ $offering?->maximum_students }}"
                                                                placeholder="Sınırsız"
                                                            >
                                                        </div>

                                                        <div class="field">
                                                            <label>
                                                                Maksimum sınıf
                                                            </label>

                                                            <input
                                                                type="number"
                                                                name="maximum_classes"
                                                                min="1"
                                                                max="20"
                                                                value="{{ $offering?->maximum_classes ?? 1 }}"
                                                            >
                                                        </div>

                                                        <div class="field">

                                                            <label>
                                                                Birden fazla sınıf
                                                            </label>

                                                            <label style="
                                                                display:flex;
                                                                align-items:center;
                                                                gap:8px;
                                                                min-height:38px;
                                                            ">

                                                                <input
                                                                    type="checkbox"
                                                                    name="allow_multiple_classes"
                                                                    value="1"
                                                                    {{ $offering?->allow_multiple_classes ? 'checked' : '' }}
                                                                >

                                                                İzin ver
                                                            </label>

                                                        </div>

                                                    </div>

                                                    <div class="settings-grid" style="margin-top:12px;">

                                                        <div class="field">

                                                            <label>
                                                                Durum
                                                            </label>

                                                            <select name="active">

                                                                <option
                                                                    value="1"
                                                                    {{ $isOpen ? 'selected' : '' }}
                                                                >
                                                                    Açık
                                                                </option>

                                                                <option
                                                                    value="0"
                                                                    {{ !$isOpen ? 'selected' : '' }}
                                                                >
                                                                    Kapalı
                                                                </option>

                                                            </select>

                                                        </div>

                                                        <div>
                                                            <div class="info-box">
                                                                Minimum öğrenci:
                                                                <strong>10</strong>
                                                            </div>
                                                        </div>

                                                        <div></div>
                                                        <div></div>

                                                        <div class="status-right">

                                                            <button
                                                                type="submit"
                                                                class="button"
                                                            >
                                                                Kaydet
                                                            </button>

                                                        </div>

                                                    </div>

                                                </form>

                                            </div>

                                        </div>

                                    @empty

                                        <div class="empty">
                                            Bu modül için tanımlanmış haftalık saat seçeneği yok.
                                        </div>

                                    @endforelse

                                </div>

                            </div>

                        @endforeach

                    </div>

                @empty

                    <div class="empty">
                        Bu ders için aktif program/modül grubu bulunmuyor.
                    </div>

                @endforelse

            @else

                <div class="note">
                    Bu ders tek aşamalıdır. Modül bazlı kontenjan tanımı
                    kullanılmayacaktır.
                </div>

            @endif

        </section>

    @endforeach

</main>

</body>
</html>