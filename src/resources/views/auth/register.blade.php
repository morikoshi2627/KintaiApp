@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth/register.css') }}" />
@endsection

@section('content')
<main class="main-content">

    <div class="main-inner">

        <!-- タイトル -->
        <h2 class="main-title">会員登録</h2>

        <!-- 登録用フォーム -->
        <form class="register-form" method="POST" action="{{ route('user.register') }}" novalidate>
            @csrf
            <div class="input-area">
                <div class="form-group">
                    <label class="form-label" for="name">名前</label>
                    <input class="form-input" type="email" name="name" value="{{ old('name') }}" required>
                    @error('name')
                    <div class="form-error">
                        {{ $message }}
                    </div>
                    @enderror
                </div>

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

                <div class="form-group">
                    <label class="form-label" for="password_confirmation">パスワード確認</label>
                    <input class="form-input" type="password" name="password_confirmation" required>
                    @error('password_confirmation')
                    <div class="form-error">
                        {{ $message }}
                    </div>
                    @enderror
                </div>

                <div class="form-actions">
                    <button class="register-button" type="submit">登録する</button>
                </div>
            </div>
        </form>

        <!-- アカウント作成リンク -->
        <div class="login-link">
            <a class="login-button" href="{{ route('login') }}">ログインはこちら</a>
        </div>
        @endsection