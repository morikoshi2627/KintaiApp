<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>KintaiApp</title>
  <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}" />
  <link rel="stylesheet" href="{{ asset('css/admin/attendance/index.css') }}">

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

      <h2 class="main-title">|{{$date}} の勤怠</h2>
      <a href="{{ route('admin.attendances', ['date' => \Carbon\Carbon::parse($date)->subDay()->toDateString()]) }}">前日</a>
      <a href="{{ route('admin.attendances', ['date' => \Carbon\Carbon::parse($date)->addDay()->toDateString()]) }}">翌日</a>

      <table>
        <thead>
          <tr>
            <th>名前</th>
            <th>出勤</th>
            <th>退勤</th>
            <th>休憩</th>
            <th>合計</th>
            <th>詳細</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($attendances as $attendance)
          <tr>
            <td>{{ $attendance->user->name }}</td>
            <td>{{ $attendance->start_time ?? '' }}</td>
            <td>{{ $attendance->end_time ?? '' }}</td>
            <td>{{ $attendance->break_time ?? '' }}</td>
            <td><a href="{{ route('admin.attendance.detail', $attendance->id) }}">詳細</a></td>
          </tr>
          @endforeach
        </tbody>
      </table>


    </div>
  </main>
</body>

</html>