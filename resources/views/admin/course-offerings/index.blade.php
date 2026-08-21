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
            max-width: 1250px;
            margin: 30px auto;
            padding: 0 20px 50px;
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
            font-size: 19px;
            font-weight: 700;
        }

        .category {
            margin-top: 5px;
            color: #64748b;
            font-size: 13px;
        }

        .status {
            padding: 7px 11px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
        }

        .status-open {
            background: #dcfce7;
            color: #166534;
        }

        .status-closed {
            background: #fee2e2;
            color: #991b1b;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            margin-bottom: 18px;
        }

        .info-box {
            background: #f8fafc;
            border-radius: 10px;
            padding: 14px;
        }

        .info-label {
            color: #64748b;
            font-size: 12px;
            margin-bottom: 5px;
        }

        .info-value {
            font-size: 18px;
            font-weight: 800;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 14px;
        }

        .field {
            display: flex;
            flex-direction: column;
            gap: 7px;
        }

        .field label {
            font-size: 12px;
            font-weight: 700;
            color: #475569;
        }

        .field input[type="number"] {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #cbd5e1;
            border-radius: 9px;
        }

        .check {
            display: flex;
            align-items: center;
            gap: 8px;
            min-height: 42px;
        }

        .minimum {
            background: #eff6ff;
            color: #1d4ed8;
            border-radius: 9px;
            padding: 10px 12px;
            font-weight: 700;
        }

        .actions {
            margin-top: 16px;
            display: flex;
            justify-content: flex-end;
        }

        .button {
            border: 0;
            border-radius: 9px;
            padding: 11px 18px;
            background: #245b91;
            color: white;
            font-weight: 700;
            cursor: pointer;
        }

        .back {
            display: inline-block;
            margin-bottom: 18px;
            color: #245b91;
            text-decoration: none;
            font-weight: 600;
        }

        @media (max-width: 800px) {
            .topbar,
            .course-header {
                flex-direction: column;
                align-items: stretch;
            }

            .info-grid,
            .form-grid {
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

    <div class="topbar">

        <div>
            <h2>Ders Kontenjanları</h2>
            <p>
                {{ $academicYear->name }} eğitim yılı
            </p>
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

        @php
            $offering = $course->offerings->first();

            $selectedCount =
                (int) ($selectionCounts[$course->id] ?? 0);

            $minimumStudents =
                $offering?->minimum_students ?? 10;

            $maximumStudents =
                $offering?->maximum_students;

            $allowMultipleClasses =
                $offering?->allow_multiple_classes ?? false;

            $maximumClasses =
                $offering?->maximum_classes ?? 1;

            $active =
                $offering?->active ?? true;

            $canOpen =
                $selectedCount >= 10;

            $isFull =
                $maximumStudents !== null &&
                $selectedCount >= $maximumStudents;
        @endphp

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

                @if(! $canOpen)
                    <div class="status status-closed">
                        Minimum öğrenci şartı sağlanmadı
                    </div>
                @elseif($isFull && ! $allowMultipleClasses)
                    <div class="status status-closed">
                        Kontenjan dolu
                    </div>
                @else
                    <div class="status status-open">
                        Açılabilir
                    </div>
                @endif

            </div>

            <div class="info-grid">

                <div class="info-box">
                    <div class="info-label">
                        Tercih eden öğrenci
                    </div>

                    <div class="info-value">
                        {{ $selectedCount }}
                    </div>
                </div>

                <div class="info-box">
                    <div class="info-label">
                        Sistem minimumu
                    </div>

                    <div class="info-value">
                        {{ $minimumStudents }}
                    </div>
                </div>

                <div class="info-box">
                    <div class="info-label">
                        Maksimum öğrenci
                    </div>

                    <div class="info-value">
                        {{ $maximumStudents ?? 'Sınırsız' }}
                    </div>
                </div>

            </div>

            <form
                method="POST"
                action="{{ route('admin.course-offerings.update', $course) }}"
            >
                @csrf
                @method('PUT')

                <input
                    type="hidden"
                    name="academic_year_id"
                    value="{{ $academicYear->id }}"
                >

                <div class="form-grid">

                    <div class="field">
                        <label>
                            Minimum öğrenci
                        </label>

                        <div class="minimum">
                            10 öğrenci
                        </div>
                    </div>

                    <div class="field">
                        <label for="maximum_{{ $course->id }}">
                            Maksimum öğrenci
                        </label>

                        <input
                            type="number"
                            id="maximum_{{ $course->id }}"
                            name="maximum_students"
                            min="1"
                            value="{{ $maximumStudents }}"
                            placeholder="Sınırsız"
                        >
                    </div>

                    <div class="field">
                        <label for="classes_{{ $course->id }}">
                            Maksimum sınıf sayısı
                        </label>

                        <input
                            type="number"
                            id="classes_{{ $course->id }}"
                            name="maximum_classes"
                            min="1"
                            max="20"
                            value="{{ $maximumClasses }}"
                        >
                    </div>

                </div>

                <div style="margin-top:16px;">

                    <label class="check">
                        <input
                            type="checkbox"
                            name="allow_multiple_classes"
                            value="1"
                            {{ $allowMultipleClasses ? 'checked' : '' }}
                        >

                        Aynı ders için birden fazla sınıf açılabilir
                    </label>

                    <label
                        class="check"
                        style="margin-top:8px;"
                    >
                        <input
                            type="checkbox"
                            name="active"
                            value="1"
                            {{ $active ? 'checked' : '' }}
                        >

                        Ders bu eğitim yılında aktif
                    </label>

                </div>

                <div class="actions">
                    <button
                        type="submit"
                        class="button"
                    >
                        Kaydet
                    </button>
                </div>

            </form>

        </section>

    @endforeach

</main>

</body>
</html>