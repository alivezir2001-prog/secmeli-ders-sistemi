<!DOCTYPE html>
<html lang="tr">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>
        Öğrenci Listesi İçe Aktar
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
            max-width: 900px;
            margin: 30px auto;
            padding: 0 20px 50px;
        }

        .card {
            background: #fff;
            border: 1px solid #dbe3ec;
            border-radius: 14px;
            padding: 22px;
        }

        .back {
            display: inline-block;
            margin-bottom: 18px;
            color: #245b91;
            text-decoration: none;
            font-size: 13px;
            font-weight: 700;
        }

        h2 {
            margin: 0 0 7px;
        }

        .description {
            margin: 0 0 20px;
            color: #64748b;
            font-size: 13px;
            line-height: 1.5;
        }

        .message {
            padding: 13px;
            border-radius: 9px;
            margin-bottom: 15px;
            font-size: 13px;
        }

        .error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
        }

        .field {
            margin-bottom: 16px;
        }

        label {
            display: block;
            margin-bottom: 6px;
            color: #64748b;
            font-size: 10px;
            font-weight: 800;
        }

        select,
        input[type="file"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            background: #fff;
        }

        .hint {
            margin-top: 6px;
            color: #64748b;
            font-size: 11px;
        }

        .actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            margin-top: 22px;
        }

        .button {
            border: 0;
            border-radius: 9px;
            padding: 11px 16px;
            font-weight: 800;
            cursor: pointer;
            text-decoration: none;
        }

        .button-primary {
            background: #245b91;
            color: #fff;
        }

        .button-muted {
            background: #e2e8f0;
            color: #334155;
        }
    </style>

</head>

<body>

    <header class="header">

        <h1>
            Öğrenci Listesi İçe Aktar
        </h1>

        <p>
            {{ $academicYear->name }} Eğitim ve Öğretim Yılı
        </p>

    </header>

    <main class="container">

        <a
            href="{{ route('admin.students.index', [
            'academic_year_id' => $academicYear->id
        ]) }}"
            class="back">
            ← Öğrenci Yönetimine Dön
        </a>

        @if($errors->any())

        <div class="message error">

            @foreach($errors->all() as $error)

            <div>
                {{ $error }}
            </div>

            @endforeach

        </div>

        @endif

        <section class="card">

            <h2>
                e-Okul PDF Öğrenci Listesi
            </h2>

            <p class="description">
                e-Okul'dan alınan sınıf listesi PDF'sini yükleyin.
                Sistem sınıf ve şube bilgilerini PDF başlıklarından
                okuyarak öğrenci listesini hazırlayacaktır.
            </p>

            <form
                method="POST"
                action="{{ route(
                'admin.students.import.preview'
            ) }}"
                enctype="multipart/form-data">

                @csrf

                <div class="field">

                    <label>
                        EĞİTİM YILI
                    </label>

                    <select
                        name="academic_year_id"
                        required>

                        @foreach($academicYears as $year)

                        <option
                            value="{{ $year->id }}"
                            {{ (int) $year->id === (int) $academicYear->id ? 'selected' : '' }}>
                            {{ $year->name }}
                            {{ $year->active ? ' (Aktif)' : '' }}
                        </option>

                        @endforeach

                    </select>

                </div>

                <div class="field">

                    <label>
                        ÖĞRENCİ LİSTESİ PDF
                    </label>

                    <input
                        type="file"
                        name="pdf"
                        accept=".pdf,application/pdf"
                        required>

                    <div class="hint">
                        PDF dosyası en fazla 20 MB olabilir.
                    </div>

                </div>

                <div class="actions">

                    <a
                        href="{{ route(
                        'admin.students.index',
                        [
                            'academic_year_id' =>
                                $academicYear->id
                        ]
                    ) }}"
                        class="button button-muted">
                        Vazgeç
                    </a>

                    <button
                        type="submit"
                        class="button button-primary">
                        PDF'yi Analiz Et
                    </button>

                </div>

            </form>

        </section>

    </main>

</body>

</html>