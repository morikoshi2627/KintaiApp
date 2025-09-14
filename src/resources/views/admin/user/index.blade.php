<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KintaiApp</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/admin/user/index.css') }}">

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
        </div>
    </main>
</body>

</html>