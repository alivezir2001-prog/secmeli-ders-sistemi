<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Öğrenci Tercihleri - Yönetim</title>

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
            font-size: 24px;
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

        .back {
            display: inline-block;
            margin-bottom: 18px;
            color: #245b91;
            text-decoration: none;
            font-weight: 600;
        }

        .title {
            margin-bottom: 22px;
        }

        .title h2 {
            margin: 0 0 6px;
        }

        .title p {
            margin: 0;
            color: #64748b;
        }

        .filters {
            background: white;
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 24px;
            box-shadow: 0 4px 15px rgba(0,0,0,.05);
        }

        .filters-grid {
            display: grid;
            grid-template-columns:
                1.2fr
                1fr
                .8fr
                .8fr
                1.2fr;
            gap: 12px;
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

        .field input,
        .field select {
            width: 100%;
            padding: 11px 12px;
            border: 1px solid #cbd5e1;
            border-radius: 9px;
            background: white;
            color: #0f172a;
        }

        .filter-actions {
            display: flex;
            align-items: flex-end;
            gap: 10px;
            margin-top: 14px;
        }

        .button {
            border: 0;
            border-radius: 9px;
            padding: 11px 18px;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }

        .button-primary {
            background: #245b91;
            color: white;
        }

        .button-secondary {
            background: #e2e8f0;
            color: #334155;
        }

        .summary {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            margin-bottom: 24px;
        }

        .summary-card {
            background: white;
            border-radius: 14px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,.05);
        }

        .summary-label {
            color: #64748b;
            font-size: 13px;
            margin-bottom: 8px;
        }

        .summary-value {
            font-size: 25px;
            font-weight: 800;
        }

        .report-card {
            background: white;
            border-radius: 16px;
            padding: 22px;
            margin-bottom: 24px;
            box-shadow: 0 4px 15px rgba(0,0,0,.05);
        }

        .report-title {
            margin: 0 0 16px;
            font-size: 18px;
        }

        .report-list {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .report-item {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 15px;
            padding: 11px 13px;
            background: #f8fafc;
            border-radius: 9px;
        }

        .report-name {
            font-weight: 600;
        }

        .report-category {
            margin-top: 3px;
            color: #64748b;
            font-size: 12px;
        }

        .report-count {
            font-weight: 800;
            color: #245b91;
            white-space: nowrap;
        }

        .student-card {
            background: white;
            border-radius: 16px;
            padding: 22px;
            margin-bottom: 18px;
            box-shadow: 0 4px 15px rgba(0,0,0,.05);
        }

        .student-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 20px;
            margin-bottom: 18px;
        }

        .student-name {
            font-size: 20px;
            font-weight: 700;
        }

        .student-meta {
            margin-top: 5px;
            color: #64748b;
            font-size: 14px;
        }

        .status {
            padding: 7px 12px;
            border-radius: 999px;
            background: #dcfce7;
            color: #166534;
            font-size: 12px;
            font-weight: 700;
        }

        .selection-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .selection-item {
            display: grid;
            grid-template-columns: 38px 1fr auto;
            align-items: center;
            gap: 12px;
            padding: 12px 14px;
            background: #f8fafc;
            border-radius: 10px;
        }

        .order {
            width: 30px;
            height: 30px;
            border-radius: 8px;
            background: #245b91;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 700;
        }

        .course-name {
            font-weight: 600;
        }

        .course-category {
            margin-top: 3px;
            color: #64748b;
            font-size: 12px;
        }

        .hours {
            color: #245b91;
            font-weight: 700;
            white-space: nowrap;
        }

        .student-footer {
            display: flex;
            justify-content: flex-end;
            margin-top: 16px;
            padding-top: 14px;
            border-top: 1px solid #e2e8f0;
            font-weight: 700;
        }

        .empty {
            background: white;
            border-radius: 16px;
            padding: 40px;
            text-align: center;
            color: #64748b;
        }

        @media (max-width: 1000px) {
            .filters-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .summary {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 650px) {
            .filters-grid,
            .summary {
                grid-template-columns: 1fr;
            }

            .student-header {
                flex-direction: column;
            }

            .selection-item {
                grid-template-columns: 36px 1fr;
            }

            .hours {
                grid-column: 2;
            }
        }
    </style>
</head>

<body>

<header class="header">
    <h1>Seçmeli Ders Sistemi</h1>
    <p>Yönetim → Öğrenci Tercihleri</p>
</header>

<main class="container">

    <a
        href="{{ route('admin.dashboard') }}"
        class="back"
    >
        ← Yönetim Paneli
    </a>

    <div class="title">
        <h2>Öğrenci Tercihleri</h2>

        <p>
            Aktif eğitim yılı:
            <strong>{{ $academicYear->name }}</strong>
        </p>
    </div>

    {{-- FİLTRELER --}}
    <form
        method="GET"
        action="{{ route('admin.course-selections.index') }}"
        class="filters"
    >

        <div class="filters-grid">

            <div class="field">
                <label for="academic_year_id">
                    Eğitim Yılı
                </label>

                <select name="academic_year_id" id="academic_year_id">
                    @foreach($academicYears as $year)

                        <option
                            value="{{ $year->id }}"
                            {{ (int) $academicYear->id === (int) $year->id ? 'selected' : '' }}
                        >
                            {{ $year->name }}
                            {{ $year->active ? ' (Aktif)' : '' }}
                        </option>

                    @endforeach
                </select>
            </div>

            <div class="field">
                <label for="search">
                    Öğrenci Ara
                </label>

                <input
                    type="text"
                    id="search"
                    name="search"
                    value="{{ $search }}"
                    placeholder="Ad, soyad veya öğrenci no"
                >
            </div>

            <div class="field">
                <label for="grade">
                    Sınıf
                </label>

                <select name="grade" id="grade">
                    <option value="">Tümü</option>

                    @foreach($grades as $item)
                        <option
                            value="{{ $item }}"
                            {{ (string) $grade === (string) $item ? 'selected' : '' }}
                        >
                            {{ $item }}. Sınıf
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="field">
                <label for="section">
                    Şube
                </label>

                <select name="section" id="section">
                    <option value="">Tümü</option>

                    @foreach($sections as $item)
                        <option
                            value="{{ $item }}"
                            {{ (string) $section === (string) $item ? 'selected' : '' }}
                        >
                            {{ $item }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="field">
                <label for="course_id">
                    Ders
                </label>

                <select name="course_id" id="course_id">
                    <option value="">Tüm Dersler</option>

                    @foreach($courses as $course)
                        <option
                            value="{{ $course->id }}"
                            {{ (string) $courseId === (string) $course->id ? 'selected' : '' }}
                        >
                            {{ $course->name }}
                        </option>
                    @endforeach
                </select>
            </div>

        </div>

        <div class="filter-actions">

            <button
                type="submit"
                class="button button-primary"
            >
                Filtrele
            </button>

            <a
                href="{{ route('admin.course-selections.index') }}"
                class="button button-secondary"
            >
                Temizle
            </a>

        </div>

    </form>

    @php
        $studentCount = $selectionGroups->count();

        $totalSelections = $selectionGroups
            ->flatten(1)
            ->count();

        $totalHours = $selectionGroups
            ->flatten(1)
            ->sum(
                fn ($selection) =>
                    $selection->gradeOption->weekly_hours
            );
    @endphp

    <div class="summary">

        <div class="summary-card">
            <div class="summary-label">
                Tercih yapan öğrenci
            </div>

            <div class="summary-value">
                {{ $studentCount }}
            </div>
        </div>

        <div class="summary-card">
            <div class="summary-label">
                Toplam tercih
            </div>

            <div class="summary-value">
                {{ $totalSelections }}
            </div>
        </div>

        <div class="summary-card">
            <div class="summary-label">
                Toplam seçilen saat
            </div>

            <div class="summary-value">
                {{ $totalHours }}
            </div>
        </div>

        <div class="summary-card">
            <div class="summary-label">
                Eğitim yılı
            </div>

            <div class="summary-value">
                {{ $academicYear->name }}
            </div>
        </div>

    </div>

    {{-- DERS DAĞILIM RAPORU --}}
    <section class="report-card">

        <h3 class="report-title">
            Ders Tercih Dağılımı
        </h3>

        @if($courseCounts->isEmpty())

            <div class="empty">
                Filtreye uygun tercih bulunmuyor.
            </div>

        @else

            <div class="report-list">

                @foreach($courseCounts as $report)

                    <div class="report-item">

                        <div>
                            <div class="report-name">
                                {{ $report['name'] }}
                            </div>

                            <div class="report-category">
                                {{ $report['category'] }}
                            </div>
                        </div>

                        <div class="report-count">
                            {{ $report['count'] }} öğrenci
                        </div>

                    </div>

                @endforeach

            </div>

        @endif

    </section>

    {{-- ÖĞRENCİLER --}}
    @if($selectionGroups->isEmpty())

        <div class="empty">
            Filtreye uygun gönderilmiş tercih bulunmuyor.
        </div>

    @else

        @foreach($selectionGroups as $studentId => $selections)

            @php
                $firstSelection = $selections->first();

                $student = $firstSelection->student;

                $studentYear = $student->studentYears->first();

                $studentTotalHours = $selections->sum(
                    fn ($selection) =>
                        $selection->gradeOption->weekly_hours
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
                            <strong>
                                {{ $student->student_number }}
                            </strong>

                            @if($studentYear)
                                ·
                                {{ $studentYear->grade }}.
                                Sınıf
                                {{ $studentYear->section }}
                            @endif

                        </div>
                    </div>

                    <div class="status">
                        Gönderildi
                    </div>

                </div>

                <div class="selection-list">

                    @foreach($selections as $selection)

                        <div class="selection-item">

                            <div class="order">
                                {{ $selection->preference_order }}
                            </div>

                            <div>

                                <div class="course-name">
                                    {{ $selection->course->name }}
                                </div>

                                <div class="course-category">
                                    {{ $selection->course->category->name }}
                                </div>

                            </div>

                            <div class="hours">
                                {{ $selection->gradeOption->weekly_hours }}
                                saat
                            </div>

                        </div>

                    @endforeach

                </div>

                <div class="student-footer">
                    Toplam: {{ $studentTotalHours }} saat
                </div>

            </section>

        @endforeach

    @endif

</main>

</body>
</html>