<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Eğitim Yılları - Yönetim</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: #f1f5f9;
            font-family: Arial, sans-serif;
            color: #0f172a;
        }

        .header {
            background: #0f172a;
            color: white;
            padding: 22px 32px;
        }

        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }

        .title {
            margin-bottom: 20px;
        }

        .title h1 {
            margin: 0 0 6px;
        }

        .title p {
            margin: 0;
            color: #64748b;
        }

        .success {
            background: #ecfdf5;
            color: #166534;
            border: 1px solid #bbf7d0;
            padding: 14px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .error {
            background: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
            padding: 14px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .year-card {
            background: white;
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,.05);
        }

        .year-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            margin-bottom: 20px;
        }

        .year-name {
            font-size: 22px;
            font-weight: 700;
        }

        .badges {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .badge {
            padding: 6px 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
        }

        .badge-active {
            background: #dcfce7;
            color: #166534;
        }

        .badge-inactive {
            background: #e2e8f0;
            color: #475569;
        }

        .badge-open {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .badge-closed {
            background: #fee2e2;
            color: #991b1b;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 18px;
        }

        .field {
            display: flex;
            flex-direction: column;
            gap: 7px;
        }

        .field label {
            font-weight: 600;
        }

        input[type="datetime-local"] {
            width: 100%;
            padding: 11px;
            border: 1px solid #cbd5e1;
            border-radius: 9px;
        }

        .checkbox {
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
        }

        .actions {
            margin-top: 20px;
            display: flex;
            justify-content: flex-end;
        }

        button {
            border: 0;
            border-radius: 9px;
            padding: 11px 18px;
            background: #245b91;
            color: white;
            font-weight: 700;
            cursor: pointer;
        }

        @media (max-width: 700px) {
            .form-grid {
                grid-template-columns: 1fr;
            }

            .year-header {
                align-items: flex-start;
                flex-direction: column;
            }
        }
    </style>
</head>

<body>

<header class="header">
    <h1>Seçmeli Ders Sistemi</h1>
    <div>Yönetim → Eğitim Yılları</div>
</header>

<main class="container">

    <div class="title">
        <h1>Eğitim Yılları</h1>
        <p>Aktif eğitim yılını ve tercih dönemlerini yönetin.</p>
    </div>

    @if(session('success'))
        <div class="success">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="error">
            {{ $errors->first() }}
        </div>
    @endif

    @foreach($academicYears as $academicYear)

        <section class="year-card">

            <div class="year-header">

                <div class="year-name">
                    {{ $academicYear->name }}
                </div>

                <div class="badges">

                    @if($academicYear->active)
                        <span class="badge badge-active">
                            Aktif Eğitim Yılı
                        </span>
                    @else
                        <span class="badge badge-inactive">
                            Pasif
                        </span>
                    @endif

                    @if($academicYear->selectionsAreOpen())
                        <span class="badge badge-open">
                            Tercihler Açık
                        </span>
                    @else
                        <span class="badge badge-closed">
                            Tercihler Kapalı
                        </span>
                    @endif

                </div>

            </div>

            <form
                method="POST"
                action="{{ route('admin.academic-years.update', $academicYear) }}"
            >
                @csrf
                @method('PUT')

                <div class="form-grid">

                    <div class="field">

                        <label>
                            <span class="checkbox">
                                <input
                                    type="checkbox"
                                    name="active"
                                    value="1"
                                    {{ $academicYear->active ? 'checked' : '' }}
                                >

                                Aktif eğitim yılı
                            </span>
                        </label>

                    </div>

                    <div class="field">

                        <label>
                            <span class="checkbox">
                                <input
                                    type="checkbox"
                                    name="selection_enabled"
                                    value="1"
                                    {{ $academicYear->selection_enabled ? 'checked' : '' }}
                                >

                                Tercih dönemi manuel olarak açık
                            </span>
                        </label>

                    </div>

                    <div class="field">
                        <label for="start_{{ $academicYear->id }}">
                            Tercih başlangıcı
                        </label>

                        <input
                            type="datetime-local"
                            id="start_{{ $academicYear->id }}"
                            name="selection_start_at"
                            value="{{ $academicYear->selection_start_at?->format('Y-m-d\TH:i') }}"
                        >
                    </div>

                    <div class="field">
                        <label for="end_{{ $academicYear->id }}">
                            Tercih bitişi
                        </label>

                        <input
                            type="datetime-local"
                            id="end_{{ $academicYear->id }}"
                            name="selection_end_at"
                            value="{{ $academicYear->selection_end_at?->format('Y-m-d\TH:i') }}"
                        >
                    </div>

                </div>

                <div class="actions">
                    <button type="submit">
                        Kaydet
                    </button>
                </div>

            </form>

        </section>

    @endforeach

</main>

</body>
</html>