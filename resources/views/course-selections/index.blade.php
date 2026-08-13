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
            font-family: Inter, -apple-system, BlinkMacSystemFont, "Segoe UI",
                Roboto, Arial, sans-serif;
            background: #f3f6fa;
            color: #1f2937;
        }

        button,
        input {
            font: inherit;
        }

        .page {
            min-height: 100vh;
        }

        /* HEADER */

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
            font-weight: 700;
        }

        .brand p {
            margin: 4px 0 0;
            opacity: .82;
            font-size: 14px;
        }

        /* STUDENT CARD */

        .student-card {
            max-width: 1180px;
            margin: -45px auto 24px;
            position: relative;
            background: white;
            border-radius: 18px;
            box-shadow: 0 12px 35px rgba(15, 23, 42, .10);
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

        /* MAIN */

        .container {
            max-width: 1180px;
            margin: auto;
            padding: 0 20px 60px;
        }

        /* PROGRESS */

        .progress-card {
            background: white;
            border-radius: 18px;
            padding: 22px;
            margin-bottom: 28px;
            box-shadow: 0 5px 20px rgba(15, 23, 42, .06);
        }

        .progress-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            margin-bottom: 14px;
        }

        .progress-title {
            font-size: 16px;
            font-weight: 700;
        }

        .hours {
            font-size: 25px;
            font-weight: 800;
            color: #245b91;
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
            transition: width .25s ease;
        }

        .progress-fill.complete {
            background: #16834a;
        }

        .progress-message {
            margin-top: 12px;
            font-size: 14px;
            color: #6b7280;
        }

        /* CATEGORY */

        .category {
            margin-bottom: 30px;
        }

        .category-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 14px;
        }

        .category-number {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            background: #245b91;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
        }

        .category-title {
            margin: 0;
            font-size: 19px;
        }

        .course-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 15px;
        }

        /* COURSE CARD */

        .course-card {
            background: white;
            border: 2px solid transparent;
            border-radius: 16px;
            padding: 18px;
            box-shadow: 0 4px 15px rgba(15, 23, 42, .05);
            transition: .2s ease;
        }

        .course-card:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 20px rgba(15, 23, 42, .08);
        }

        .course-card.selected {
            border-color: #245b91;
            background: #f7fbff;
        }

        .course-card.locked {
            opacity: .62;
            background: #f8fafc;
        }

        .course-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 15px;
        }

        .course-name {
            font-weight: 700;
            font-size: 16px;
            line-height: 1.35;
        }

        .selection-number {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: #245b91;
            color: white;
            display: none;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-weight: 800;
        }

        .course-card.selected .selection-number {
            display: flex;
        }

        .course-meta {
            margin-top: 8px;
            color: #6b7280;
            font-size: 13px;
        }

        .lock-message {
            margin-top: 13px;
            padding: 10px 12px;
            background: #fff3f3;
            color: #a12b2b;
            border-radius: 9px;
            font-size: 13px;
        }

        /* HOURS */

        .hours-label {
            margin-top: 16px;
            margin-bottom: 9px;
            font-size: 13px;
            font-weight: 600;
            color: #4b5563;
        }

        .hours-options {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .hour-option {
            position: relative;
        }

        .hour-option input {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .hour-option label {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 75px;
            padding: 9px 13px;
            border: 1px solid #d7dee7;
            border-radius: 9px;
            cursor: pointer;
            background: white;
            color: #374151;
            font-size: 13px;
            font-weight: 600;
            transition: .15s;
        }

        .hour-option label:hover {
            border-color: #245b91;
        }

        .hour-option input:checked + label {
            background: #245b91;
            border-color: #245b91;
            color: white;
        }

        /* SELECTED */

        .selected-card {
            background: #fff;
            border-radius: 18px;
            padding: 22px;
            margin-top: 35px;
            box-shadow: 0 5px 20px rgba(15, 23, 42, .06);
        }

        .selected-title {
            margin: 0 0 15px;
            font-size: 18px;
        }

        .selected-list {
            display: flex;
            flex-direction: column;
            gap: 9px;
        }

        .selected-item {
            display: flex;
            align-items: center;
            gap: 12px;
            background: #f7f9fc;
            padding: 11px 13px;
            border-radius: 10px;
        }

        .selected-order {
            width: 28px;
            height: 28px;
            border-radius: 8px;
            background: #245b91;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 700;
        }

        .selected-course {
            flex: 1;
            font-weight: 600;
            font-size: 14px;
        }

        .selected-hours {
            color: #245b91;
            font-size: 13px;
            font-weight: 700;
        }

        /* ERRORS */

        .errors {
            background: #fff1f1;
            border: 1px solid #fecaca;
            color: #991b1b;
            border-radius: 14px;
            padding: 16px;
            margin-bottom: 22px;
        }

        .errors strong {
            display: block;
            margin-bottom: 7px;
        }

        .errors ul {
            margin: 0;
            padding-left: 20px;
        }

        /* SUBMIT */

        .submit-area {
            margin-top: 28px;
            background: white;
            border-radius: 18px;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            box-shadow: 0 5px 20px rgba(15, 23, 42, .06);
        }

        .submit-info {
            font-size: 14px;
            color: #6b7280;
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
            transition: .2s;
        }

        .submit-button:hover {
            background: #194a78;
        }

        .submit-button:disabled {
            background: #cbd5e1;
            color: #64748b;
            cursor: not-allowed;
        }

        /* SUCCESS */

        .success {
            background: #ecfdf3;
            border: 1px solid #bbf7d0;
            color: #166534;
            border-radius: 14px;
            padding: 14px 16px;
            margin-bottom: 22px;
            font-weight: 600;
        }

        /* FOOTER */

        .footer {
            text-align: center;
            color: #94a3b8;
            font-size: 12px;
            padding: 30px 20px;
        }

        @media (max-width: 800px) {
            .course-grid {
                grid-template-columns: 1fr;
            }

            .student-card {
                align-items: flex-start;
                flex-direction: column;
            }

            .submit-area {
                flex-direction: column;
                align-items: stretch;
            }

            .submit-button {
                width: 100%;
            }
        }

        @media (max-width: 500px) {
            .header {
                padding: 22px 15px 60px;
            }

            .container {
                padding: 0 12px 40px;
            }

            .student-card {
                margin-left: 12px;
                margin-right: 12px;
                padding: 18px;
            }

            .brand h1 {
                font-size: 18px;
            }

            .course-card,
            .progress-card,
            .selected-card,
            .submit-area {
                border-radius: 14px;
            }
        }

        .remove-selection-button {
            margin-top: 12px;
            border: 1px solid #d1d5db;
            background: white;
            color: #64748b;
            border-radius: 8px;
            padding: 7px 10px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
        }

            .remove-selection-button:hover {
                background: #f1f5f9;
            }
    </style>
</head>

<body>

<div class="page">

    {{-- HEADER --}}
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


    {{-- STUDENT --}}
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
                    {{ $student->studentYears()->where('academic_year_id', $academicYear->id)->first()?->grade }}. Sınıf
                </p>
            </div>

        </div>

        <div class="year-badge">
            {{ $academicYear->name }} Eğitim Öğretim Yılı
        </div>

    </div>


    <main class="container">

        {{-- SUCCESS --}}
        @if(session('success'))
            <div class="success">
                ✓ {{ session('success') }}
            </div>
        @endif


        {{-- ERRORS --}}
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


        {{-- GRADE --}}
        @php
            $studentYear = $student->studentYears()
                ->where('academic_year_id', $academicYear->id)
                ->first();

            $grade = $studentYear?->grade ?? 5;

            $requiredHours = match ((int) $grade) {
                5, 6, 7, 8 => 5,
                default => 5,
            };

            $currentSelections = $selections
                ->sortBy('preference_order')
                ->values();

            $currentHours = $currentSelections->sum(
    fn ($selection) => $selection->gradeOption->weekly_hours
);
        @endphp


        {{-- PROGRESS --}}
        <section class="progress-card">

            <div class="progress-top">

                <div>
                    <div class="progress-title">
                        Ders Tercih Durumu
                    </div>

                    <div class="progress-message" id="progressMessage">
                        Tercihlerinizi tamamlayınız.
                    </div>
                </div>

                <div class="hours" id="hoursCounter">
                    {{ $currentHours }} / {{ $requiredHours }} saat
                </div>

            </div>

            <div class="progress-bar">
                <div
                    class="progress-fill"
                    id="progressFill"
                    style="width: {{ min(100, ($currentHours / $requiredHours) * 100) }}%"
                ></div>
            </div>

        </section>


        {{-- FORM --}}
        <form
            method="POST"
            action="{{ route('course-selections.store') }}"
            id="selectionForm"
        >

            @csrf

            {{-- DYNAMIC HIDDEN INPUTS --}}
            <div id="selectionInputs"></div>


            {{-- COURSES --}}
            @php
                $groupedCourses = $courses->groupBy(
                    fn($course) => $course->category->name
                );

                $categoryIndex = 0;
            @endphp


            @foreach($groupedCourses as $categoryName => $categoryCourses)

                @php
                    $categoryIndex++;
                @endphp

                <section class="category">

                    <div class="category-header">

                        <div class="category-number">
                            {{ $categoryIndex }}
                        </div>

                        <h2 class="category-title">
                            {{ $categoryName }}
                        </h2>

                    </div>


                    <div class="course-grid">

                        @foreach($categoryCourses as $course)

                            @php
                                $remaining = $service->remainingAttempts(
                                    $student,
                                    $course
                                );

                                $locked = $remaining <= 0;

                                $selected = $currentSelections
                                    ->firstWhere('course_id', $course->id);

                                $options = $course->gradeOptions
                                    ->where('grade', $grade)
                                    ->sortBy('weekly_hours')
                                    ->values();
                            @endphp


                            <article
                            class="course-card {{ $selected ? 'selected' : '' }} {{ $locked ? 'locked' : '' }}"
                            data-course-id="{{ $course->id }}"
                            data-course-name="{{ $course->name }}"
                            data-category-id="{{ $course->course_category_id }}"
                            data-locked="{{ $locked ? '1' : '0' }}"
                            >

                                <div class="course-head">

                                    <div class="course-name">
                                        {{ $course->name }}
                                    </div>

                                    <div class="selection-number">
                                        @if($selected)
                                            {{ $currentSelections->search($selected) + 1 }}
                                        @endif
                                    </div>

                                </div>


                                <div class="course-meta">

                                    @if($locked)

                                        Maksimum alma hakkınız doldu.

                                    @else

                                        Kalan alma hakkı:
                                        <strong>{{ $remaining }}</strong>

                                    @endif

                                </div>


                                @if($locked)

                                    <div class="lock-message">
                                        🔒 Bu dersi artık seçemezsiniz.
                                    </div>

                                @else

                                @if(!$selectionOpen)

                                <div class="lock-message">
                                🔒 Tercih dönemi kapalı olduğu için bu ders seçilemez.
                                </div>

                                @elseif($options->count())

                                        <div class="hours-label">
                                            Haftalık ders saati
                                        </div>


                                        <div class="hours-options">

                                            @foreach($options as $option)

                                                @php
                                                    $inputId =
                                                        'course_' .
                                                        $course->id .
                                                        '_option_' .
                                                        $option->id;
                                                @endphp

                                                <div class="hour-option">

                                                    <input
                                                        type="radio"
                                                        id="{{ $inputId }}"
                                                        name="course_option_{{ $course->id }}"
                                                        value="{{ $option->id }}"
                                                        data-course-id="{{ $course->id }}"
                                                        data-course-name="{{ $course->name }}"
                                                        data-hours="{{ $option->weekly_hours }}"
                                                        class="course-option"
                                                        {{ $selected && $selected->course_grade_option_id == $option->id ? 'checked' : '' }}
                                                    >

                                                    <label for="{{ $inputId }}">
                                                        {{ $option->weekly_hours }} saat
                                                    </label>

                                                </div>

                                            @endforeach

                                        </div>

                                    @else

                                        <div class="lock-message">
                                            Bu sınıf için tanımlı ders saati bulunamadı.
                                        </div>

                                    @endif

                                @endif

                            </article>

                        @endforeach

                    </div>

                </section>

            @endforeach


            {{-- SELECTED --}}
            <section class="selected-card">

                <h2 class="selected-title">
                    Tercih Sıralamanız
                </h2>

                <div
                    class="selected-list"
                    id="selectedList"
                ></div>

                <div
                    id="emptySelection"
                    style="color:#94a3b8;font-size:14px;"
                >
                    Henüz ders seçmediniz.
                </div>

            </section>


            {{-- SUBMIT --}}
            <div class="submit-area">

            @if(!$selectionOpen)

            <div
                class="closed-period-message"
                style="
                width:100%;
                padding:16px 20px;
                border-radius:12px;
                background:#fef2f2;
                border:1px solid #fecaca;
                color:#991b1b;
                font-weight:700;
                text-align:center;
            "
        >
            🔒 Tercih dönemi şu anda kapalıdır.
            <div
                style="
                    margin-top:5px;
                    font-size:13px;
                    font-weight:500;
                "
            >
                Tercihler şu anda değiştirilemez.
            </div>
        </div>

    @else

        <div class="submit-info" id="submitInfo">
            Toplam {{ $requiredHours }} saatlik ders tercihi yapmalısınız.
        </div>

        <button
            type="submit"
            class="submit-button"
            id="submitButton"
            disabled
        >
            Tercihleri Gönder
        </button>

    @endif

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

    /*
     * Seçimler:
     *
     * [
     *   {
     *      courseId: 3,
     *      courseName: "...",
     *      optionId: 6,
     *      hours: 2
     *   }
     * ]
     */

    let selections = [];

@foreach ($currentSelections as $selection)
    selections.push({
        courseId: {{ $selection->course_id }},
        courseName: @json($selection->course->name),
        optionId: {{ $selection->course_grade_option_id }},
        hours: {{ $selection->gradeOption->weekly_hours }}
    });
@endforeach


    function totalHours()
    {
        return selections.reduce(
            (total, selection) => total + Number(selection.hours),
            0
        );
    }


    function updateInterface()
    {
        const total = totalHours();

        const counter = document.getElementById('hoursCounter');
        const fill = document.getElementById('progressFill');
        const message = document.getElementById('progressMessage');
        const submitButton = document.getElementById('submitButton');
        const submitInfo = document.getElementById('submitInfo');

        counter.textContent =
            total + ' / ' + requiredHours + ' saat';


        const percentage = Math.min(
            100,
            (total / requiredHours) * 100
        );

        fill.style.width = percentage + '%';


        if (total === requiredHours) {

            counter.classList.add('complete');
            fill.classList.add('complete');

            message.textContent =
                '✓ Tercihiniz tamamlandı. Gönderebilirsiniz.';

            submitButton.disabled = false;

            submitInfo.textContent =
                '✓ ' + requiredHours + ' saatlik tercihiniz hazır.';

        } else {

            counter.classList.remove('complete');
            fill.classList.remove('complete');

            submitButton.disabled = true;

            if (total < requiredHours) {

                message.textContent =
                    'Tercihinizi tamamlamak için ' +
                    (requiredHours - total) +
                    ' saat daha seçmelisiniz.';

                submitInfo.textContent =
                    'Toplam ' +
                    requiredHours +
                    ' saatlik ders tercihi yapmalısınız.';

            } else {

                message.textContent =
                    '⚠ Toplam ders saati sınırı aşıldı.';

                submitInfo.textContent =
                    'Toplam ders saati ' +
                    requiredHours +
                    ' saati geçemez.';
            }
        }


        updateCourseCards();
        updateSelectedList();
        updateHiddenInputs();
    }


    function updateCourseCards()
    {
        /*
         * Hangi kategorilerde zaten ders seçilmiş?
         */
        const selectedCategories = new Set();

        selections.forEach(selection => {
            const selectedCard = document.querySelector(
                `.course-card[data-course-id="${selection.courseId}"]`
            );

            if (selectedCard) {
                selectedCategories.add(
                    Number(selectedCard.dataset.categoryId)
                );
            }
        });

        document
            .querySelectorAll('.course-card')
            .forEach(card => {

            if (!selectionOpen) {

                const removeButton =
                    card.querySelector('.remove-selection-button');

                if (removeButton) {
                    removeButton.remove();
                }
}

                const courseId =
                    Number(card.dataset.courseId);

                const categoryId =
                    Number(card.dataset.categoryId);

                const selectionIndex =
                    selections.findIndex(
                        selection =>
                            selection.courseId === courseId
                    );

                const isSelected =
                    selectionIndex >= 0;

                /*
                 * Seçili / seçili değil görünümünü yönet.
                 * Seçim kaldırıldığında selected class ve
                 * Seçimi kaldır butonu burada temizlenir.
                 */
                if (isSelected) {

                    card.classList.add('selected');

                    const number =
                        card.querySelector('.selection-number');

                    if (number) {
                        number.textContent =
                            selectionIndex + 1;
                    }

                    /*
                     * Seçilen dersin doğru saat seçeneğini işaretle.
                     */
                    card.querySelectorAll('.course-option')
                        .forEach(input => {
                            input.checked =
                                Number(input.value) ===
                                Number(selections[selectionIndex].optionId);
                        });

                    let removeButton =
                        card.querySelector('.remove-selection-button');

                    if (selectionOpen && !removeButton) {

                        removeButton =
                            document.createElement('button');

                        removeButton.type = 'button';
                        removeButton.className =
                            'remove-selection-button';
                        removeButton.textContent =
                            'Seçimi kaldır';

                        removeButton.addEventListener(
                            'click',
                            function (event) {

                                event.preventDefault();
                                event.stopPropagation();

                                selections =
                                    selections.filter(
                                        selection =>
                                            selection.courseId !== courseId
                                    );

                                updateInterface();
                            }
                        );

                        card.appendChild(removeButton);
                    }

                } else {

                    /*
                     * Ders artık seçili değilse kartın bütün
                     * görsel durumunu sıfırla.
                     */
                    card.classList.remove('selected');
                    card.classList.remove('locked');

                    const number =
                        card.querySelector('.selection-number');

                    if (number) {
                        number.textContent = '';
                    }

                    /*
                     * Seçili radio butonunu da temizle.
                     */
                    card.querySelectorAll('.course-option')
                        .forEach(input => {
                            input.checked = false;
                        });

                    /*
                     * Seçimi kaldır butonunu kaldır.
                     */
                    const removeButton =
                        card.querySelector('.remove-selection-button');

                    if (removeButton) {
                        removeButton.remove();
                    }
                }

                /*
                 * Aynı kategoriden başka bir ders seçilmişse,
                 * seçili ders dışındaki kartları kilitle.
                 */
                const categoryTaken =
                    selectedCategories.has(categoryId);

                const shouldLock =
                    categoryTaken && !isSelected;

                /*
                 * Öğrencinin maksimum alma hakkı dolmuş dersler
                 * sunucu tarafından locked olarak gelir.
                 */
                const serverLocked =
                    card.dataset.locked === '1';

                const inputs =
                    card.querySelectorAll('.course-option');

                if (shouldLock && !serverLocked) {

                    card.classList.add('locked');

                    inputs.forEach(input => {
                        input.disabled = true;
                    });

                    let message =
                        card.querySelector('.category-lock-message');

                    if (!message) {
                        message =
                            document.createElement('div');

                        message.className =
                            'lock-message category-lock-message';

                        card.appendChild(message);
                    }

                    message.textContent =
                        '🔒 Bu gruptan başka bir ders seçtiğiniz için bu ders seçilemez.';

                } else {

                    if (!serverLocked) {
                        card.classList.remove('locked');
                    }

                    inputs.forEach(input => {
                        input.disabled = serverLocked;
                    });

                    const message =
                        card.querySelector('.category-lock-message');

                    if (message) {
                        message.remove();
                    }
                }
            });
    }


    function updateSelectedList()
    {
        const list =
            document.getElementById('selectedList');

        const empty =
            document.getElementById('emptySelection');

        list.innerHTML = '';

        if (!selections.length) {

            empty.style.display = 'block';

            return;
        }

        empty.style.display = 'none';


        selections.forEach((selection, index) => {

            const item =
                document.createElement('div');

            item.className =
                'selected-item';

            item.innerHTML = `
                <div class="selected-order">
                    ${index + 1}
                </div>

                <div class="selected-course">
                    ${escapeHtml(selection.courseName)}
                </div>

                <div class="selected-hours">
                    ${selection.hours} saat
                </div>
            `;

            list.appendChild(item);

        });
    }


    function updateHiddenInputs()
    {
        const container =
            document.getElementById('selectionInputs');

        container.innerHTML = '';

        selections.forEach(
            (selection, index) => {

                const courseInput =
                    document.createElement('input');

                courseInput.type = 'hidden';

                courseInput.name =
                    `selections[${index}][course_id]`;

                courseInput.value =
                    selection.courseId;

                container.appendChild(courseInput);


                const optionInput =
                    document.createElement('input');

                optionInput.type = 'hidden';

                optionInput.name =
                    `selections[${index}][course_grade_option_id]`;

                optionInput.value =
                    selection.optionId;

                container.appendChild(optionInput);

            }
        );
    }


    function escapeHtml(text)
    {
        const div =
            document.createElement('div');

        div.textContent = text;

        return div.innerHTML;
    }


    /*
     * Ders saati butonları
     */

    document
        .querySelectorAll('.course-option')
        .forEach(input => {

            input.addEventListener(
                'change',
                function () {

                    const courseId =
                        Number(this.dataset.courseId);

                    const courseName =
                        this.dataset.courseName;

                    const optionId =
                        Number(this.value);

                    const hours =
                        Number(this.dataset.hours);


                    const existingIndex =
                        selections.findIndex(
                            selection =>
                                selection.courseId === courseId
                        );


                    /*
                     * Aynı ders daha önce seçilmişse
                     * saat seçeneğini değiştir.
                     */

                    if (existingIndex >= 0) {

                        selections[existingIndex] = {
                            courseId,
                            courseName,
                            optionId,
                            hours
                        };

                    } else {

                        /*
                         * Yeni ders tercihi
                         */

                        selections.push({
                            courseId,
                            courseName,
                            optionId,
                            hours
                        });

                    }

                    updateInterface();
                }
            );

        });


    /*
     * İlk açılış
     */

    updateInterface();

</script>

</body>
</html>