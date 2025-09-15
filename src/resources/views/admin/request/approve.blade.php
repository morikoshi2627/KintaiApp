<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KintaiApp</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/admin/request/approve.css') }}">

</head>

<body>
    <header class="header">
        <div class="header-inner">
            <img src="{{ asset('storage/CoachTech_White 1.png') }}" alt="coachtechロゴ">
        </div>


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
    </header>


    <main class="main-content">
        <div class="main-inner">
            <h2 class="main-title">| 勤怠詳細 </h2>

            <div class="application-update">

                <!-- 名前 -->
                <div class="application-column">
                    <div class="application-field">
                        <label class="application-label">名前</label>
                    </div>
                    <div class="input-area">
                        <div class="input-wrapper">
                            <p class="application-p">{{ $attendanceRequest->user->name }}</p>
                        </div>
                    </div>
                </div>

                <!-- 日付 -->
                <div class="application-column">
                    <div class="application-field">
                        <label class="application-label">日付</label>
                    </div>
                    <div class="input-area">
                        <div class="input-wrapper">
                            <p class="application-p">
                                <span class="application-p-span">
                                    {{ \Carbon\Carbon::parse($attendanceRequest->attendance->attendance_date)->format('Y年') }}
                                </span>
                                <span class="date-space">
                                    {{ \Carbon\Carbon::parse($attendanceRequest->attendance->attendance_date)->format('n月j日') }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- 出勤・退勤 -->
                <div class="application-column">
                    <div class="application-field">
                        <label class="application-label">出勤・退勤</label>
                    </div>
                    <div class="input-area">
                        <div class="input-wrapper">
                            <p class="application-input">
                                {{ $attendanceRequest->requested_start_time
                    ? \Carbon\Carbon::parse($attendanceRequest->requested_start_time)->format('H:i')
                    : ($attendanceRequest->attendance->start_time
                        ? \Carbon\Carbon::parse($attendanceRequest->attendance->start_time)->format('H:i')
                        : '') }}
                            </p>
                            <span class="application-span">〜</span>
                            <p class="application-input">
                                {{ $attendanceRequest->requested_end_time
                    ? \Carbon\Carbon::parse($attendanceRequest->requested_end_time)->format('H:i')
                    : ($attendanceRequest->attendance->end_time
                        ? \Carbon\Carbon::parse($attendanceRequest->attendance->end_time)->format('H:i')
                        : '') }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- 休憩 -->
                @php
                $breaks = $attendanceRequest->requestBreakTimes ?? collect();
                $maxBreaks = max($breaks->count() + 1, 1); // 休憩N + 1 の空欄も表示
                @endphp

                @for ($i = 0; $i < $maxBreaks; $i++)
                    <div class="application-column">
                    <div class="application-field">
                        <label class="application-label">休憩{{ $i + 1 }}</label>
                    </div>
                    <div class="input-area">
                        <div class="input-wrapper">
                            <p class="application-p">
                                @php
                                $start = isset($breaks[$i]) && $breaks[$i]->break_started_at
                                ? \Carbon\Carbon::parse($breaks[$i]->break_started_at)->format('H:i')
                                : '';
                                $end = isset($breaks[$i]) && $breaks[$i]->break_ended_at
                                ? \Carbon\Carbon::parse($breaks[$i]->break_ended_at)->format('H:i')
                                : '';
                                @endphp

                                {{ $start }}
                                @if($start && $end)
                                <span class="application-span">〜</span>
                                @endif
                                {{ $end }}
                            </p>
                        </div>
                    </div>
            </div>
            @endfor

            <!-- 備考 -->
            <div class="application-column">
                <div class="application-field">
                    <label class="application-label">備考</label>
                </div>
                <div class="input-area">
                    <div class="input-wrapper">
                        <p class="application-p">{{ $attendanceRequest->request_reason ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <!-- 承認ボタン -->
            <div class="button-area">
                @if($attendanceRequest->status === 'pending')
                <form method="POST" action="{{ route('admin.request.update', $attendanceRequest->id) }}">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="status" value="approved">
                    <button type="submit" class="correction-button">承認</button>
                </form>
                @else
                <p class="correction-button-p">承認済み</p>
                @endif
            </div>

        </div>
        </div>
    </main>
</body>

</html>