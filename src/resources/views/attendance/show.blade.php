<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KintaiApp</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/attendance/show.css') }}">

</head>

<body>
    <header class="header">
        <div class="header-inner">
            <img src="{{ asset('storage/CoachTech_White 1.png') }}" alt="coachtechロゴ">
        </div>

        <!-- 各種ボタン -->

        <div class="header-right">
            <!-- 出勤前・出勤中・休憩中 -->
            <a class="attendance-button" href="{{ route('attendance.index') }}">勤怠</a>
            <a class="listing-button" href="{{ route('attendance.list') }}">勤怠一覧</a>

            @if($attendanceId)
            <a class="application-button" href="{{ route('attendance_request.list') }}">申請</a>
            @else
            <span class="application-button disabled">申請</span>
            @endif

            <form class="form-actions" method="POST" action="{{ url('/logout') }}">
                @csrf
                <button class="logout-button" type="submit">ログアウト</button>
            </form>
        </div>
    </header>


    <!-- メイン -->

    <main class="main-content">

        <div class="main-inner">

            <h2 class="main-title">| 勤怠詳細</h2>

            @php
            $pendingRequest = $attendance->attendanceRequests()
            ->where('status', 'pending')
            ->latest()
            ->first();
            @endphp

            <div class="application-update">
                <form id="update-form" method="POST" action="{{ route('attendance.update', ['id' => $attendance->id ?? 0]) }}" novalidate>
                    @csrf
                    @method('PUT')

                    <div class="application-column">
                        <div class="application-field">
                            <label class="application-label">名前</label>
                        </div>
                        <div class="input-area">
                            <div class="input-wrapper">
                                <p class="application-p">{{ auth()->user()->name }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="application-column">
                        <div class="application-field">
                            <label class="application-label">日付</label>
                        </div>
                        <div class="input-area">
                            <div class="input-wrapper">
                                <p class="application-p">
                                    <span class="application-p-span">{{ \Carbon\Carbon::parse($attendance->attendance_date)->format('Y年') }}</span>
                                    <span class="date-space">{{ \Carbon\Carbon::parse($attendance->attendance_date)->format('n月j日') }}</span>
                                </p>
                            </div>
                        </div>
                    </div>

                    @php
                    // 修正申請がある場合はそちらを優先、それ以外は元の勤怠データ
                    $startTime = $pendingRequest?->requested_start_time ?? $attendance->start_time;
                    $endTime = $pendingRequest?->requested_end_time ?? $attendance->end_time;

                    // Carbonオブジェクトに変換してフォーマット
                    $startTimeValue = old('start_time', $startTime ? \Carbon\Carbon::parse($startTime)->format('H:i') : '');
                    $endTimeValue = old('end_time', $endTime ? \Carbon\Carbon::parse($endTime)->format('H:i') : '');
                    @endphp

                    <!-- 出勤・退勤 -->
                    <div class="application-column">
                        <div class="application-field">
                            <label class="application-label">出勤・退勤</label>
                        </div>
                        <div class="input-area">
                            <div class="input-wrapper">
                                <input class="application-input" type="time" name="start_time" value="{{ old('start_time', $displayData->start_time ? \Carbon\Carbon::parse($displayData->start_time)->format('H:i') : '') }}">

                                <span class="application-span">〜</span>

                                <input class="application-input" type="time" name="end_time" value="{{ old('end_time', $displayData->end_time ? \Carbon\Carbon::parse($displayData->end_time)->format('H:i') : '') }}">
                            </div>
                            <div class="error-wrapper">
                                @error('start_time')
                                <div class="form-error">{{ $message }}</div>
                                @enderror

                                @error('end_time')
                                <div class="form-error">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- 休憩 -->
                    @foreach ($displayData->breakTimes as $index => $break)
                    <div class="application-column">
                        <div class="application-field">
                            <label class="application-label">休憩{{ $index + 1 }}</label>
                        </div>
                        <div class="input-area">
                            <div class="input-wrapper">
                                <input type="hidden" name="break_id[{{ $index }}]" value="{{ $break->id ?? '' }}">
                                <input class="application-input" type="time"
                                    name="break_start[{{ $index }}]"
                                    value="{{ old('break_start.'.$index, $break->break_started_at?->format('H:i') ?? $break->start_time?->format('H:i') ?? '') }}">
                                <span class="application-span">〜</span>
                                <input class="application-input" type="time"
                                    name="break_end[{{ $index }}]"
                                    value="{{ old('break_end.'.$index, $break->break_ended_at?->format('H:i') ?? $break->end_time?->format('H:i') ?? '') }}">
                            </div>
                            <div class="error-wrapper">
                                @error('break_start.'.$index)
                                <div class="form-error">{{ $message }}</div>
                                @enderror
                                @error('break_end.'.$index)
                                <div class="form-error">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    @endforeach

                    <!--  新規休憩追加用フィールド -->
                    <div class="application-column">
                        <div class="application-field">
                            <label class="application-label">休憩{{ count($displayData->breakTimes) + 1 }}</label>
                        </div>
                        <div class="input-area">
                            <div class="input-wrapper">
                                <input class="application-input" type="time" name="break_start[new]" value="{{ $displayData->newBreak->start }}">
                                <span class="application-span">〜</span>
                                <input class="application-input" type="time" name="break_end[new]" value="{{ $displayData->newBreak->end }}">
                            </div>
                            <div class="error-wrapper">
                                <div class="error-wrapper">
                                    @error('break_start.new')
                                    <div class="form-error">{{ $message }}</div>
                                    @enderror
                                    @error('break_end.new')
                                    <div class="form-error">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 備考 -->
                    @php
                    // 承認前の修正申請がある場合はそちらを優先
                    $requestReasonValue = old('request_reason', $pendingRequest?->request_reason ?? $attendance->request_reason);
                    @endphp

                    <div class="application-column">
                        <div class="application-field">
                            <label class="application-label">備考</label>
                        </div>
                        <div class="input-area">
                            <div class="input-wrapper">
                                <textarea class="application-textarea" name="request_reason">{{ old('request_reason', $displayData->request_reason) }}</textarea>
                            </div>
                            @error('request_reason')<div class="form-error">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <!-- 修正ボタン or メッセージ -->
                    <div class="button-area">
                        @if($pendingRequest)
                        <span class="disabled-message">＊承認待ちのため修正はできません。</span>
                        @else
                        <button class="correction-button" type="submit" form="update-form">修正</button>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </main>
</body>

</html>