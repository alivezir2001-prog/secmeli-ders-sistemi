<!DOCTYPE html>
<html lang="tr">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>
        Yönetim Paneli - Seçmeli Ders Sistemi
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
            font-size: 13px;
        }

        .container {
            max-width: 1250px;
            margin: 30px auto;
            padding: 0 20px 50px;
        }

        .welcome {
            background: white;
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 24px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, .05);
        }

        .welcome h2 {
            margin: 0 0 8px;
        }

        .welcome p {
            margin: 0;
            color: #64748b;
            font-size: 14px;
        }

        .section-title {
            margin: 28px 0 12px;
            font-size: 15px;
            font-weight: 800;
            color: #334155;
        }

        .cards {
            display: grid;
            grid-template-columns:
                repeat(auto-fit, minmax(230px, 1fr));
            gap: 18px;
        }

        .card {
            display: block;
            background: white;
            border-radius: 16px;
            padding: 24px;
            text-decoration: none;
            color: inherit;
            box-shadow: 0 4px 15px rgba(0, 0, 0, .05);
            transition:
                transform .2s,
                box-shadow .2s;
            border: 1px solid transparent;
        }

        .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, .09);
        }

        .card-disabled {
            cursor: default;
            opacity: .65;
        }

        .card-disabled:hover {
            transform: none;
            box-shadow: 0 4px 15px rgba(0, 0, 0, .05);
        }

        .card-group {
            border-color: #cfe0f5;
            background: #f8fbff;
        }

        .card-placement {
            border-color: #cfdff2;
            background: #f7fbff;
        }

        .card-manual {
            border-color: #dfd2f5;
            background: #fbf8ff;
        }

        .icon {
            font-size: 32px;
            margin-bottom: 12px;
        }

        .card h3 {
            margin: 0 0 8px;
            font-size: 17px;
        }

        .card p {
            margin: 0;
            color: #64748b;
            font-size: 13px;
            line-height: 1.5;
        }

        .badge {
            display: inline-block;
            margin-top: 12px;
            padding: 4px 8px;
            border-radius: 999px;
            background: #e2e8f0;
            color: #64748b;
            font-size: 10px;
            font-weight: 800;
        }

        .logout {
            display: inline-block;
            margin-top: 28px;
            color: #dc2626;
            text-decoration: none;
            border: 0;
            background: none;
            padding: 0;
            cursor: pointer;
            font-size: 14px;
        }

        @media (max-width: 650px) {

            .container {
                padding-left: 12px;
                padding-right: 12px;
            }

            .header {
                padding: 20px;
            }

            .card {
                padding: 20px;
            }

        }
    </style>

</head>

<body>

    <header class="header">

        <h1>
            Seçmeli Ders Sistemi
        </h1>

        <p>
            Yönetim Paneli
        </p>

    </header>

    <main class="container">

        <section class="welcome">

            <h2>
                Hoş geldiniz,
                {{ auth()->user()->name }}
            </h2>

            <p>
                Sistem yönetimi ve seçmeli ders tercih işlemlerini
                buradan yönetebilirsiniz.
            </p>

        </section>


        <div class="section-title">
            TEMEL YÖNETİM
        </div>

        <section class="cards">

            <a
                href="{{ route('admin.academic-years.index') }}"
                class="card">

                <div class="icon">
                    📅
                </div>

                <h3>
                    Eğitim Yılları
                </h3>

                <p>
                    Eğitim yıllarını ve tercih dönemlerini yönetin.
                </p>

            </a>


            <a
                href="{{ route('admin.courses.index') }}"
                class="card">
                <div class="icon">
                    📚
                </div>

                <h3>
                    Dersler
                </h3>

                <p>
                    Seçmeli dersleri, programları ve modülleri yönetin.
                </p>
            </a>


            <a
                href="{{ route('admin.students.index') }}"
                class="card">
                <div class="icon">
                    👨‍🎓
                </div>

                <h3>
                    Öğrenciler
                </h3>

                <p>
                    Öğrenci kayıtlarını ve sınıf bilgilerini yönetin.
                </p>
            </a>

            <a
                href="{{ route('admin.course-selections.index') }}"
                class="card">
                <div class="icon">
                    📋
                </div>

                <h3>
                    Öğrenci Tercihleri
                </h3>

                <p>
                    Öğrencilerin yaptığı tercihleri görüntüleyin ve raporları inceleyin.
                </p>
            </a>


            <a
                href="{{ route('admin.course-offerings.index') }}"
                class="card">

                <div class="icon">
                    📊
                </div>

                <h3>
                    Ders Kontenjanları
                </h3>

                <p>
                    Derslerin öğrenci sayılarını ve okul kapasitesini yönetin.
                </p>

            </a>

        </section>


        <div class="section-title">
            YERLEŞTİRME YÖNETİMİ
        </div>

        <section class="cards">

            <a
                href="{{ route(
                'admin.student-course-groups.index'
            ) }}"
                class="card card-group">

                <div class="icon">
                    👥
                </div>

                <h3>
                    Grup Yönetimi
                </h3>

                <p>
                    Otomatik öğrenci grupları oluşturun,
                    grupları yönetin ve öğrencileri kontrol edin.
                </p>

            </a>


            <a
                href="{{ route(
                'admin.student-placements.index'
            ) }}"
                class="card card-placement">

                <div class="icon">
                    ✅
                </div>

                <h3>
                    Yerleştirme Kontrol Merkezi
                </h3>

                <p>
                    Öğrenci-kategori yerleştirmelerini toplu olarak
                    kontrol edin ve kesinleştirin.
                </p>

            </a>


            <a
                href="{{ route(
                'admin.student-course-groups.manual-placement'
            ) }}"
                class="card card-manual">

                <div class="icon">
                    🔧
                </div>

                <h3>
                    Manuel Yerleştirme
                </h3>

                <p>
                    Eksik veya özel durumdaki öğrenci-kategori
                    yerleştirmelerine müdahale edin.
                </p>

            </a>

        </section>


        <form
            method="POST"
            action="{{ route('logout') }}">

            @csrf

            <button
                type="submit"
                class="logout">
                Çıkış Yap
            </button>

        </form>

    </main>

</body>

</html>