<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KintaiApp</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/auth/login.css') }}" />

</head>

<body>
    <header class="header">
        <div class="header-inner">
            <img src="{{ asset('storage/CoachTech_White 1.png') }}" alt="coachtechロゴ">
        </div>
    </header>

    <main class="main-content">

        <div class="main-inner">

            <!-- タイトル -->
            <h2 class="main-title">ログイン</h2>

            <form class="login-form" method="POST" action="{{ route('login') }}" novalidate>
                @csrf

                <div class="input-area">
                    <div class="form-group">
                        <label class="form-label" for="email">メールアドレス</label>
                        <input class="form-input" type="email" name="email" value="{{ old('email') }}" required>
                        @error('email')
                        <div class="form-error">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="password">パスワード</label>
                        <input class="form-input" type="password" name="password" required>
                        @error('password')
                        <div class="form-error">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    <div class="form-actions">
                        <button class="login-button" type="submit">ログインする
                        </button>
                    </div>
                </div>
            </form>

            <!-- アカウント作成リンク -->
            <div class="login-link">
                <a class="register-button" href="{{ route('user.register') }}">会員登録はこちら</a>
            </div>
        </div>
    </main>
</body>

</html>