<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Seçmeli Ders Tercihleri</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Inter, Arial, sans-serif;
            background: #f3f6fa;
            color: #1f2937;
        }

        button,
        input,
        select {
            font: inherit;
        }

        .page {
            min-height: 100vh;
        }

        .header {
            background: linear-gradient(135deg, #173b68, #245b91);
            color: white;
            padding: 28px 20px 70px;
        }

        .header-inner {
            max-width: 1180px;
            margin: auto;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .brand-icon {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            background: rgba(255,255,255,.14);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 25px;
        }

        .brand h1 {
            margin: 0;
            font-size: 22px;
        }

        .brand p {
            margin: 4px 0 0;
            opacity: .82;
            font-size: 14px;
        }

        .student-card {
            max-width: 1180px;
            margin: -45px auto 24px;
            position: relative;
            background: white;
            border-radius: 18px;
            box-shadow: 0 12px 35px rgba(15,23,42,.10);
            padding: 22px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
        }

        .student-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .avatar {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            background: #e8f0f8;
            color: #245b91;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            font-weight: 700;
        }

        .student-info h2 {
            margin: 0;
            font-size: 18px;
        }

        .student-info p {
            margin: 5px 0 0;
            color: #6b7280;
            font-size: 14px;
        }

        .year-badge {
            background: #eef5fb;
            color: #245b91;
            border-radius: 10px;
            padding: 9px 14px;
            font-size: 14px;
            font-weight: 600;
        }

        .container {
            max-width: 1180px;
            margin: auto;
            padding: 0 20px 60px;
        }

        .success,
        .errors {
            border-radius: 14px;
            padding: 15px 16px;
            margin-bottom: 22px;
        }

        .success {
            background: #ecfdf3;
            border: 1px solid #bbf7d0;
            color: #166534;
            font-weight: 600;
        }

        .errors {
            background: #fff1f1;
            border: 1px solid #fecaca;
            color: #991b1b;
        }

        .errors strong {
            display: block;
            margin-bottom: 7px;
        }

        .errors ul {
            margin: 0;
            padding-left: 20px;
        }

        .info-banner {
            background: white;
            border-radius: 18px;
            padding: 20px;
            margin-bottom: 22px;
            box-shadow: 0 5px 20px rgba(15,23,42,.06);
        }

        .info-banner strong {
            color: #245b91;
        }

        .info-banner p {
            margin: 7px 0 0;
            color: #64748b;
            font-size: 14px;
            line-height: 1.6;
        }

        .progress-card {
            background: white;
            border-radius: 18px;
            padding: 22px;
            margin-bottom: 28px;
            box-shadow: 0 5px 20px rgba(15,23,42,.06);
        }

        .progress-top {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            align-items: center;
            margin-bottom: 14px;
        }

        .progress-title {
            font-size: 16px;
            font-weight: 700;
        }

        .progress-message {
            margin-top: 7px;
            color: #64748b;
            font-size: 13px;
        }

        .hours {
            font-size: 26px;
            font-weight: 800;
            color: #245b91;
            white-space: nowrap;
        }

        .hours.complete {
            color: #16834a;
        }

        .progress-bar {
            height: 12px;
            background: #e8edf3;
            border-radius: 20px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            width: 0;
            background: #245b91;
            border-radius: 20px;
            transition: width .2s ease;
        }

        .progress-fill.complete {
            background: #16834a;
        }

        .category-card {
            background: white;
            border-radius: 18px;
            padding: 20px;
            margin-bottom: 24px;
            box-shadow: 0 5px 20px rgba(15,23,42,.06);
        }

        .category-header {
            display: flex;
            justify-content: space-between;
            gap: 15px;
            align-items: center;
            margin-bottom: 18px;
        }

        .category-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .category-number {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            background: #245b91;
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            font-weight: 800;
        }

        .category-title {
            margin: 0;
            font-size: 18px;
        }

        .category-hint {
            color: #64748b;
            font-size: 12px;
            margin-top: 3px;
        }

        .selected-count {
            padding: 6px 10px;
            background: #eef5fb;
            color: #245b91;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 800;
        }

        .preference-row {
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 16px;
            margin-top: 10px;
            background: #f8fafc;
            transition: .2s;
        }

        .preference-row.primary {
            border-color: #c4d7ea;
            background: #f7fbff;
        }

        .preference-row.invalid {
            border-color: #fca5a5;
            background: #fff7f7;
        }

        .preference-grid {
            display: grid;
            grid-template-columns: 75px 1.4fr 1.2fr 190px;
            gap: 12px;
            align-items: end;
        }

        .preference-number {
            font-size: 14px;
            font-weight: 800;
            color: #245b91;
            padding-bottom: 11px;
        }

        .field label {
            display: block;
            margin-bottom: 5px;
            color: #475569;
            font-size: 11px;
            font-weight: 800;
        }

        .field select {
            width: 100%;
            min-height: 42px;
            padding: 9px 10px;
            border: 1px solid #cbd5e1;
            border-radius: 9px;
            background: white;
            color: #1f2937;
        }

        .field select:disabled {
            background: #f1f5f9;
            color: #94a3b8;
            cursor: not-allowed;
        }

        .hour-required-note {
            margin-top: 6px;
            color: #245b91;
            font-size: 11px;
        }

        .optional-note {
            margin-top: 6px;
            color: #64748b;
            font-size: 11px;
        }

        .submit-area {
            margin-top: 28px;
            background: white;
            border-radius: 18px;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            box-shadow: 0 5px 20px rgba(15,23,42,.06);
        }

        .submit-info {
            color: #64748b;
            font-size: 14px;
        }

        .submit-button {
            border: 0;
            border-radius: 11px;
            padding: 13px 22px;
            background: #245b91;
            color: white;
            font-weight: 700;
            cursor: pointer;
            min-width: 180px;
        }

        .submit-button:hover {
            background: #194a78;
        }

        .submit-button:disabled {
            background: #cbd5e1;
            color: #64748b;
            cursor: not-allowed;
        }

        .footer {
            text-align: center;
            color: #94a3b8;
            font-size: 12px;
            padding: 30px 20px;
        }

        @media (max-width: 1000px) {
            .preference-grid {
                grid-template-columns: 60px 1fr 1fr;
            }

            .hour-cell {
                grid-column: 2 / 4;
            }
        }

        @media (max-width: 700px) {
            .student-card,
            .progress-top,
            .submit-area,
            .category-header {
                flex-direction: column;
                align-items: stretch;
            }

            .preference-grid {
                grid-template-columns: 1fr;
            }

            .hour-cell {
                grid-column: auto;
            }

            .preference-number {
                padding-bottom: 0;
            }

            .hours {
                align-self: flex-start;
            }
        }
    </style>
</head>

<body>

<div class="page">

    <header class="header">
        <div class="header-inner">

            <div class="brand">
                <div class="brand-icon">🎓</div>

                <div>
                    <h1>Seçmeli Ders Tercih Sistemi</h1>
                    <p>Öğrenci Ders Tercih İşlemleri</p>
                </div>
            </div>

        </div>
    </header>

    <div class="student-card">

        <div class="student-info">

            <div class="avatar">
                {{ mb_substr($student->first_name, 0, 1) }}
            </div>

            <div>
                <h2>
                    {{ $student->first_name }}
                    {{ $student->last_name }}
                </h2>

                <p>
                    {{ $student->student_number ?? '' }}

                    @if($student->student_number)
                        •
                    @endif

                    {{ $student->studentYears()->where('academic_year_id', $academicYear->id)->first()?->grade }}
                    . Sınıf
                </p>
            </div>

        </div>

        <div class="year-badge">
            {{ $academicYear->name }} Eğitim Öğretim Yılı
        </div>

    </div>

    <main class="container">

        @if(session('success'))
            <div class="success">
                ✓ {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="errors">
                <strong>⚠ Tercihleriniz kaydedilemedi.</strong>

                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @php
            $studentYear = $student->studentYears()
                ->where('academic_year_id', $academicYear->id)
                ->where('active', true)
                ->first();

            $grade = (int) ($studentYear?->grade ?? 5);

            $requiredHours = $grade === 8 ? 6 : 5;

            /*
             * Dersleri kategorilere ayır.
             */
            $groupedCourses = $courses->groupBy(
                fn ($course) => $course->course_category_id
            );

            /*
             * Kategori isimleri.
             */
            $categoryNames = $courses
                ->mapWithKeys(function ($course) {
                    return [
                        $course->course_category_id =>
                            $course->category->name,
                    ];
                })
                ->unique();

            /*
             * Mevcut tercihleri kategori + sıra
             * şeklinde hazırla.
             */
            $existingSelections = [];

            foreach ($selections as $selection) {
                if (! $selection->course) {
                    continue;
                }

                $categoryId =
                    (int) $selection->course->course_category_id;

                $order =
                    (int) $selection->preference_order;

                if ($order < 1 || $order > 3) {
                    continue;
                }

                $existingSelections[$categoryId][$order] =
                    $selection;
            }

            /*
             * JavaScript'e gönderilecek ders kataloğu.
             *
             * Burada karmaşık @json(map(...)) kullanmıyoruz.
             */
            $courseCatalog = $courses
                ->map(function ($course) {
                    return [
                        'id' => (int) $course->id,
                        'name' => $course->name,
                        'categoryId' => (int) $course->course_category_id,
                        'isModular' => (bool) $course->is_modular,

                        'moduleGroups' => $course->moduleGroups
                            ->where('active', true)
                            ->map(function ($group) {
                                return [
                                    'id' => (int) $group->id,
                                    'name' => $group->name,
                                ];
                            })
                            ->values()
                            ->all(),

                        'gradeOptions' => $course->gradeOptions
                            ->where('active', true)
                            ->map(function ($option) {
                                return [
                                    'id' => (int) $option->id,
                                    'grade' => (int) $option->grade,
                                    'weeklyHours' => (int) $option->weekly_hours,
                                ];
                            })
                            ->values()
                            ->all(),
                    ];
                })
                ->values()
                ->all();

            /*
             * Mevcut seçimleri sadeleştir.
             */
            $existingSelectionsJson = $selections
                ->map(function ($selection) {
                    return [
                        'categoryId' =>
                            (int) $selection->course->course_category_id,

                        'preferenceOrder' =>
                            (int) $selection->preference_order,

                        'courseId' =>
                            (int) $selection->course_id,

                        'moduleGroupId' =>
                            $selection->course_module_group_id
                                ? (int) $selection->course_module_group_id
                                : null,

                        'gradeOptionId' =>
                            $selection->course_grade_option_id
                                ? (int) $selection->course_grade_option_id
                                : null,

                        'weeklyHours' =>
                            $selection->weekly_hours !== null
                                ? (int) $selection->weekly_hours
                                : null,
                    ];
                })
                ->values()
                ->all();
        @endphp

        <div class="info-banner">
            <strong>Tercih sistemi nasıl çalışır?</strong>

            <p>
                Her ders grubunda en fazla üç alternatif tercih
                yapabilirsiniz.
                <strong>1. tercih</strong> için haftalık ders saati
                seçilir.
                2. ve 3. tercihler alternatif olarak tutulur;
                bu tercihler için ayrıca saat seçilmez.
                Okul, grup oluşturma aşamasında ihtiyaç olması halinde
                alternatif tercihlerinizi kullanabilir.
            </p>
        </div>

        <section class="progress-card">

            <div class="progress-top">

                <div>

                    <div class="progress-title">
                        1. Tercihlerinizin Ders Saati
                    </div>

                    <div
                        class="progress-message"
                        id="progressMessage"
                    >
                        Her ders grubunda 1. tercih ve saat seçmelisiniz.
                    </div>

                </div>

                <div
                    class="hours"
                    id="hoursCounter"
                >
                    0 / {{ $requiredHours }} saat
                </div>

            </div>

            <div class="progress-bar">
                <div
                    class="progress-fill"
                    id="progressFill"
                ></div>
            </div>

        </section>

        <form
            method="POST"
            action="{{ route('course-selections.store') }}"
            id="selectionForm"
        >

            @csrf

            <div id="selectionInputs"></div>

            @foreach($groupedCourses as $categoryId => $categoryCourses)

                @php
                    $categoryId = (int) $categoryId;
                    $existing = $existingSelections[$categoryId] ?? [];
                @endphp

                <section
                    class="category-card"
                    data-category-id="{{ $categoryId }}"
                >

                    <div class="category-header">

                        <div class="category-left">

                            <div class="category-number">
                                {{ $loop->iteration }}
                            </div>

                            <div>
                                <h2 class="category-title">
                                    {{ $categoryNames[$categoryId] }}
                                </h2>

                                <div class="category-hint">
                                    Bu gruptaki dersleri tercih sıranıza göre seçebilirsiniz.
                                </div>
                            </div>

                        </div>

                        <div
                            class="selected-count"
                            data-category-count="{{ $categoryId }}"
                        >
                            0 tercih
                        </div>

                    </div>

                    @for($order = 1; $order <= 3; $order++)

                        @php
                            $existingSelection =
                                $existing[$order] ?? null;
                        @endphp

                        <div
                            class="preference-row {{ $order === 1 ? 'primary' : '' }}"
                            data-category-id="{{ $categoryId }}"
                            data-preference-order="{{ $order }}"
                        >

                            <div class="preference-grid">

                                <div class="preference-number">
                                    {{ $order }}. Tercih
                                </div>

                                <div class="field">
                                    <label>DERS</label>

                                    <select
                                        class="course-select"
                                        data-category-id="{{ $categoryId }}"
                                        data-preference-order="{{ $order }}"
                                    >
                                        <option value="">
                                            Ders seçiniz
                                        </option>

                                        @foreach($categoryCourses as $course)

                                            @php
                                                $remaining =
                                                    $service->remainingAttempts(
                                                        $student,
                                                        $course
                                                    );

                                                $selectedCourse =
                                                    $existingSelection
                                                    &&
                                                    (int) $existingSelection->course_id
                                                    === (int) $course->id;
                                            @endphp

                                            <option
                                                value="{{ $course->id }}"
                                                {{ $selectedCourse ? 'selected' : '' }}
                                                {{ $remaining <= 0 ? 'disabled' : '' }}
                                            >
                                                {{ $course->name }}

                                                @if($remaining <= 0)
                                                    — alma hakkı dolu
                                                @endif
                                            </option>

                                        @endforeach

                                    </select>
                                </div>

                                <div class="field">
                                    <label>PROGRAM / ALAN</label>

                                    <select
                                        class="module-group-select"
                                        data-category-id="{{ $categoryId }}"
                                        data-preference-order="{{ $order }}"
                                        disabled
                                    >
                                        <option value="">
                                            Önce ders seçiniz
                                        </option>
                                    </select>
                                </div>

                                <div class="field hour-cell">
                                    <label>HAFTALIK DERS SAATİ</label>

                                    <select
                                        class="grade-option-select"
                                        data-category-id="{{ $categoryId }}"
                                        data-preference-order="{{ $order }}"
                                        disabled
                                    >
                                        <option value="">
                                            {{ $order === 1 ? 'Saat seçiniz' : 'Sadece 1. tercih için' }}
                                        </option>
                                    </select>

                                    @if($order === 1)
                                        <div class="hour-required-note">
                                            1. tercih için zorunludur.
                                        </div>
                                    @else
                                        <div class="optional-note">
                                            Alternatif tercihtir; saat burada seçilmez.
                                        </div>
                                    @endif

                                </div>

                            </div>

                        </div>

                    @endfor

                </section>

            @endforeach

            <div class="submit-area">

                <div
                    class="submit-info"
                    id="submitInfo"
                >
                    Her ders grubunda 1. tercih ve saat seçimi yapmalısınız.
                </div>

                <button
                    type="submit"
                    class="submit-button"
                    id="submitButton"
                    disabled
                    {{ !$selectionOpen ? 'disabled' : '' }}
                >
                    {{ $selectionOpen ? 'Tercihleri Gönder' : 'Tercih Dönemi Kapalı' }}
                </button>

            </div>

        </form>

    </main>

    <footer class="footer">
        Seçmeli Ders Tercih Sistemi
    </footer>

</div>

<script>
    const requiredHours = {{ $requiredHours }};
    const selectionOpen = @json($selectionOpen);

    const courseCatalog = @json($courseCatalog);
    const existingSelections = @json($existingSelectionsJson);

    function escapeHtml(text)
    {
        const div = document.createElement('div');
        div.textContent = text ?? '';
        return div.innerHTML;
    }

    function findCourse(courseId)
    {
        return courseCatalog.find(
            course => Number(course.id) === Number(courseId)
        ) || null;
    }

    function getRow(categoryId, order)
    {
        return document.querySelector(
            `.preference-row[data-category-id="${categoryId}"][data-preference-order="${order}"]`
        );
    }

    function getCourseSelect(categoryId, order)
    {
        return getRow(categoryId, order)
            ?.querySelector('.course-select');
    }

    function getGroupSelect(categoryId, order)
    {
        return getRow(categoryId, order)
            ?.querySelector('.module-group-select');
    }

    function getHourSelect(categoryId, order)
    {
        return getRow(categoryId, order)
            ?.querySelector('.grade-option-select');
    }

    /*
     * Program / alan listesini doldur.
     */
    function fillModuleGroups(
        categoryId,
        order,
        selectedGroupId = null
    ) {
        const courseSelect =
            getCourseSelect(categoryId, order);

        const groupSelect =
            getGroupSelect(categoryId, order);

        if (!courseSelect || !groupSelect) {
            return;
        }

        const course =
            findCourse(courseSelect.value);

        groupSelect.innerHTML = '';

        if (!course) {
            groupSelect.innerHTML = `
                <option value="">
                    Önce ders seçiniz
                </option>
            `;

            groupSelect.disabled = true;

            return;
        }

        /*
         * Modüler değilse program gerekmez.
         */
        if (!course.isModular) {
            groupSelect.innerHTML = `
                <option value="">
                    Program gerekmiyor
                </option>
            `;

            groupSelect.disabled = true;

            return;
        }

        const groups =
            Array.isArray(course.moduleGroups)
                ? course.moduleGroups
                : [];

        if (!groups.length) {
            groupSelect.innerHTML = `
                <option value="">
                    Aktif program bulunamadı
                </option>
            `;

            groupSelect.disabled = true;

            return;
        }

        /*
         * Tek program varsa otomatik seç.
         */
        if (groups.length === 1) {
            groupSelect.innerHTML = `
                <option value="${groups[0].id}">
                    ${escapeHtml(groups[0].name)}
                </option>
            `;

            groupSelect.value =
                String(groups[0].id);

            groupSelect.disabled = true;

            return;
        }

        /*
         * Birden fazla program varsa öğrenci seçsin.
         */
        groupSelect.disabled = false;

        groupSelect.innerHTML = `
            <option value="">
                Program / alan seçiniz
            </option>
        `;

        groups.forEach(group => {
            const option =
                document.createElement('option');

            option.value =
                String(group.id);

            option.textContent =
                group.name;

            if (
                selectedGroupId !== null &&
                Number(selectedGroupId) === Number(group.id)
            ) {
                option.selected = true;
            }

            groupSelect.appendChild(option);
        });
    }

    /*
     * Sadece 1. tercih için saatleri doldur.
     */
    function fillHours(
        categoryId,
        order,
        selectedOptionId = null
    ) {
        const courseSelect =
            getCourseSelect(categoryId, order);

        const hourSelect =
            getHourSelect(categoryId, order);

        if (!courseSelect || !hourSelect) {
            return;
        }

        hourSelect.innerHTML = '';

        /*
         * 2. ve 3. tercihte saat yok.
         */
        if (Number(order) !== 1) {
            hourSelect.innerHTML = `
                <option value="">
                    Sadece 1. tercih için
                </option>
            `;

            hourSelect.disabled = true;

            return;
        }

        const course =
            findCourse(courseSelect.value);

        if (!course) {
            hourSelect.innerHTML = `
                <option value="">
                    Saat seçiniz
                </option>
            `;

            hourSelect.disabled = true;

            return;
        }

        const options =
            Array.isArray(course.gradeOptions)
                ? course.gradeOptions
                : [];

        if (!options.length) {
            hourSelect.innerHTML = `
                <option value="">
                    Uygun saat seçeneği yok
                </option>
            `;

            hourSelect.disabled = true;

            return;
        }

        hourSelect.disabled = false;

        hourSelect.innerHTML = `
            <option value="">
                Saat seçiniz
            </option>
        `;

        options.forEach(option => {
            const item =
                document.createElement('option');

            item.value =
                String(option.id);

            item.textContent =
                `${option.weeklyHours} saat`;

            if (
                selectedOptionId !== null &&
                Number(selectedOptionId) === Number(option.id)
            ) {
                item.selected = true;
            }

            hourSelect.appendChild(item);
        });
    }

    /*
     * Aynı kategori içindeki aynı ders
     * 2 kez seçilemez.
     */
    function refreshDuplicateOptions(categoryId)
    {
        const rows =
            document.querySelectorAll(
                `.preference-row[data-category-id="${categoryId}"]`
            );

        const selectedCourseIds = [];

        rows.forEach(row => {
            const select =
                row.querySelector('.course-select');

            if (select && select.value) {
                selectedCourseIds.push(
                    Number(select.value)
                );
            }
        });

        rows.forEach(row => {
            const select =
                row.querySelector('.course-select');

            if (!select) {
                return;
            }

            const currentId =
                Number(select.value || 0);

            Array.from(select.options).forEach(option => {
                if (!option.value) {
                    return;
                }

                const optionId =
                    Number(option.value);

                option.disabled =
                    optionId !== currentId &&
                    selectedCourseIds.includes(optionId);
            });
        });
    }

    /*
     * 2. tercih ancak 1. tercih seçildikten sonra,
     * 3. tercih ancak 2. tercih seçildikten sonra aktifleşir.
     */
    function refreshPreferenceAvailability(categoryId)
    {
        const firstSelect =
            getCourseSelect(categoryId, 1);

        const secondSelect =
            getCourseSelect(categoryId, 2);

        const thirdSelect =
            getCourseSelect(categoryId, 3);

        const firstHasCourse =
            !!firstSelect?.value;

        const secondHasCourse =
            !!secondSelect?.value;

        if (secondSelect) {
            secondSelect.disabled =
                !firstHasCourse;

            if (!firstHasCourse) {
                secondSelect.value = '';

                fillModuleGroups(categoryId, 2);
                fillHours(categoryId, 2);
            }
        }

        if (thirdSelect) {
            thirdSelect.disabled =
                !secondHasCourse;

            if (!secondHasCourse) {
                thirdSelect.value = '';

                fillModuleGroups(categoryId, 3);
                fillHours(categoryId, 3);
            }
        }
    }

    /*
     * Seçim değiştiğinde ilgili satırı güncelle.
     */
    function handleCourseChange(
        categoryId,
        order
    ) {
        fillModuleGroups(
            categoryId,
            order
        );

        fillHours(
            categoryId,
            order
        );

        refreshPreferenceAvailability(
            categoryId
        );

        refreshDuplicateOptions(
            categoryId
        );

        updateProgress();
        rebuildHiddenInputs();
    }

    function handleProgramChange(
        categoryId,
        order
    ) {
        updateProgress();
        rebuildHiddenInputs();
    }

    function handleHourChange(
        categoryId,
        order
    ) {
        updateProgress();
        rebuildHiddenInputs();
    }

    /*
     * Üstteki saat sayacını güncelle.
     */
    function updateProgress()
    {
        let totalHours = 0;
        let allFirstChoicesComplete = true;

        document
            .querySelectorAll('.category-card')
            .forEach(categoryCard => {
                const categoryId =
                    Number(
                        categoryCard.dataset.categoryId
                    );

                const firstCourse =
                    getCourseSelect(
                        categoryId,
                        1
                    );

                const firstGroup =
                    getGroupSelect(
                        categoryId,
                        1
                    );

                const firstHour =
                    getHourSelect(
                        categoryId,
                        1
                    );

                if (
                    !firstCourse ||
                    !firstCourse.value ||
                    (
                        firstGroup &&
                        firstGroup.disabled === false &&
                        !firstGroup.value
                    ) ||
                    !firstHour ||
                    !firstHour.value
                ) {
                    allFirstChoicesComplete = false;
                }

                if (
                    firstHour &&
                    firstHour.value
                ) {
                    const course =
                        findCourse(
                            firstCourse.value
                        );

                    const option =
                        course?.gradeOptions?.find(
                            item =>
                                Number(item.id)
                                ===
                                Number(firstHour.value)
                        );

                    if (option) {
                        totalHours +=
                            Number(option.weeklyHours);
                    }
                }

                const counter =
                    categoryCard.querySelector(
                        '.selected-count'
                    );

                if (counter) {
                    let count = 0;

                    for (
                        let order = 1;
                        order <= 3;
                        order++
                    ) {
                        const select =
                            getCourseSelect(
                                categoryId,
                                order
                            );

                        if (select && select.value) {
                            count++;
                        }
                    }

                    counter.textContent =
                        `${count} tercih`;
                }
            });

        const hoursCounter =
            document.getElementById(
                'hoursCounter'
            );

        const progressFill =
            document.getElementById(
                'progressFill'
            );

        const progressMessage =
            document.getElementById(
                'progressMessage'
            );

        const submitInfo =
            document.getElementById(
                'submitInfo'
            );

        const submitButton =
            document.getElementById(
                'submitButton'
            );

        hoursCounter.textContent =
            `${totalHours} / ${requiredHours} saat`;

        const percentage =
            Math.min(
                100,
                requiredHours > 0
                    ? (
                        totalHours /
                        requiredHours
                    ) * 100
                    : 0
            );

        progressFill.style.width =
            `${percentage}%`;

        const hoursComplete =
            totalHours === requiredHours;

        const complete =
            allFirstChoicesComplete &&
            hoursComplete &&
            selectionOpen;

        hoursCounter.classList.toggle(
            'complete',
            complete
        );

        progressFill.classList.toggle(
            'complete',
            complete
        );

        if (complete) {
            progressMessage.textContent =
                '✓ Birinci tercihler ve toplam ders saati tamamlandı.';

            submitInfo.textContent =
                'Tercihleriniz gönderilmeye hazır.';

            submitButton.disabled =
                false;

            return;
        }

        submitButton.disabled =
            true;

        if (!allFirstChoicesComplete) {
            progressMessage.textContent =
                'Her ders grubunda 1. tercih, program ve saat seçmelisiniz.';

            submitInfo.textContent =
                'Üç ders grubundaki 1. tercihlerinizi tamamlayın.';

            return;
        }

        if (totalHours < requiredHours) {
            progressMessage.textContent =
                `${requiredHours - totalHours} saat daha seçmeniz gerekiyor.`;
        } else {
            progressMessage.textContent =
                'Toplam ders saati sınırı aşıldı.';
        }

        submitInfo.textContent =
            `1. tercihlerin toplamı ${requiredHours} saat olmalıdır.`;
    }

    /*
     * Hidden inputları oluştur.
     */
    function rebuildHiddenInputs()
    {
        const container =
            document.getElementById(
                'selectionInputs'
            );

        container.innerHTML = '';

        let index = 0;

        document
            .querySelectorAll('.category-card')
            .forEach(categoryCard => {
                const categoryId =
                    Number(
                        categoryCard.dataset.categoryId
                    );

                for (
                    let order = 1;
                    order <= 3;
                    order++
                ) {
                    const courseSelect =
                        getCourseSelect(
                            categoryId,
                            order
                        );

                    const groupSelect =
                        getGroupSelect(
                            categoryId,
                            order
                        );

                    const hourSelect =
                        getHourSelect(
                            categoryId,
                            order
                        );

                    if (
                        !courseSelect ||
                        !courseSelect.value
                    ) {
                        continue;
                    }

                    const fields = [
                        [
                            'course_id',
                            courseSelect.value
                        ],
                        [
                            'course_module_group_id',
                            groupSelect?.value || ''
                        ],
                        [
                            'course_grade_option_id',
                            order === 1
                                ? (
                                    hourSelect?.value || ''
                                )
                                : ''
                        ],
                        [
                            'preference_order',
                            order
                        ],
                    ];

                    fields.forEach(
                        ([field, value]) => {
                            const input =
                                document.createElement(
                                    'input'
                                );

                            input.type =
                                'hidden';

                            input.name =
                                `selections[${index}][${field}]`;

                            input.value =
                                value;

                            container.appendChild(
                                input
                            );
                        }
                    );

                    index++;
                }
            });
    }

    /*
     * Mevcut kayıtları ekrana yükle.
     */
    function loadExistingSelections()
    {
        existingSelections.forEach(
            selection => {
                const categoryId =
                    Number(
                        selection.categoryId
                    );

                const order =
                    Number(
                        selection.preferenceOrder
                    );

                const courseSelect =
                    getCourseSelect(
                        categoryId,
                        order
                    );

                if (!courseSelect) {
                    return;
                }

                courseSelect.value =
                    String(
                        selection.courseId
                    );

                fillModuleGroups(
                    categoryId,
                    order,
                    selection.moduleGroupId
                );

                fillHours(
                    categoryId,
                    order,
                    selection.gradeOptionId
                );
            }
        );

        document
            .querySelectorAll('.category-card')
            .forEach(categoryCard => {
                const categoryId =
                    Number(
                        categoryCard.dataset.categoryId
                    );

                refreshPreferenceAvailability(
                    categoryId
                );

                refreshDuplicateOptions(
                    categoryId
                );
            });

        updateProgress();
        rebuildHiddenInputs();
    }

    /*
     * Ders değişiklikleri.
     */
    document
        .querySelectorAll('.course-select')
        .forEach(select => {
            select.addEventListener(
                'change',
                function () {
                    handleCourseChange(
                        Number(
                            this.dataset.categoryId
                        ),
                        Number(
                            this.dataset.preferenceOrder
                        )
                    );
                }
            );
        });

    /*
     * Program değişiklikleri.
     */
    document
        .querySelectorAll('.module-group-select')
        .forEach(select => {
            select.addEventListener(
                'change',
                function () {
                    handleProgramChange(
                        Number(
                            this.dataset.categoryId
                        ),
                        Number(
                            this.dataset.preferenceOrder
                        )
                    );
                }
            );
        });

    /*
     * Saat değişiklikleri.
     */
    document
        .querySelectorAll('.grade-option-select')
        .forEach(select => {
            select.addEventListener(
                'change',
                function () {
                    handleHourChange(
                        Number(
                            this.dataset.categoryId
                        ),
                        Number(
                            this.dataset.preferenceOrder
                        )
                    );
                }
            );
        });

    /*
     * Form gönderiminden hemen önce
     * son bir güvenlik kontrolü.
     */
    document
        .getElementById('selectionForm')
        .addEventListener(
            'submit',
            function (event) {

                updateProgress();
                rebuildHiddenInputs();

                let valid = true;
                let totalHours = 0;

                document
                    .querySelectorAll('.category-card')
                    .forEach(categoryCard => {
                        const categoryId =
                            Number(
                                categoryCard.dataset.categoryId
                            );

                        const firstCourse =
                            getCourseSelect(
                                categoryId,
                                1
                            );

                        const firstGroup =
                            getGroupSelect(
                                categoryId,
                                1
                            );

                        const firstHour =
                            getHourSelect(
                                categoryId,
                                1
                            );

                        if (
                            !firstCourse ||
                            !firstCourse.value
                        ) {
                            valid = false;
                            return;
                        }

                        if (
                            firstGroup &&
                            !firstGroup.disabled &&
                            !firstGroup.value
                        ) {
                            valid = false;
                            return;
                        }

                        if (
                            !firstHour ||
                            !firstHour.value
                        ) {
                            valid = false;
                            return;
                        }

                        const course =
                            findCourse(
                                firstCourse.value
                            );

                        const option =
                            course?.gradeOptions?.find(
                                item =>
                                    Number(item.id)
                                    ===
                                    Number(firstHour.value)
                            );

                        if (option) {
                            totalHours +=
                                Number(
                                    option.weeklyHours
                                );
                        }
                    });

                if (
                    !valid ||
                    totalHours !== requiredHours
                ) {
                    event.preventDefault();

                    alert(
                        `Tercihlerinizi tamamlayınız.\n\n` +
                        `1. tercihlerin toplamı ` +
                        `${requiredHours} saat olmalıdır.`
                    );
                }
            }
        );

    /*
     * İlk açılış.
     */
    loadExistingSelections();

    /*
     * Boş sayfada 2. ve 3. tercihleri kilitle.
     */
    document
        .querySelectorAll('.category-card')
        .forEach(categoryCard => {
            refreshPreferenceAvailability(
                Number(
                    categoryCard.dataset.categoryId
                )
            );
        });

    updateProgress();
    rebuildHiddenInputs();
</script>

</body>
</html>