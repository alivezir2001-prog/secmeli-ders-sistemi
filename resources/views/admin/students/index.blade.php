<!DOCTYPE html>
<html lang="tr">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>
        Öğrenciler - Yönetim
    </title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f1f5f9;
            color: #0f172a;
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

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: end;
            gap: 20px;
            margin-bottom: 20px;
        }

        .title h2 {
            margin: 0 0 5px;
            font-size: 21px;
        }

        .title p {
            margin: 0;
            color: #64748b;
            font-size: 13px;
        }

        .year-select {
            min-width: 220px;
            padding: 10px 12px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            background: white;
        }

        .message {
            padding: 13px 15px;
            border-radius: 10px;
            margin-bottom: 18px;
            font-size: 13px;
        }

        .success {
            background: #ecfdf5;
            border: 1px solid #bbf7d0;
            color: #166534;
        }

        .error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
        }

        .filters {
            background: white;
            border: 1px solid #dbe3ec;
            border-radius: 14px;
            padding: 16px;
            margin-bottom: 18px;
        }

        .filter-grid {
            display: grid;
            grid-template-columns: 1.5fr 1fr 1fr 1fr auto;
            gap: 10px;
            align-items: end;
        }

        .field label {
            display: block;
            margin-bottom: 6px;
            color: #64748b;
            font-size: 10px;
            font-weight: 800;
        }

        .field input,
        .field select {
            width: 100%;
            padding: 10px 11px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            background: white;
        }

        .button {
            border: 0;
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
        }

        .button-primary {
            background: #245b91;
            color: white;
        }

        .button-secondary {
            background: #e2e8f0;
            color: #334155;
        }

        .button-success {
            background: #166534;
            color: white;
        }

        .button-danger {
            background: #dc2626;
            color: white;
        }

        .add-button {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            background: #166534;
            color: white;
            text-decoration: none;
            border-radius: 9px;
            padding: 11px 15px;
            font-size: 13px;
            font-weight: 800;
        }

        .table-card {
            background: white;
            border: 1px solid #dbe3ec;
            border-radius: 14px;
            overflow: hidden;
        }

        .table-wrap {
            overflow-x: auto;
        }

        table {
            width: 100%;
            min-width: 850px;
            border-collapse: collapse;
        }

        th {
            padding: 12px 14px;
            background: #f8fafc;
            color: #64748b;
            border-bottom: 1px solid #dbe3ec;
            text-align: left;
            font-size: 10px;
            font-weight: 800;
            white-space: nowrap;
        }

        td {
            padding: 13px 14px;
            border-bottom: 1px solid #edf2f7;
            font-size: 12px;
            vertical-align: middle;
        }

        tr:last-child td {
            border-bottom: 0;
        }

        .student-name {
            font-weight: 800;
        }

        .student-number {
            margin-top: 3px;
            color: #64748b;
            font-size: 10px;
        }

        .class-info {
            font-weight: 700;
        }

        .class-empty {
            color: #94a3b8;
        }

        .status {
            display: inline-flex;
            align-items: center;
            padding: 5px 9px;
            border-radius: 999px;
            font-size: 10px;
            font-weight: 800;
        }

        .status-active {
            background: #dcfce7;
            color: #166534;
        }

        .status-passive {
            background: #e2e8f0;
            color: #475569;
        }

        .actions {
            display: flex;
            gap: 7px;
            flex-wrap: wrap;
        }

        .actions a,
        .actions button {
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .edit-form,
        .status-form {
            margin: 0;
        }

        .empty {
            padding: 40px;
            text-align: center;
            color: #64748b;
        }

        .add-panel {
            display: none;
            background: white;
            border: 1px solid #dbe3ec;
            border-radius: 14px;
            padding: 20px;
            margin-bottom: 18px;
        }

        .add-panel.open {
            display: block;
        }

        .add-panel h3 {
            margin: 0 0 15px;
            font-size: 17px;
        }

        .add-grid {
            display: grid;
            grid-template-columns:
                repeat(4, minmax(0, 1fr));
            gap: 12px;
        }

        .form-actions {
            margin-top: 15px;
            display: flex;
            justify-content: flex-end;
            gap: 8px;
        }

        .modal-backdrop {
            position: fixed;
            inset: 0;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 20px;
            background: rgba(15, 23, 42, .45);
            z-index: 1000;
        }

        .modal-backdrop.open {
            display: flex;
        }

        .student-modal {
            width: min(680px, 100%);
            max-height: 90vh;
            overflow-y: auto;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(15, 23, 42, .2);
        }

        .student-modal-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
            padding: 20px 22px;
            border-bottom: 1px solid #e2e8f0;
        }

        .student-modal-header h3 {
            margin: 0;
            font-size: 18px;
        }

        .student-modal-header p {
            margin: 5px 0 0;
            color: #64748b;
            font-size: 12px;
        }

        .modal-close {
            border: 0;
            background: transparent;
            color: #64748b;
            font-size: 28px;
            line-height: 1;
            cursor: pointer;
        }

        .student-modal form {
            padding: 20px 22px 22px;
        }

        .edit-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .modal-actions {
            display: flex;
            justify-content: flex-end;
            gap: 8px;
            margin-top: 18px;
        }

        @media (max-width: 900px) {

            .topbar {
                flex-direction: column;
                align-items: stretch;
            }

            .filter-grid {
                grid-template-columns: 1fr 1fr;
            }

            .add-grid {
                grid-template-columns: 1fr 1fr;
            }

        }

        @media (max-width: 600px) {

            .container {
                padding-left: 12px;
                padding-right: 12px;
            }

            .filter-grid,
            .add-grid {
                grid-template-columns: 1fr;
            }

        }
    </style>

</head>

<body>

    <header class="header">

        <h1>
            Öğrenci Yönetimi
        </h1>

        <p>
            Yönetim → Öğrenciler
        </p>

    </header>

    <main class="container">

        @if(session('success'))

        <div class="message success">
            {{ session('success') }}
        </div>

        @endif

        @if($errors->any())

        <div class="message error">

            @foreach($errors->all() as $error)

            <div>
                {{ $error }}
            </div>

            @endforeach

        </div>

        @endif


        <div class="topbar">

            <div class="title">

                <h2>
                    {{ $academicYear->name }} Öğrencileri
                </h2>

                <p>
                    Öğrenci kayıtlarını, sınıf/şube bilgilerini
                    ve aktiflik durumlarını yönetin.
                </p>

            </div>

            <div>

                <form method="GET">

                    @if($search !== '')

                    <input
                        type="hidden"
                        name="search"
                        value="{{ $search }}">

                    @endif

                    @if($status !== 'active')

                    <input
                        type="hidden"
                        name="status"
                        value="{{ $status }}">

                    @endif

                    @if($grade !== 'all')

                    <input
                        type="hidden"
                        name="grade"
                        value="{{ $grade }}">

                    @endif

                    @if($section !== 'all')

                    <input
                        type="hidden"
                        name="section"
                        value="{{ $section }}">

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

        </div>


        <section class="filters">

            <form method="GET">

                <input
                    type="hidden"
                    name="academic_year_id"
                    value="{{ $academicYear->id }}">

                <div class="filter-grid">

                    <div class="field">

                        <label>
                            ÖĞRENCİ ARA
                        </label>

                        <input
                            type="text"
                            name="search"
                            value="{{ $search }}"
                            placeholder="Ad, soyad veya öğrenci no...">

                    </div>


                    <div class="field">

                        <label>
                            DURUM
                        </label>

                        <select name="status">

                            <option
                                value="active"
                                {{ $status === 'active' ? 'selected' : '' }}>
                                Aktif
                            </option>

                            <option
                                value="passive"
                                {{ $status === 'passive' ? 'selected' : '' }}>
                                Pasif
                            </option>

                            <option
                                value="all"
                                {{ $status === 'all' ? 'selected' : '' }}>
                                Tümü
                            </option>

                        </select>

                    </div>


                    <div class="field">

                        <label>
                            SINIF
                        </label>

                        <select name="grade" id="gradeFilter">

                            <option value="all">
                                Tüm Sınıflar
                            </option>

                            @foreach($gradeOptions as $gradeOption)

                            <option
                                value="{{ $gradeOption }}"
                                {{ (string) $grade === (string) $gradeOption ? 'selected' : '' }}>
                                {{ $gradeOption }}. Sınıf
                            </option>

                            @endforeach

                        </select>

                    </div>


                    <div class="field">

                        <label>
                            ŞUBE
                        </label>

                        <select name="section" id="sectionFilter">

                            <option value="all">
                                Tüm Şubeler
                            </option>

                            @foreach($sectionOptions as $sectionOption)

                            <option
                                value="{{ $sectionOption }}"
                                {{ $section === $sectionOption ? 'selected' : '' }}>
                                {{ $sectionOption }}
                            </option>

                            @endforeach

                        </select>

                    </div>


                    <button
                        type="submit"
                        class="button button-primary">
                        Filtrele
                    </button>

                </div>

            </form>

        </section>


        <section
            class="add-panel"
            id="addStudentPanel">

            <h3>
                Yeni Öğrenci
            </h3>

            <form
                method="POST"
                action="{{ route('admin.students.store') }}">

                @csrf

                <input
                    type="hidden"
                    name="academic_year_id"
                    value="{{ $academicYear->id }}">

                <div class="add-grid">

                    <div class="field">

                        <label>
                            ÖĞRENCİ NO
                        </label>

                        <input
                            type="text"
                            name="student_number"
                            maxlength="30"
                            value="{{ old('student_number') }}">

                    </div>


                    <div class="field">

                        <label>
                            AD
                        </label>

                        <input
                            type="text"
                            name="first_name"
                            required
                            value="{{ old('first_name') }}">

                    </div>


                    <div class="field">

                        <label>
                            SOYAD
                        </label>

                        <input
                            type="text"
                            name="last_name"
                            required
                            value="{{ old('last_name') }}">

                    </div>


                    <div class="field">

                        <label>
                            T.C. KİMLİK NO
                        </label>

                        <input
                            type="text"
                            name="national_id"
                            maxlength="11"
                            minlength="11"
                            value="{{ old('national_id') }}">

                    </div>


                    <div class="field">

                        <label>
                            SINIF
                        </label>

                        <select
                            name="grade"
                            required>

                            <option value="">
                                Sınıf seçiniz
                            </option>

                            @for($grade = 1; $grade <= 12; $grade++)

                                <option
                                value="{{ $grade }}"
                                {{ (string) old('grade') === (string) $grade ? 'selected' : '' }}>
                                {{ $grade }}. Sınıf
                                </option>

                                @endfor

                        </select>

                    </div>


                    <div class="field">

                        <label>
                            ŞUBE
                        </label>

                        <input
                            type="text"
                            name="section"
                            maxlength="20"
                            value="{{ old('section') }}"
                            placeholder="Örn. A">

                    </div>

                </div>


                <div class="form-actions">

                    <button
                        type="button"
                        class="button button-secondary"
                        onclick="toggleAddStudent()">
                        İptal
                    </button>

                    <button
                        type="submit"
                        class="button button-success">
                        Öğrenciyi Kaydet
                    </button>

                </div>

            </form>

        </section>


        <div
            style="
                margin-bottom:15px;
                display:flex;
                justify-content:flex-end;
                gap:8px;
                flex-wrap:wrap;
            ">

            <a
                href="{{ route(
            'admin.students.import',
            [
                'academic_year_id' =>
                    $academicYear->id
            ]
        ) }}"
                class="add-button"
                style="background:#245b91;">
                ⇧ PDF'den İçe Aktar
            </a>


            <button
                type="button"
                class="add-button"
                onclick="toggleAddStudent()">
                ＋ Yeni Öğrenci
            </button>

        </div>


        <section class="table-card">

            @if($students->isEmpty())

            <div class="empty">

                Bu filtrelere uygun öğrenci bulunamadı.

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
                                SINIF / ŞUBE
                            </th>

                            <th>
                                DURUM
                            </th>

                            <th>
                                İŞLEMLER
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        @foreach($students as $student)

                        @php
                        $studentYear =
                        $student->studentYears->first();
                        @endphp

                        <tr>

                            <td>

                                <div class="student-name">

                                    {{ $student->first_name }}
                                    {{ $student->last_name }}

                                </div>

                                <div class="student-number">

                                    Öğrenci No:
                                    {{ $student->student_number ?? '-' }}

                                </div>

                            </td>


                            <td>

                                @if($studentYear)

                                <div class="class-info">

                                    {{ $studentYear->grade }}. Sınıf

                                    @if($studentYear->section)
                                    / {{ $studentYear->section }}
                                    @endif

                                </div>

                                @else

                                <span class="class-empty">
                                    Bu eğitim yılı için sınıf kaydı yok
                                </span>

                                @endif

                            </td>


                            <td>

                                @if($student->active)

                                <span class="status status-active">
                                    Aktif
                                </span>

                                @else

                                <span class="status status-passive">
                                    Pasif
                                </span>

                                @endif

                            </td>


                            <td>

                                <div class="actions">

                                    <button
                                        type="button"
                                        class="button button-secondary edit-student-button"
                                        data-student-id="{{ $student->id }}"
                                        data-student-number="{{ $student->student_number }}"
                                        data-national-id="{{ $student->national_id }}"
                                        data-first-name="{{ $student->first_name }}"
                                        data-last-name="{{ $student->last_name }}"
                                        data-grade="{{ $studentYear->grade ?? '' }}"
                                        data-section="{{ $studentYear->section ?? '' }}"
                                        data-update-url="{{ route('admin.students.update', $student) }}">
                                        Düzenle
                                    </button>


                                    <form
                                        method="POST"
                                        action="{{ route(
                                            'admin.students.status',
                                            $student 
                                        ) }}"
                                        class="status-form">

                                        @csrf
                                        @method('PUT')

                                        <button
                                            type="submit"
                                            class="button {{ $student->active ? 'button-danger' : 'button-success' }}">
                                            {{ $student->active ? 'Pasif Yap' : 'Aktif Yap' }}
                                        </button>

                                    </form>

                                </div>

                            </td>

                        </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

            @endif

        </section>

    </main>


    <div
        id="sectionsByGradeData"
        data-sections='@json($sectionsByGrade)'
        style="display:none;"></div>


    <div
        class="modal-backdrop"
        id="editStudentModal"
        aria-hidden="true">

        <div
            class="student-modal"
            role="dialog"
            aria-modal="true"
            aria-labelledby="editStudentTitle">

            <div class="student-modal-header">

                <div>
                    <h3 id="editStudentTitle">
                        Öğrenci Bilgilerini Düzenle
                    </h3>

                    <p id="editStudentSubtitle">
                        Öğrenci bilgilerini güncelleyin.
                    </p>
                </div>

                <button
                    type="button"
                    class="modal-close"
                    id="closeEditStudentModal"
                    aria-label="Kapat">
                    ×
                </button>

            </div>

            <form
                method="POST"
                id="editStudentForm">

                @csrf
                @method('PUT')

                <input
                    type="hidden"
                    name="academic_year_id"
                    value="{{ $academicYear->id }}">

                <div class="edit-grid">

                    <div class="field">
                        <label>ÖĞRENCİ NO</label>
                        <input
                            type="text"
                            name="student_number"
                            id="editStudentNumber"
                            maxlength="30">
                    </div>

                    <div class="field">
                        <label>T.C. KİMLİK NO</label>
                        <input
                            type="text"
                            name="national_id"
                            id="editNationalId"
                            maxlength="11">
                    </div>

                    <div class="field">
                        <label>AD</label>
                        <input
                            type="text"
                            name="first_name"
                            id="editFirstName"
                            required>
                    </div>

                    <div class="field">
                        <label>SOYAD</label>
                        <input
                            type="text"
                            name="last_name"
                            id="editLastName"
                            required>
                    </div>

                    <div class="field">
                        <label>SINIF</label>
                        <select
                            name="grade"
                            id="editGrade"
                            required>
                            @for($grade = 1; $grade <= 12; $grade++)
                                <option value="{{ $grade }}">
                                {{ $grade }}. Sınıf
                                </option>
                                @endfor
                        </select>
                    </div>

                    <div class="field">
                        <label>ŞUBE</label>
                        <input
                            type="text"
                            name="section"
                            id="editSection"
                            maxlength="20">
                    </div>

                </div>

                <div class="modal-actions">

                    <button
                        type="button"
                        class="button button-secondary"
                        id="cancelEditStudent">
                        Vazgeç
                    </button>

                    <button
                        type="submit"
                        class="button button-primary">
                        Değişiklikleri Kaydet
                    </button>

                </div>

            </form>

        </div>

    </div>


    <script>
        function toggleAddStudent() {
            const panel =
                document.getElementById(
                    'addStudentPanel'
                );

            if (!panel) {
                return;
            }

            panel.classList.toggle('open');
        }


        const sectionsData =
            document.getElementById(
                'sectionsByGradeData'
            );

        const sectionsByGrade =
            sectionsData ?
            JSON.parse(
                sectionsData.dataset.sections
            ) : {};

        const gradeFilter =
            document.getElementById(
                'gradeFilter'
            );

        const sectionFilter =
            document.getElementById(
                'sectionFilter'
            );

        function updateSectionFilter() {
            if (!gradeFilter || !sectionFilter) {
                return;
            }

            const selectedGrade =
                gradeFilter.value;

            const currentSection =
                sectionFilter.value;

            sectionFilter.innerHTML = '';

            const allOption =
                document.createElement('option');

            allOption.value = 'all';
            allOption.textContent = 'Tüm Şubeler';

            sectionFilter.appendChild(
                allOption
            );

            if (
                selectedGrade === 'all' ||
                !sectionsByGrade[selectedGrade]
            ) {
                sectionFilter.value = 'all';
                return;
            }

            sectionsByGrade[selectedGrade]
                .forEach(section => {

                    const option =
                        document.createElement('option');

                    option.value = section;
                    option.textContent = section;

                    if (
                        section === currentSection
                    ) {
                        option.selected = true;
                    }

                    sectionFilter.appendChild(
                        option
                    );
                });

            const sectionStillExists =
                Array.from(
                    sectionFilter.options
                ).some(
                    option =>
                    option.value === currentSection
                );

            if (!sectionStillExists) {
                sectionFilter.value = 'all';
            }
        }

        if (gradeFilter) {
            gradeFilter.addEventListener(
                'change',
                updateSectionFilter
            );
        }

        updateSectionFilter();


        const editModal =
            document.getElementById(
                'editStudentModal'
            );

        const editForm =
            document.getElementById(
                'editStudentForm'
            );

        const closeEditButton =
            document.getElementById(
                'closeEditStudentModal'
            );

        const cancelEditButton =
            document.getElementById(
                'cancelEditStudent'
            );

        function closeStudentModal() {
            if (!editModal) {
                return;
            }

            editModal.classList.remove('open');
            editModal.setAttribute(
                'aria-hidden',
                'true'
            );
        }

        function openStudentModal(button) {
            if (!editModal || !editForm) {
                return;
            }

            editForm.action =
                button.dataset.updateUrl;

            document.getElementById(
                    'editStudentNumber'
                ).value =
                button.dataset.studentNumber || '';

            document.getElementById(
                    'editNationalId'
                ).value =
                button.dataset.nationalId || '';

            document.getElementById(
                    'editFirstName'
                ).value =
                button.dataset.firstName || '';

            document.getElementById(
                    'editLastName'
                ).value =
                button.dataset.lastName || '';

            document.getElementById(
                    'editGrade'
                ).value =
                button.dataset.grade || '';

            document.getElementById(
                    'editSection'
                ).value =
                button.dataset.section || '';

            document.getElementById(
                    'editStudentSubtitle'
                ).textContent =
                (button.dataset.firstName || '') +
                ' ' +
                (button.dataset.lastName || '');

            editModal.classList.add('open');
            editModal.setAttribute(
                'aria-hidden',
                'false'
            );

        }

        document
            .querySelectorAll('.edit-student-button')
            .forEach(button => {
                button.addEventListener(
                    'click',
                    () => openStudentModal(button)
                );
            });

        if (closeEditButton) {
            closeEditButton.addEventListener(
                'click',
                closeStudentModal
            );
        }

        if (cancelEditButton) {
            cancelEditButton.addEventListener(
                'click',
                closeStudentModal
            );
        }

        if (editModal) {
            editModal.addEventListener(
                'click',
                event => {
                    if (event.target === editModal) {
                        closeStudentModal();
                    }
                }
            );
        }

        document.addEventListener(
            'keydown',
            event => {
                if (
                    event.key === 'Escape' &&
                    editModal &&
                    editModal.classList.contains('open')
                ) {
                    closeStudentModal();
                }
            }
        );
    </script>

</body>

</html>