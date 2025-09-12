<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KintaiApp</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/admin/attendances/show.css') }}">

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
                <form id="update-form" method="POST" action="{{ route('admin.attendances.update', ['id' => $attendance->id]) }}" novalidate>
                    @csrf
                    @method('PUT')

                    <!-- 名前 -->
                    <div class="application-column">
                        <div class="application-field">
                            <label class="application-label">名前</label>
                        </div>
                        <div class="input-area">
                            <div class="input-wrapper">
                                <p class="application-p">{{ $attendance->user->name }}</p>
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
                                    <span class="application-p-span">{{ \Carbon\Carbon::parse($attendance->attendance_date)->format('Y年') }}</span>
                                    <span class="date-space">{{ \Carbon\Carbon::parse($attendance->attendance_date)->format('n月j日') }}</span>
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
                                <input class="application-input" type="time" name="start_time"
                                    value="{{ old('start_time', $attendance->start_time ? \Carbon\Carbon::parse($attendance->start_time)->format('H:i') : '') }}">
                                <span class="application-span">〜</span>
                                <input class="application-input" type="time" name="end_time"
                                    value="{{ old('end_time', $attendance->end_time ? \Carbon\Carbon::parse($attendance->end_time)->format('H:i') : '') }}">
                            </div>
                            <div class="error-wrapper">
                                @error('start_time')<div class="form-error">{{ $message }}</div>@enderror
                                @error('end_time')<div class="form-error">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>

                    <!-- 休憩 -->
                    @foreach ($attendance->breakTimes as $index => $break)
                    <div class="application-column">
                        <div class="application-field">
                            <label class="application-label">休憩{{ $index + 1 }}</label>
                        </div>
                        <div class="input-area">
                            <div class="input-wrapper">
                                <input type="hidden" name="break_id[{{ $index }}]" value="{{ $break->id }}">
                                <input class="application-input" type="time" name="break_start[{{ $index }}]"
                                    value="{{ old('break_start.'.$index, $break->start_time?->format('H:i')) }}">
                                <span class="application-span">〜</span>
                                <input class="application-input" type="time" name="break_end[{{ $index }}]"
                                    value="{{ old('break_end.'.$index, $break->end_time?->format('H:i')) }}">
                            </div>
                            <div class="error-wrapper">
                                @error('break_start.'.$index)<div class="form-error">{{ $message }}</div>@enderror
                                @error('break_end.'.$index)<div class="form-error">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                    @endforeach

                    <!-- 新規休憩追加 -->
                    <div class="application-column">
                        <div class="application-field">
                            <label class="application-label">休憩{{ count($attendance->breakTimes) + 1 }}</label>
                        </div>
                        <div class="input-area">
                            <div class="input-wrapper">
                                <input class="application-input" type="time"
                                    name="break_start[new]"
                                    value="{{ old('break_start.new', $displayData->newBreak->start ?? '') }}">
                                <span class="application-span">〜</span>
                                <input class="application-input" type="time"
                                    name="break_end[new]"
                                    value="{{ old('break_end.new', $displayData->newBreak->end ?? '') }}">
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
                    <div class="application-column">
                        <div class="application-field">
                            <label class="application-label">備考</label>
                        </div>
                        <div class="input-area">
                            <div class="input-wrapper">
                                <textarea class="application-textarea" name="request_reason">{{ old('request_reason', $attendance->request_reason) }}</textarea>
                            </div>
                            @error('request_reason')<div class="form-error">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <!-- 修正ボタン -->
                    <div class="button-area">
                        <button class="correction-button" type="submit" form="update-form">修正</button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</body>

</html>