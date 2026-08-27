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
            grid-template-columns: 1.5fr 1fr 1fr auto;
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

        .student-modal {
            position: fixed;
            inset: 0;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 20px;
            background: rgba(15, 23, 42, .45);
            z-index: 1000;
        }

        .student-modal.open {
            display: flex;
        }

        .student-modal-dialog {
            width: min(560px, 100%);
            max-height: calc(100vh - 40px);
            overflow-y: auto;
            background: white;
            border-radius: 14px;
            border: 1px solid #dbe3ec;
            box-shadow: 0 20px 60px rgba(15, 23, 42, .25);
        }

        .student-modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            padding: 16px 18px;
            border-bottom: 1px solid #e2e8f0;
        }

        .student-modal-header h3 {
            margin: 0;
            font-size: 17px;
        }

        .modal-close {
            width: 34px;
            height: 34px;
            border: 0;
            border-radius: 8px;
            background: #e2e8f0;
            color: #334155;
            font-size: 20px;
            line-height: 1;
            cursor: pointer;
        }

        .student-modal-body {
            padding: 18px;
        }

        .student-modal-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .student-modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 8px;
            margin-top: 18px;
            padding-top: 16px;
            border-top: 1px solid #e2e8f0;
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

            .student-modal-grid {
                grid-template-columns: 1fr;
            }

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


                    <div></div>


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
                                        data-update-url="{{ route('admin.students.update', $student) }}"
                                        data-student-number="{{ $student->student_number ?? '' }}"
                                        data-national-id="{{ $student->national_id ?? '' }}"
                                        data-first-name="{{ $student->first_name }}"
                                        data-last-name="{{ $student->last_name }}"
                                        data-grade="{{ $studentYear->grade ?? '' }}"
                                        data-section="{{ $studentYear->section ?? '' }}">
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
        class="student-modal"
        id="studentEditModal"
        aria-hidden="true">
        <div
            class="student-modal-dialog"
            role="dialog"
            aria-modal="true"
            aria-labelledby="studentEditModalTitle">
            <div class="student-modal-header">
                <h3 id="studentEditModalTitle">
                    Öğrenci Bilgilerini Düzenle
                </h3>

                <button
                    type="button"
                    class="modal-close"
                    id="closeStudentModal"
                    aria-label="Kapat">
                    ×
                </button>
            </div>

            <div class="student-modal-body">

                <form
                    method="POST"
                    id="studentEditForm">
                    @csrf
                    @method('PUT')

                    <input
                        type="hidden"
                        name="academic_year_id"
                        value="{{ $academicYear->id }}">

                    <div class="student-modal-grid">

                        <div class="field">
                            <label for="editStudentNumber">
                                ÖĞRENCİ NO
                            </label>

                            <input
                                type="text"
                                id="editStudentNumber"
                                name="student_number"
                                maxlength="30">
                        </div>

                        <div class="field">
                            <label for="editNationalId">
                                T.C. KİMLİK NO
                            </label>

                            <input
                                type="text"
                                id="editNationalId"
                                name="national_id"
                                maxlength="11">
                        </div>

                        <div class="field">
                            <label for="editFirstName">
                                AD
                            </label>

                            <input
                                type="text"
                                id="editFirstName"
                                name="first_name"
                                required>
                        </div>

                        <div class="field">
                            <label for="editLastName">
                                SOYAD
                            </label>

                            <input
                                type="text"
                                id="editLastName"
                                name="last_name"
                                required>
                        </div>

                        <div class="field">
                            <label for="editGrade">
                                SINIF
                            </label>

                            <select
                                id="editGrade"
                                name="grade"
                                required>
                                @for($grade = 1; $grade <= 12; $grade++)

                                    <option
                                    value="{{ $grade }}">
                                    {{ $grade }}. Sınıf
                                    </option>

                                    @endfor
                            </select>
                        </div>

                        <div class="field">
                            <label for="editSection">
                                ŞUBE
                            </label>

                            <input
                                type="text"
                                id="editSection"
                                name="section"
                                maxlength="20">
                        </div>

                    </div>

                    <div class="student-modal-footer">

                        <button
                            type="button"
                            class="button button-secondary"
                            id="cancelStudentModal">
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

        const studentEditModal =
            document.getElementById(
                'studentEditModal'
            );

        const studentEditForm =
            document.getElementById(
                'studentEditForm'
            );

        const editStudentNumber =
            document.getElementById(
                'editStudentNumber'
            );

        const editNationalId =
            document.getElementById(
                'editNationalId'
            );

        const editFirstName =
            document.getElementById(
                'editFirstName'
            );

        const editLastName =
            document.getElementById(
                'editLastName'
            );

        const editGrade =
            document.getElementById(
                'editGrade'
            );

        const editSection =
            document.getElementById(
                'editSection'
            );

        const closeStudentModalButton =
            document.getElementById(
                'closeStudentModal'
            );

        const cancelStudentModalButton =
            document.getElementById(
                'cancelStudentModal'
            );

        function closeStudentModal() {
            if (!studentEditModal) {
                return;
            }

            studentEditModal.classList.remove(
                'open'
            );

            studentEditModal.setAttribute(
                'aria-hidden',
                'true'
            );

            document.body.style.overflow = '';
        }

        function openStudentModal(button) {
            if (!studentEditModal || !studentEditForm) {
                return;
            }

            studentEditForm.action =
                button.dataset.updateUrl;

            editStudentNumber.value =
                button.dataset.studentNumber || '';

            editNationalId.value =
                button.dataset.nationalId || '';

            editFirstName.value =
                button.dataset.firstName || '';

            editLastName.value =
                button.dataset.lastName || '';

            editGrade.value =
                button.dataset.grade || '';

            editSection.value =
                button.dataset.section || '';

            studentEditModal.classList.add(
                'open'
            );

            studentEditModal.setAttribute(
                'aria-hidden',
                'false'
            );

            document.body.style.overflow = 'hidden';

            editFirstName.focus();
        }

        document
            .querySelectorAll(
                '.edit-student-button'
            )
            .forEach(button => {
                button.addEventListener(
                    'click',
                    function() {
                        openStudentModal(this);
                    }
                );
            });

        if (closeStudentModalButton) {
            closeStudentModalButton.addEventListener(
                'click',
                closeStudentModal
            );
        }

        if (cancelStudentModalButton) {
            cancelStudentModalButton.addEventListener(
                'click',
                closeStudentModal
            );
        }

        if (studentEditModal) {
            studentEditModal.addEventListener(
                'click',
                function(event) {
                    if (event.target === studentEditModal) {
                        closeStudentModal();
                    }
                }
            );
        }

        document.addEventListener(
            'keydown',
            function(event) {
                if (
                    event.key === 'Escape' &&
                    studentEditModal &&
                    studentEditModal.classList.contains('open')
                ) {
                    closeStudentModal();
                }
            }
        );
    </script>

</body>

</html>