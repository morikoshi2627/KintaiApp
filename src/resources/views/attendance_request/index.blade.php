@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance_request/index.css') }}">
@endsection

@section('header')
<!-- 各種ボタン -->

<div class="header-right">
    <!-- 出勤前・出勤中・休憩中 -->
    <a class="attendance-button" href="{{ route('attendance.index') }}">勤怠</a>
    <a class="listing-button" href="{{ route('attendance.list') }}">勤怠一覧</a>
    <span class="application-button disabled">申請</span>
    <form class="form-actions" method="POST" action="{{ url('/logout') }}">
        @csrf
        <button class="logout-button" type="submit">ログアウト</button>
    </form>
</div>
@endsection

@section('content')
<!-- メイン -->

<main class="main-content">

    <div class="main-inner">

        <h2 class="main-title">| 申請一覧</h2>

        <div class="filter-tabs">
            <a href="{{ route('attendance_request.list', ['status' => 'pending']) }}"
                class="filter-tabs-a {{ $status === 'pending' ? 'active' : '' }}">
                承認待ち
            </a>

            <a href="{{ route('attendance_request.list', ['status' => 'approved']) }}"
                class="filter-tabs-a {{ $status === 'approved' ? 'active' : '' }}">
                承認済み
            </a>

        </div>
        <hr class="filter-tabs-hr">

        <div class="application-update">
            <table class="application-update-table">
                <thead>
                    <tr class="application-update-tr">
                        <th class="application-update-th">状態</th>
                        <th class="application-update-th">名前</th>
                        <th class="application-update-th">対象日時</th>
                        <th class="application-update-th">申請理由</th>
                        <th class="application-update-th">申請日時</th>
                        <th class="application-update-th">詳細</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $request)
                    <tr class="application-update-tbody">
                        <!--  状態  -->
                        <td class="application-update-td">
                            @if($request->status === 'pending')
                            承認待ち
                            @elseif($request->status === 'approved')
                            承認済み
                            @else
                            {{ $request->status }}
                            @endif
                        </td>

                        <!-- ユーザー名 -->
                        <td class="application-update-td">{{ $request->user->name }}</td>

                        <!-- 対象日 -->
                        <td class="application-update-td">
                            {{ optional($request->attendance)->attendance_date 
                            ? \Carbon\Carbon::parse($request->attendance->attendance_date)->format('Y/m/d')
                            : '-' }}
                        </td>

                        <!-- 申請理由 -->
                        <td class="application-update-td">{{ $request->request_reason }}</td>

                        <!-- 申請日時 -->
                        <td class="application-update-td">{{ $request->created_at->format('Y/m/d') }}</td>

                        <!-- 詳細ボタン -->
                        <td class="application-update-td">
                            <a class="detail-button" href="{{ route('attendance.detail', $request->attendance_id) }}">詳細</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td class="message-colspan" colspan="6">データがありません。</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @endsection