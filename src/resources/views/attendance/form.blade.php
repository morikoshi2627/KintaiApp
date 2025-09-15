<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KintaiApp</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/attendance/form.css') }}">

</head>

<body>
    <header class="header">
        <div class="header-inner">
            <img class="header-left .logo" src="{{ asset('storage/CoachTech_White 1.png') }}" alt="coachtechロゴ">
        </div>


        <!-- 各種ボタン -->

        <div class="header-right">
            @if($status !== '退勤済')

            <!-- 出勤前・出勤中・休憩中 -->
            <a class="attendance-button" href="{{ route('attendance.index') }}">勤怠</a>
            <a class="listing-button" href="{{ route('attendance.list') }}">勤怠一覧</a>

            <a class="application-button" href="{{ route('attendance_request.list') }}">申請</a>

            <form class="form-actions" method="POST" action="{{ url('/logout') }}">
                @csrf
                <button class="logout-button" type="submit">ログアウト</button>
            </form>
            @else
            <!-- 退勤後 -->
            <a class="listing-button" href="{{ route('attendance.list') }}">今月の出勤一覧</a>
            <a class="application-button" href="{{ route('attendance_request.list') }}">申請一覧</a>
            <form class="form-actions" method="POST" action="{{ url('/logout') }}">
                @csrf
                <button class="logout-button" type="submit">ログアウト</button>
            </form>
            @endif
        </div>
    </header>



    <main class="main-content">

        <div class="main-inner">

            <div class="attendance-status">
                <h2 class="situation">{{ $status }}</h2>
                <p class="date">{{ \Carbon\Carbon::now()->isoFormat('YYYY年M月D日(ddd)') }}</p>
                <p class="time">{{ \Carbon\Carbon::now()->format('H:i') }}</p>
            </div>

            <div class="attendance-actions">
                @if ($status === '勤務外')
                <form class="work-situation" method="POST" action="{{ route('attendance.store') }}">
                    @csrf
                    <button class="situation-button" type="submit" name="clock_in">出勤</button>
                </form>
                @elseif ($status === '出勤中')
                <div class="button-container">
                    <form class="work-situation" method="POST" action="{{ route('attendance.store') }}">
                        @csrf
                        <button class="situation-button" type="submit" name="clock_out">退勤</button>
                    </form>
                    <form class="work-situation" method="POST" action="{{ route('attendance.store') }}">
                        @csrf
                        <button class="breaktime-button" type="submit" name="break_in">休憩入</button>
                    </form>
                </div>
                @elseif ($status === '休憩中')
                <form class="work-situation" method="POST" action="{{ route('attendance.store') }}">
                    @csrf
                    <button class="breaktime-button" type="submit" name="break_out">休憩戻</button>
                </form>
                @elseif ($status === '退勤済')
                <p class="message">お疲れ様でした。</p>
                @endif
            </div>

        </div>
    </main>
</body>

</html>