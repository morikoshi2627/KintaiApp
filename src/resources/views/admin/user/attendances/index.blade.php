<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KintaiApp</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/admin/user/attendance.css') }}">

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
            <h2 class="main-title">| {{ $user->name }}さんの勤怠 </h2>

            <div class="month-selector">
                <a class="attendance-date" href="{{ route('attendance.list', ['year' => $currentMonth->copy()->subMonth()->year, 'month' => $currentMonth->copy()->subMonth()->month]) }}">
                    ←前月
                </a>
                <div class="calendar-select-wrapper">
                    <img class="calendar-img" src="{{ asset('storage/calendar.png') }}" alt="カレンダー">

                    <span class="select-month">{{ $currentMonth->format('Y/m') }}</span>
                </div>

                <a class="attendance-date" href="{{ route('attendance.list', ['year' => $currentMonth->copy()->addMonth()->year, 'month' => $currentMonth->copy()->addMonth()->month]) }}">
                    翌月→
                </a>
            </div>

            <table class="attendance-table">
                <thead class="attendance-table-title">
                    <tr class="attendance-column">
                        <th class="attendance-column-th">日付</th>
                        <th class="attendance-column-th">出勤</th>
                        <th class="attendance-column-th">退勤</th>
                        <th class="attendance-column-th">休憩</th>
                        <th class="attendance-column-th">合計</th>
                        <th class="attendance-column-th">詳細</th>
                    </tr>
                </thead>

                <tbody class="attendance-whole">
                    @foreach($dates as $date)
                    @php
                    $attendance = $attendancesByDate[$date->toDateString()] ?? null;
                    @endphp
                    <tr class="attendance-list">
                        <td class="date-list">{{ $date->translatedFormat('m/d(D)') }}</td>
                        <td class="starttime-list">{{ $attendance?->start_time?->format('H:i') ?? '' }}</td>
                        <td class="endtime-list">{{ $attendance?->end_time?->format('H:i') ?? '' }}</td>
                        
                        <td class="breaktime-list">
                            @if($attendance && ($attendance->start_time || $attendance->end_time))
                            {{ sprintf('%d:%02d', intdiv($attendance->breakMinutes, 60), $attendance->breakMinutes % 60) }}
                            @endif
                        </td>
                        <td class="workminutes-list">
                            @if($attendance && ($attendance->start_time || $attendance->end_time))
                            {{ sprintf('%d:%02d', intdiv($attendance->workMinutes, 60), $attendance->workMinutes % 60) }}
                            @endif
                        </td>

                        <!-- 
                        <td class="breaktime-list">
                            @if($attendance !== null)
                            {{ sprintf('%d:%02d', intdiv($attendance->breakMinutes, 60), $attendance->breakMinutes % 60) }}
                            @endif
                        </td>
                        <td class="workminutes-list">
                            @if($attendance !== null)
                            {{ sprintf('%d:%02d', intdiv($attendance->workMinutes, 60), $attendance->workMinutes % 60) }}
                            @endif
                        </td> -->
                        <td class="show-list">
                            @if($attendance)
                            <a class="show-button" href="{{ route('admin.attendances.show', ['id' => $attendance->id]) }}">詳 細</a>
                            @else
                            <!-- 存在しない日は「新規作成リンク」 -->
                            <a class="show-button" href="{{ route('admin.attendances.create', ['user' => $user->id, 'date' => $date->toDateString()]) }}">
                                詳 細
                            </a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </main>
</body>

</html>