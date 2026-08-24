<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Öğrenci Yerleştirmeleri</title>

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
            max-width: 1400px;
            margin: 30px auto;
            padding: 0 20px 60px;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            margin-bottom: 22px;
        }

        .year-select {
            padding: 10px 12px;
            border: 1px solid #cbd5e1;
            border-radius: 9px;
            background: white;
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

        .student-card {
            background: white;
            border-radius: 14px;
            margin-bottom: 16px;
            box-shadow: 0 4px 15px rgba(15,23,42,.05);
            overflow: hidden;
        }

        .student-header {
            padding: 16px 18px;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
        }

        .student-name {
            font-size: 17px;
            font-weight: 800;
        }

        .student-meta {
            margin-top: 4px;
            color: #64748b;
            font-size: 13px;
        }

        .status {
            display: inline-flex;
            padding: 6px 10px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 700;
        }

        .status-waiting {
            background: #fef3c7;
            color: #92400e;
        }

        .status-placed {
            background: #dcfce7;
            color: #166534;
        }

        .row {
            padding: 16px 18px;
            border-bottom: 1px solid #e2e8f0;
        }

        .row:last-child {
            border-bottom: 0;
        }

        .row-grid {
            display: grid;
            grid-template-columns: 1.4fr 1fr 130px 1.1fr 180px;
            gap: 14px;
            align-items: center;
        }

        .label {
            color: #64748b;
            font-size: 11px;
            margin-bottom: 4px;
            font-weight: 700;
        }

        .value {
            font-weight: 700;
        }

        .subvalue {
            color: #475569;
            font-size: 13px;
        }

        select,
        textarea {
            width: 100%;
            padding: 9px 10px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            background: white;
        }

        textarea {
            min-height: 70px;
            resize: vertical;
        }

        .button {
            border: 0;
            border-radius: 9px;
            padding: 10px 13px;
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

        .bottom-bar {
            margin-top: 22px;
            background: white;
            border-radius: 14px;
            padding: 18px;
            box-shadow: 0 4px 15px rgba(15,23,42,.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 18px;
        }

        .bottom-info {
            color: #64748b;
            font-size: 13px;
        }

        .confirm-button {
            border: 0;
            border-radius: 10px;
            padding: 13px 20px;
            background: #166534;
            color: white;
            font-weight: 800;
            cursor: pointer;
        }

        .confirm-button:disabled {
            background: #cbd5e1;
            color: #64748b;
            cursor: not-allowed;
        }

        .empty {
            background: white;
            border-radius: 14px;
            padding: 30px;
            text-align: center;
            color: #64748b;
        }

        @media (max-width: 1100px) {
            .row-grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 700px) {
            .topbar,
            .student-header,
            .bottom-bar {
                flex-direction: column;
                align-items: stretch;
            }

            .row-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

<header class="header">
    <h1>Öğrenci Yerleştirmeleri</h1>
    <p>{{ $academicYear->name }} eğitim öğretim yılı</p>
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
        <div>
            <h2 style="margin:0 0 5px;">
                Tercihlerden Yerleştirmeye
            </h2>

            <div style="color:#64748b;font-size:13px;">
                Öğrencinin tercihi değişmez; okul gerçek yerleştirmeyi belirler.
            </div>
        </div>

        <form method="GET">
            <select
                class="year-select"
                name="academic_year_id"
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

    @php
        $totalRows = $rows->count();
        $placedRows = $rows->filter(
            fn ($row) => $row['placement']
                && (int) $row['placement']->status === 2
        )->count();
    @endphp

    @forelse($rows->groupBy(fn ($row) => $row['selection']->student_id) as $studentId => $studentRows)

        @php
            $firstRow = $studentRows->first();
            $student = $firstRow['selection']->student;
            $studentComplete = $studentRows->every(
                fn ($row) => $row['placement']
                    && (int) $row['placement']->status === 2
            );
        @endphp

        <section class="student-card">

            <div class="student-header">

                <div>
                    <div class="student-name">
                        {{ $student->first_name }}
                        {{ $student->last_name }}
                    </div>

                    <div class="student-meta">
                        Öğrenci No:
                        {{ $student->student_number ?? '-' }}
                    </div>
                </div>

                @if($studentComplete)
                    <span class="status status-placed">
                        Tüm tercihler yerleştirildi
                    </span>
                @else
                    <span class="status status-waiting">
                        Yerleştirme bekliyor
                    </span>
                @endif

            </div>

            @foreach($studentRows as $row)

                @php
                    $selection = $row['selection'];
                    $placement = $row['placement'];
                    $suggested = $row['suggestedModule'];

                    $course = $selection->course;
                    $group = $selection->moduleGroup;

                    $modules = $group
                        ? $group->modules()
                            ->where('active', true)
                            ->orderBy('module_number')
                            ->get()
                        : collect();

                    $selectedModuleId =
                        $placement?->course_module_id
                        ?? $suggested?->id;
                @endphp

                <div class="row">

                    <form
                        method="POST"
                        action="{{ route(
                            'admin.student-placements.place',
                            $selection
                        ) }}"
                    >

                        @csrf
                        @method('PUT')

                        <div class="row-grid">

                            <div>
                                <div class="label">
                                    DERS
                                </div>

                                <div class="value">
                                    {{ $course->name }}
                                </div>

                                <div class="subvalue">
                                    {{ $course->category?->name }}
                                </div>
                            </div>

                            <div>
                                <div class="label">
                                    PROGRAM / ALAN
                                </div>

                                <div class="value">
                                    {{ $group?->name ?? $course->name }}
                                </div>
                            </div>

                            <div>
                                <div class="label">
                                    HAFTALIK SAAT
                                </div>

                                <div class="value">
                                    {{ $selection->weekly_hours }}
                                    saat
                                </div>
                            </div>

                            <div>
                                <div class="label">
                                    MODÜL
                                </div>

                                @if($modules->count())

                                    <select name="course_module_id">

                                        @foreach($modules as $module)

                                            <option
                                                value="{{ $module->id }}"
                                                {{ (int) $selectedModuleId === (int) $module->id ? 'selected' : '' }}
                                            >
                                                Modül {{ $module->module_number }}
                                                —
                                                {{ $module->name }}
                                            </option>

                                        @endforeach

                                    </select>

                                    @if($suggested && !$placement)
                                        <div style="
                                            margin-top:5px;
                                            color:#166534;
                                            font-size:11px;
                                            font-weight:700;
                                        ">
                                            Sistem önerisi:
                                            Modül {{ $suggested->module_number }}
                                        </div>
                                    @endif

                                @else

                                    <div class="subvalue">
                                        Modül gerekmiyor.
                                    </div>

                                @endif

                            </div>

                            <div>
                                <div class="label">
                                    DURUM
                                </div>

                                @if($placement)

                                    @if((int) $placement->status === 2)

                                        <span class="status status-placed">
                                            Yerleştirildi
                                        </span>

                                    @elseif((int) $placement->status === 3)

                                        <span class="status status-placed">
                                            Kesinleşti
                                        </span>

                                    @else

                                        <span class="status status-waiting">
                                            Bekliyor
                                        </span>

                                    @endif

                                @else

                                    <span class="status status-waiting">
                                        Yerleştirilmedi
                                    </span>

                                @endif

                                <button
                                    type="submit"
                                    class="button button-primary"
                                    style="margin-top:8px;width:100%;"
                                >
                                    {{ $placement ? 'Güncelle' : 'Yerleştir' }}
                                </button>

                            </div>

                        </div>

                        <div style="margin-top:12px;">

                            <div class="label">
                                NOT
                            </div>

                            <textarea
                                name="notes"
                                placeholder="İsteğe bağlı not..."
                            >{{ $placement?->notes }}</textarea>

                        </div>

                    </form>

                </div>

            @endforeach

        </section>

    @empty

        <div class="empty">
            Bu eğitim yılı için henüz öğrenci tercihi bulunmuyor.
        </div>

    @endforelse

    <div class="bottom-bar">

        <div class="bottom-info">
            <strong>{{ $placedRows }}</strong>
            /
            <strong>{{ $totalRows }}</strong>
            tercih için yerleştirme yapıldı.
            <br>
            Kesinleştirme sonrası öğrenci geçmişleri oluşturulur.
        </div>

        <form
            method="POST"
            action="{{ route(
                'admin.student-placements.confirm',
                $academicYear
            ) }}"
        >
            @csrf

            <button
                type="submit"
                class="confirm-button"
                {{ $totalRows === 0 || $placedRows !== $totalRows ? 'disabled' : '' }}
                onclick="
                    return confirm(
                        'Tüm yerleştirmeleri kesinleştirmek istediğinize emin misiniz? Bu işlem öğrenci geçmişlerini oluşturacaktır.'
                    );
                "
            >
                Yerleştirmeleri Kesinleştir
            </button>

        </form>

    </div>

</main>

</body>
</html>