<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yönetici Girişi</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: Arial, sans-serif;
            background: #0f172a;
        }

        .box {
            width: 100%;
            max-width: 420px;
            background: white;
            border-radius: 18px;
            padding: 32px;
            box-shadow: 0 15px 40px rgba(0,0,0,.25);
        }

        h1 {
            margin: 0 0 8px;
            color: #0f172a;
        }

        p {
            color: #64748b;
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin: 15px 0 7px;
            font-weight: 600;
        }

        input {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
        }

        button {
            width: 100%;
            margin-top: 22px;
            padding: 13px;
            border: 0;
            border-radius: 10px;
            background: #245b91;
            color: white;
            font-weight: 700;
            cursor: pointer;
        }

        .error {
            margin-top: 15px;
            padding: 12px;
            background: #fef2f2;
            color: #991b1b;
            border-radius: 10px;
        }
    </style>
</head>

<body>

<div class="box">

    <h1>Yönetici Girişi</h1>
    <p>Seçmeli Ders Sistemi Yönetim Paneli</p>

    @if ($errors->any())
        <div class="error">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('admin.login.post') }}">
        @csrf

        <label for="email">E-posta</label>

        <input
            type="email"
            id="email"
            name="email"
            value="{{ old('email') }}"
            required
            autofocus
        >

        <label for="password">Şifre</label>

        <input
            type="password"
            id="password"
            name="password"
            required
        >

        <button type="submit">
            Yönetici Girişi
        </button>
    </form>

</div>

</body>
</html>