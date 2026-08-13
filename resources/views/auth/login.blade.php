<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Öğrenci Girişi - Seçmeli Ders Sistemi</title>

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
            background: #f3f6fa;
        }

        .login-box {
            width: 100%;
            max-width: 420px;
            background: white;
            padding: 35px;
            border-radius: 16px;
            box-shadow: 0 10px 35px rgba(0, 0, 0, .10);
        }

        h1 {
            margin-top: 0;
            text-align: center;
            color: #1e3a5f;
        }

        .subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 30px;
        }

        label {
            display: block;
            margin-bottom: 7px;
            font-weight: 600;
        }

        input {
            width: 100%;
            padding: 12px;
            margin-bottom: 18px;
            border: 1px solid #d0d7de;
            border-radius: 8px;
            font-size: 16px;
        }

        button {
            width: 100%;
            padding: 13px;
            border: 0;
            border-radius: 8px;
            background: #1e3a5f;
            color: white;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
        }

        .error {
            color: #b42318;
            margin-bottom: 15px;
            font-size: 14px;
        }
    </style>
</head>

<body>

<div class="login-box">

    <h1>Seçmeli Ders Sistemi</h1>

    <div class="subtitle">
        Öğrenci Girişi
    </div>

    @if ($errors->any())
        <div class="error">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('login.post') }}">
        @csrf

        <label for="student_number">Öğrenci Numarası</label>

        <input
            type="text"
            id="student_number"
            name="student_number"
            value="{{ old('student_number') }}"
            required
            autofocus
            autocomplete="username"
        >

        <label for="password">Şifre</label>

        <input
            type="password"
            id="password"
            name="password"
            required
            autocomplete="current-password"
        >

        <button type="submit">
            Giriş Yap
        </button>
    </form>

</div>

</body>
</html>