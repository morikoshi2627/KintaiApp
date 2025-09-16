@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/user/index.css') }}">
@endsection

@section('header')
<!-- 各種ボタン -->
<div class="header-right">

    <a class="attendance-button" href="{{ route('admin.attendances') }}">勤怠一覧</a>
    <a class="listing-button" href="{{ route('admin.users') }}">スタッフ一覧</a>

    <a class="application-button" href="{{ route('admin.requests') }}">申請一覧</a>

    <form class="form-actions" method="POST" action="{{ url('/admin/logout') }}">
        @csrf
        <button class="logout-button" type="submit">ログアウト</button>
    </form>
</div>
@endsection

@section('content')
<main class="main-content">
    <div class="main-inner">
        <h2 class="main-title">| スタッフ一覧 </h2>

        <table class="attendance-table">
            <thead class="attendance-table-title">
                <tr class="attendance-column">
                    <th class="attendance-column-th">名前</th>
                    <th class="attendance-column-th">メールアドレス</th>
                    <th class="attendance-column-th">詳細</th>
                </tr>
            </thead>

            <tbody class="attendance-whole">
                @foreach ($users as $user)
                <tr class="attendance-list">
                    <td class="name-list">{{ $user->name }}</td>
                    <td class="meil-list">{{ $user->email }}</td>
                    <td class="show-list">
                        <a class="show-button" href="{{ route('admin.user.attendances', ['id' => $user->id]) }}">詳 細</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endsection