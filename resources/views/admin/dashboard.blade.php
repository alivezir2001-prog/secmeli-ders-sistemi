<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Yönetim Paneli - Seçmeli Ders Sistemi</title>

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
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }

        .welcome {
            background: white;
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 24px;
            box-shadow: 0 4px 15px rgba(0,0,0,.05);
        }

        .welcome h2 {
            margin-top: 0;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 18px;
        }

        .card {
            background: white;
            border-radius: 16px;
            padding: 24px;
            text-decoration: none;
            color: inherit;
            box-shadow: 0 4px 15px rgba(0,0,0,.05);
            transition: .2s;
        }

        .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 24px rgba(0,0,0,.09);
        }

        .icon {
            font-size: 32px;
            margin-bottom: 12px;
        }

        .card h3 {
            margin: 0 0 8px;
        }

        .card p {
            margin: 0;
            color: #64748b;
            font-size: 14px;
        }

        .logout {
            display: inline-block;
            margin-top: 25px;
            color: #dc2626;
            text-decoration: none;
        }
    </style>
</head>

<body>

<header class="header">
    <h1>Seçmeli Ders Sistemi</h1>
    <p>Yönetim Paneli</p>
</header>

<main class="container">

    <section class="welcome">
        <h2>Hoş geldiniz, {{ auth()->user()->name }}</h2>

        <p>
            Sistem yönetimi ve seçmeli ders tercih işlemlerini
            buradan yönetebilirsiniz.
        </p>
    </section>

    <section class="cards">

        <a href="{{ route('admin.academic-years.index') }}" class="card">
            <div class="icon">📅</div>
            <h3>Eğitim Yılları</h3>
            <p>
                Eğitim yıllarını ve tercih dönemlerini yönetin.
            </p>
        </a>

        <a href="#" class="card">
            <div class="icon">📚</div>
            <h3>Dersler</h3>
            <p>
                Seçmeli dersleri ve ders gruplarını yönetin.
            </p>
        </a>

        <a href="#" class="card">
            <div class="icon">👨‍🎓</div>
            <h3>Öğrenciler</h3>
            <p>
                Öğrenci kayıtlarını ve sınıf bilgilerini yönetin.
            </p>
        </a>

        <a href="#" class="card">
            <div class="icon">📋</div>
            <h3>Tercihler</h3>
            <p>
                Öğrencilerin yaptığı tercihleri görüntüleyin.
            </p>
        </a>

        <a href="{{ route('admin.course-offerings.index') }}" class="card"
        >
            <div class="icon">📊</div>
            <h3>Ders Kontenjanları</h3>
            <p>
                Derslerin öğrenci sayılarını ve okul kapasitesini yönetin.
            </p>
        </a>

    </section>

    <form method="POST" action="{{ route('logout') }}">
        @csrf

        <button
            type="submit"
            class="logout"
            style="
                border:0;
                background:none;
                padding:0;
                cursor:pointer;
                font-size:14px;
            "
        >
            Çıkış Yap
        </button>
    </form>

</main>

</body>
</html>