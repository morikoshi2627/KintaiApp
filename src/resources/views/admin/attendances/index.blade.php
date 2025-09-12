<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KintaiApp</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/admin/attendances/index.css') }}">

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
            <h2 class="main-title">| {{ $date->translatedFormat('Y年m月d日') }} の勤怠</h2>
            <div class="month-selector">
                <a class="attendance-date" href="{{ route('admin.attendances', ['date' => \Carbon\Carbon::parse($date)->subDay()->toDateString()]) }}">←前日</a>

                <div class="calendar-select-wrapper">
                    <img class="calendar-img" src="{{ asset('storage/calendar.png') }}" alt="カレンダー">

                    <span class="select-month">{{ $date->translatedFormat('Y/m/d') }}</span>
                </div>

                <a class="attendance-date" href="{{ route('admin.attendances', ['date' => \Carbon\Carbon::parse($date)->addDay()->toDateString()]) }}">翌日→</a>
            </div>

            <table class="attendance-table">
                <thead class="attendance-table-title">
                    <tr class="attendance-column">
                        <th class="attendance-column-th">名前</th>
                        <th class="attendance-column-th">出勤</th>
                        <th class="attendance-column-th">退勤</th>
                        <th class="attendance-column-th">休憩</th>
                        <th class="attendance-column-th">合計</th>
                        <th class="attendance-column-th">詳細</th>
                    </tr>
                </thead>

                <tbody class="attendance-whole">
                    @foreach ($records as $record)
                    <tr class="attendance-list">
                        <td class="date-list">{{ $record['user']->name }}</td>

                        @if ($record['attendance'])
                        <td class="starttime-list">{{ \Carbon\Carbon::parse($record['attendance']->start_time)->format('H:i') }}</td>
                        <td class="endtime-list">{{ \Carbon\Carbon::parse($record['attendance']->end_time)->format('H:i') }}</td>
                        <td class="breaktime-list">{{ sprintf('%d:%02d', intdiv($record['attendance']->breakMinutes, 60), $record['attendance']->breakMinutes % 60) }}</td>
                        <td class="workminutes-list">{{ sprintf('%d:%02d', intdiv($record['attendance']->workMinutes, 60), $record['attendance']->workMinutes % 60) }}</td>
                        <td class="show-list">
                            <a class="show-button" href="{{ route('admin.attendances.show', $record['attendance']->id) }}">詳 細</a>
                        </td>
                        @else
                        <td class="starttime-list"></td>
                        <td class="endtime-list"></td>
                        <td class="breaktime-list"></td>
                        <td class="workminutes-list"></td>
                        <td class="show-list">
                            <a class="show-button" href="{{ route('admin.attendances.create', ['user' => $record['user']->id, 'date' => $date->toDateString()]) }}">詳 細</a>
                        </td>
                        @endif
                    </tr>
                    @endforeach
                </tbody>
            </table>


        </div>
    </main>
</body>

</html>