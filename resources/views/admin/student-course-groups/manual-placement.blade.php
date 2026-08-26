<!DOCTYPE html>
<html lang="tr">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Manuel Yerleştirme</title>

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
            max-width: 1400px;
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

        h2 {
            margin: 0 0 5px;
            font-size: 21px;
        }

        .description {
            margin: 0;
            color: #64748b;
            font-size: 13px;
        }

        .search-area {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        .search-area input {
            flex: 1;
            padding: 10px 12px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
        }

        .button {
            border: 0;
            border-radius: 8px;
            padding: 10px 15px;
            font-weight: 700;
            cursor: pointer;
        }

        .button-primary {
            background: #245b91;
            color: #fff;
        }

        .button-success {
            background: #166534;
            color: #fff;
        }

        .button-muted {
            background: #e2e8f0;
            color: #334155;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
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

        .card {
            background: #fff;
            border: 1px solid #dbe3ec;
            border-radius: 14px;
            overflow: hidden;
            margin-bottom: 18px;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            padding: 15px 18px;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
        }

        .student-name {
            font-weight: 800;
        }

        .student-number {
            margin-top: 3px;
            color: #64748b;
            font-size: 11px;
        }

        .category-name {
            font-size: 12px;
            font-weight: 800;
        }

        .body {
            padding: 18px;
        }

        .grid {
            display: grid;
            grid-template-columns: 1fr 1.4fr;
            gap: 24px;
        }

        .label {
            margin-bottom: 7px;
            color: #64748b;
            font-size: 10px;
            font-weight: 800;
        }

        .preference {
            padding: 9px 11px;
            margin-bottom: 7px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 12px;
        }

        .preference strong {
            color: #64748b;
            margin-right: 5px;
        }

        .current {
            padding: 12px;
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 9px;
            margin-bottom: 16px;
        }

        .group-list {
            display: grid;
            gap: 8px;
        }

        .group {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 15px;
            align-items: center;
            padding: 11px 13px;
            border: 1px solid #e2e8f0;
            border-radius: 9px;
            background: #fff;
        }

        .group:hover {
            background: #f8fafc;
        }

        .group-title {
            font-size: 12px;
            font-weight: 800;
        }

        .group-meta {
            margin-top: 4px;
            color: #64748b;
            font-size: 10px;
        }

        .capacity {
            color: #475569;
            font-size: 10px;
            white-space: nowrap;
        }

        .no-group {
            padding: 15px;
            background: #fff7ed;
            border: 1px solid #fed7aa;
            border-radius: 9px;
            color: #9a3412;
            font-size: 12px;
        }

        .empty {
            background: #fff;
            border: 1px solid #dbe3ec;
            border-radius: 14px;
            padding: 35px;
            text-align: center;
            color: #64748b;
        }

        @media (max-width: 800px) {

            .topbar {
                flex-direction: column;
                align-items: stretch;
            }

            .search-area {
                flex-direction: column;
            }

            .grid {
                grid-template-columns: 1fr;
            }

        }
    </style>

</head>

<body>

    <header class="header">

        <h1>
            Manuel Yerleştirme
        </h1>

        <p>
            {{ $academicYear->name }} Eğitim ve Öğretim Yılı
        </p>

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

            <div>
                {{ $error }}
            </div>

            @endforeach

        </div>

        @endif

        <div class="topbar">

            <div>

                <h2>
                    Manuel Yerleştirme
                </h2>

                <p class="description">
                    Eksik veya düzeltilmesi gereken
                    öğrenci-kategori yerleşimlerini yönetin.
                </p>

            </div>

            <a
                href="{{ route(
                'admin.student-placements.index',
                ['academic_year_id' => $academicYear->id]
            ) }}"
                class="button button-muted">
                Yerleştirme Kontrolüne Dön
            </a>

        </div>

        <form
            method="GET"
            class="search-area">

            <input
                type="hidden"
                name="academic_year_id"
                value="{{ $academicYear->id }}">

            <input
                type="text"
                name="search"
                value="{{ $search }}"
                placeholder="Öğrenci adı, soyadı veya öğrenci no...">

            <button
                type="submit"
                class="button button-primary">
                Ara
            </button>

        </form>

        @forelse($studentCategories as $row)

        @php

        $categoryId =
        $row['category_id'];

        /*
        * Yalnızca aynı kategoriye ait gruplar.
        */
        $groups =
        $activeGroups
        ->filter(
        function ($group) use (
        $categoryId
        ) {
        return
        (int) (
        $group
        ->course
        ?->course_category_id
        )
        ===
        (int) $categoryId;
        }
        );

        @endphp

        <section class="card">

            <div class="card-header">

                <div>

                    <div class="student-name">
                        {{ $row['student']->first_name }}
                        {{ $row['student']->last_name }}
                    </div>

                    <div class="student-number">
                        Öğrenci No:
                        {{ $row['student']->student_number ?? '-' }}
                    </div>

                </div>

                <div class="category-name">
                    {{ $row['category']?->name ?? 'Kategori' }}
                </div>

            </div>

            <div class="body">

                <div class="grid">

                    <div>

                        <div class="label">
                            ÖĞRENCİNİN TERCİHLERİ
                        </div>

                        @foreach($row['selections'] as $selection)

                        <div class="preference">

                            <strong>
                                {{ $selection->preference_order }}.
                            </strong>

                            {{ $selection->course?->name }}

                        </div>

                        @endforeach

                    </div>

                    <div>

                        <div class="label">
                            MEVCUT YERLEŞİM
                        </div>

                        @if($row['placement'])

                        <div class="current">

                            <strong>
                                {{ $row['placement']->course?->name }}
                            </strong>

                            @if($row['placement']->group)

                            <div class="group-meta">

                                Grup
                                {{ $row['placement']->group->group_number }}

                            </div>

                            @endif

                        </div>

                        @else

                        <div class="no-group">
                            Bu öğrenci-kategori için henüz yerleştirme yok.
                        </div>

                        @endif

                        <div class="label">
                            UYGUN HEDEF GRUPLAR
                        </div>

                        <div class="group-list">

                            @forelse($groups as $group)

                            @php

                            $groupCount =
                            $group
                            ->placements()
                            ->whereIn(
                            'status',
                            [1, 2, 3]
                            )
                            ->count();

                            $isFull =
                            $group->maximum_students !== null
                            &&
                            $groupCount >=
                            (int) $group->maximum_students;

                            $isCurrent =
                            $row['placement']
                            &&
                            (int)
                            $row['placement']
                            ->student_course_group_id
                            ===
                            (int) $group->id;

                            @endphp

                            <div class="group">

                                <div>

                                    <div class="group-title">

                                        {{ $group->course?->name }}

                                        ·

                                        Grup
                                        {{ $group->group_number }}

                                    </div>

                                    <div class="group-meta">

                                        @if($group->moduleGroup)

                                        {{ $group->moduleGroup->name }}

                                        @endif

                                        @if($group->module)

                                        ·
                                        {{ $group->module->name }}

                                        @endif

                                        ·
                                        {{ $group->weekly_hours }}
                                        saat

                                    </div>

                                </div>

                                <div>

                                    @if($isCurrent)

                                    <span
                                        class="button button-muted">
                                        Mevcut
                                    </span>

                                    @elseif(!$isFull)

                                    <form
                                        method="POST"
                                        action="{{ route(
                                                    'admin.student-course-groups.manual-place',
                                                    $row['selections']->first()
                                                ) }}">

                                        @csrf

                                        <input
                                            type="hidden"
                                            name="target_group_id"
                                            value="{{ $group->id }}">

                                        <button
                                            type="submit"
                                            class="button button-success"
                                            onclick="
                                                        return confirm(
                                                            'Bu öğrenciyi bu gruba yerleştirmek istediğinize emin misiniz?'
                                                        );
                                                    ">
                                            Yerleştir
                                        </button>

                                    </form>

                                    @else

                                    <span
                                        class="button button-muted">
                                        Dolu
                                    </span>

                                    @endif

                                </div>

                                <div class="capacity">

                                    {{ $groupCount }}
                                    /
                                    {{ $group->maximum_students ?? '∞' }}

                                </div>

                            </div>

                            @empty

                            <div class="no-group">
                                Bu kategori için aktif grup bulunamadı.
                            </div>

                            @endforelse

                        </div>

                    </div>

                </div>

            </div>

        </section>

        @empty

        <div class="empty">
            Manuel yerleştirme gerektiren öğrenci-kategori bulunamadı.
        </div>

        @endforelse

    </main>

</body>

</html>