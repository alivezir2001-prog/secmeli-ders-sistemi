<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dersler - Yönetim</title>

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
            max-width: 1350px;
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

        .button {
            border: 0;
            border-radius: 9px;
            padding: 10px 15px;
            background: #245b91;
            color: white;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }

        .button-secondary {
            background: #e2e8f0;
            color: #334155;
        }

        .button-danger {
            background: #b91c1c;
        }

        .search,
        .new-course {
            background: white;
            padding: 18px;
            border-radius: 14px;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,.05);
        }

        .search form {
            display: flex;
            gap: 10px;
        }

        .search input {
            flex: 1;
            padding: 11px 12px;
            border: 1px solid #cbd5e1;
            border-radius: 9px;
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
            margin-bottom: 18px;
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
            font-size: 20px;
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

        .section-title {
            margin: 24px 0 12px;
            font-size: 15px;
            font-weight: 800;
        }

        .new-course-grid,
        .course-edit-grid {
            display: grid;
            grid-template-columns: 1.4fr 1fr 1fr;
            gap: 14px;
        }

        .field label,
        .new-course > form > div > label,
        .new-course-grid > div > label,
        .course-edit-grid > div > label {
            display: block;
            font-size: 12px;
            color: #475569;
            font-weight: 700;
            margin-bottom: 6px;
        }

        .field input,
        .field select,
        .new-course input,
        .new-course select,
        .course-card input[type="text"],
        .course-card input[type="number"],
        .course-card select {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #cbd5e1;
            border-radius: 9px;
            background: white;
        }

        .check-row {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-top: 15px;
        }

        .check-row label {
            display: flex;
            align-items: center;
            gap: 7px;
            font-size: 13px;
            font-weight: 700;
        }

        .grade-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
        }

        .grade-card {
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 14px;
            background: #f8fafc;
        }

        .grade-title {
            font-weight: 800;
            margin-bottom: 10px;
        }

        .hours {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .hours label {
            display: flex;
            align-items: center;
            gap: 5px;
            background: white;
            padding: 7px 9px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            font-size: 12px;
        }

        .actions {
            display: flex;
            justify-content: flex-end;
            gap: 8px;
            margin-top: 16px;
        }

        .groups {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .group-card {
            border: 1px solid #e2e8f0;
            border-radius: 13px;
            background: #f8fafc;
            overflow: hidden;
        }

        .group-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            padding: 15px 16px;
            background: white;
            border-bottom: 1px solid #e2e8f0;
        }

        .group-name {
            font-size: 16px;
            font-weight: 800;
        }

        .group-actions {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .module-list {
            padding: 12px;
            display: grid;
            gap: 8px;
        }

        .module-row {
            display: grid;
            grid-template-columns: 90px 1fr 160px 100px;
            gap: 10px;
            align-items: center;
            padding: 10px 12px;
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
        }

        .module-number {
            font-weight: 800;
        }

        .module-name {
            font-weight: 700;
        }

        .module-status {
            text-align: right;
        }

        .module-save {
            display: flex;
            justify-content: flex-end;
        }

        .new-group {
            margin-top: 14px;
            padding: 14px;
            background: white;
            border: 1px dashed #cbd5e1;
            border-radius: 11px;
        }

        .new-group-form {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 10px;
        }

        .module-count {
            padding: 10px 12px;
            background: #eff6ff;
            color: #1d4ed8;
            border-radius: 9px;
            font-size: 12px;
            font-weight: 700;
            margin-bottom: 12px;
        }

        .muted {
            color: #64748b;
            font-size: 13px;
        }

        @media (max-width: 1000px) {
            .new-course-grid,
            .course-edit-grid {
                grid-template-columns: 1fr 1fr;
            }

            .grade-grid {
                grid-template-columns: 1fr 1fr;
            }

            .module-row {
                grid-template-columns: 70px 1fr 120px;
            }

            .module-save {
                grid-column: 1 / -1;
            }
        }

        @media (max-width: 700px) {
            .topbar,
            .course-header,
            .search form,
            .new-group-form {
                flex-direction: column;
                align-items: stretch;
            }

            .new-course-grid,
            .course-edit-grid,
            .grade-grid {
                grid-template-columns: 1fr;
            }

            .group-header {
                flex-direction: column;
                align-items: stretch;
            }

            .module-row {
                grid-template-columns: 1fr;
            }

            .module-status {
                text-align: left;
            }

            .module-save {
                justify-content: flex-start;
            }
        }
    </style>
</head>

<body>

<header class="header">
    <h1>Seçmeli Ders Sistemi</h1>
    <p>Yönetim → Dersler</p>
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
            <h2>Ders Kataloğu</h2>
            <p>
                Ders, sınıf/saat ve program-modül tanımlarını yönetin.
            </p>
        </div>
    </div>

    {{-- YENİ DERS --}}
    <section class="new-course">

        <h3>Yeni Ders Ekle</h3>

        <form
            method="POST"
            action="{{ route('admin.courses.store') }}"
        >
            @csrf

            <div class="new-course-grid">

                <div>
                    <label>Ders adı</label>

                    <input
                        type="text"
                        name="name"
                        required
                    >
                </div>

                <div>
                    <label>Grup</label>

                    <select
                        name="course_category_id"
                        required
                    >
                        <option value="">
                            Grup seçiniz
                        </option>

                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label>Seçim/modül sayısı</label>

                    <input
                        type="number"
                        name="max_selections"
                        value="1"
                        min="1"
                        max="10"
                    >

                    <div class="muted" style="margin-top:5px;">
                        Bakanlık kuralına göre tanımlanmalıdır.
                    </div>
                </div>

            </div>

            <div class="check-row">

                <label>
                    <input
                        type="checkbox"
                        name="active"
                        value="1"
                        checked
                    >
                    Aktif
                </label>

                <label>
                    <input
                        type="checkbox"
                        name="offered"
                        value="1"
                        checked
                    >
                    Öğrenci seçimine açık
                </label>

                <label>
                    <input
                        type="checkbox"
                        name="is_modular"
                        value="1"
                    >
                    Modüler
                </label>

            </div>

            <div class="section-title">
                Sınıf / Saat seçenekleri
            </div>

            <div class="grade-grid">

                @for($grade = 5; $grade <= 8; $grade++)

                    <div class="grade-card">

                        <div class="grade-title">
                            {{ $grade }}. sınıf
                        </div>

                        <div class="hours">

                            @foreach([1, 2] as $hours)

                                <label>

                                    <input
                                        type="checkbox"
                                        name="grade_options[{{ $grade }}][]"
                                        value="{{ $hours }}"
                                    >

                                    {{ $hours }} saat

                                </label>

                            @endforeach

                        </div>

                    </div>

                @endfor

            </div>

            <div class="actions">

                <button
                    type="submit"
                    class="button"
                >
                    Dersi Oluştur
                </button>

            </div>

        </form>

    </section>

    {{-- ARAMA --}}
    <section class="search">

        <form method="GET">

            <input
                type="text"
                name="search"
                value="{{ $search }}"
                placeholder="Ders ara..."
            >

            <button
                type="submit"
                class="button"
            >
                Ara
            </button>

            <a
                href="{{ route('admin.courses.index') }}"
                class="button button-secondary"
            >
                Temizle
            </a>

        </form>

    </section>

    {{-- DERSLER --}}
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

                    <span class="badge badge-blue">
                        {{ $course->category->name }}
                    </span>

                    @if($course->active)
                        <span class="badge badge-green">
                            Aktif
                        </span>
                    @else
                        <span class="badge badge-red">
                            Pasif
                        </span>
                    @endif

                    @if($course->offered)
                        <span class="badge badge-green">
                            Seçime açık
                        </span>
                    @else
                        <span class="badge badge-red">
                            Seçime kapalı
                        </span>
                    @endif

                    @if($course->is_modular)
                        <span class="badge badge-blue">
                            Modüler
                        </span>
                    @else
                        <span class="badge badge-gray">
                            Modüler değil
                        </span>
                    @endif

                </div>

            </div>

            {{-- DERS TEMEL BİLGİLERİ --}}
            <form
                method="POST"
                action="{{ route('admin.courses.update', $course) }}"
            >
                @csrf
                @method('PUT')

                <div class="course-edit-grid">

                    <div>
                        <label>Ders adı</label>

                        <input
                            type="text"
                            name="name"
                            value="{{ $course->name }}"
                            required
                        >
                    </div>

                    <div>
                        <label>Grup</label>

                        <select
                            name="course_category_id"
                            required
                        >

                            @foreach($categories as $category)

                                <option
                                    value="{{ $category->id }}"
                                    {{ (int) $course->course_category_id === (int) $category->id ? 'selected' : '' }}
                                >
                                    {{ $category->name }}
                                </option>

                            @endforeach

                        </select>
                    </div>

                    <div>
                        <label>Modül / seçim sayısı</label>

                        <input
                            type="number"
                            value="{{ $course->max_selections }}"
                            disabled
                        >

                        <div class="muted" style="margin-top:5px;">
                            Bakanlık tarafından belirlenir.
                        </div>
                    </div>

                </div>

                <div class="check-row">

                    <label>
                        <input
                            type="checkbox"
                            name="active"
                            value="1"
                            {{ $course->active ? 'checked' : '' }}
                        >
                        Aktif
                    </label>

                    <label>
                        <input
                            type="checkbox"
                            name="offered"
                            value="1"
                            {{ $course->offered ? 'checked' : '' }}
                        >
                        Öğrenci seçimine açık
                    </label>

                    <label>
                        <input
                            type="checkbox"
                            name="is_modular"
                            value="1"
                            {{ $course->is_modular ? 'checked' : '' }}
                        >
                        Modüler
                    </label>

                </div>

                <div class="section-title">
                    Sınıf / Saat seçenekleri
                </div>

                <div class="grade-grid">

                    @for($grade = 5; $grade <= 8; $grade++)

                        <div class="grade-card">

                            <div class="grade-title">
                                {{ $grade }}. sınıf
                            </div>

                            <div class="hours">

                                @foreach([1, 2] as $hours)

                                    @php

                                        $option = $course->gradeOptions
                                            ->first(
                                                fn ($item) =>
                                                    (int) $item->grade === $grade
                                                    &&
                                                    (int) $item->weekly_hours === $hours
                                            );

                                        $checked =
                                            $option?->active ?? false;

                                    @endphp

                                    <label>

                                        <input
                                            type="checkbox"
                                            name="grade_options[{{ $grade }}][]"
                                            value="{{ $hours }}"
                                            {{ $checked ? 'checked' : '' }}
                                        >

                                        {{ $hours }} saat

                                    </label>

                                @endforeach

                            </div>

                        </div>

                    @endfor

                </div>

                <div class="actions">

                    <button
                        type="submit"
                        class="button"
                    >
                        Dersi Kaydet
                    </button>

                </div>

            </form>

            {{-- MODÜL / PROGRAM GRUPLARI --}}
            @if($course->is_modular)

                <div class="section-title">
                    Program / Modül Grupları
                </div>

                <div class="module-count">
                    Bu ders için Bakanlık tarafından belirlenen modül sayısı:
                    {{ $course->max_selections }}
                </div>

                <div class="groups">

                    @forelse($course->moduleGroups as $group)

                        <div class="group-card">

                            <div class="group-header">

                                <div>

                                    <div class="group-name">
                                        {{ $group->name }}
                                    </div>

                                    <div style="margin-top:5px;">

                                        @if($group->active)

                                            <span class="badge badge-green">
                                                Aktif
                                            </span>

                                        @else

                                            <span class="badge badge-red">
                                                Pasif
                                            </span>

                                        @endif

                                        <span class="badge badge-gray">
                                            {{ $group->modules->count() }}
                                            modül
                                        </span>

                                    </div>

                                </div>

                                <div class="group-actions">

                                    <form
                                        method="POST"
                                        action="{{ route(
                                            'admin.courses.module-groups.toggle',
                                            [$course, $group]
                                        ) }}"
                                    >
                                        @csrf
                                        @method('PUT')

                                        <button
                                            type="submit"
                                            class="button {{ $group->active ? 'button-danger' : '' }}"
                                        >
                                            {{ $group->active
                                                ? 'Pasifleştir'
                                                : 'Aktifleştir' }}
                                        </button>

                                    </form>

                                </div>

                            </div>

                            <div class="module-list">

                                @foreach($group->modules as $module)

                                    <form
                                        method="POST"
                                        action="{{ route(
                                            'admin.courses.modules.update',
                                            [$course, $module]
                                        ) }}"
                                        class="module-row"
                                    >
                                        @csrf
                                        @method('PUT')

                                        <div class="module-number">
                                            Modül {{ $module->module_number }}
                                        </div>

                                        <div class="module-name">
                                            {{ $module->name }}
                                        </div>

                                        <div>

                                            <input
                                                type="number"
                                                name="weekly_hours"
                                                value="{{ $module->weekly_hours }}"
                                                min="1"
                                                max="10"
                                                placeholder="Saat"
                                            >

                                        </div>

                                        <div class="module-save">

                                            <button
                                                type="submit"
                                                class="button"
                                            >
                                                Kaydet
                                            </button>

                                        </div>

                                    </form>

                                @endforeach

                            </div>

                        </div>

                    @empty

                        <div class="muted">
                            Henüz bir program/modül grubu tanımlanmamış.
                        </div>

                    @endforelse

                </div>

                {{-- YENİ PROGRAM / MODÜL GRUBU --}}
                <div class="new-group">

                    <div class="section-title" style="margin-top:0;">
                        Yeni Program / Modül Grubu
                    </div>

                    <div class="muted" style="margin-bottom:10px;">
                        Programı eklediğinizde sistem bu ders için
                        {{ $course->max_selections }}
                        modülü otomatik oluşturur.
                    </div>

                    <form
                        method="POST"
                        action="{{ route(
                            'admin.courses.module-groups.store',
                            $course
                        ) }}"
                        class="new-group-form"
                    >
                        @csrf

                        <input
                            type="text"
                            name="name"
                            placeholder="Örn. Piyano, Futbol, Satranç..."
                            required
                        >

                        <button
                            type="submit"
                            class="button"
                        >
                            Programı Ekle
                        </button>

                    </form>

                </div>

            @endif

        </section>

    @endforeach

</main>

</body>
</html>