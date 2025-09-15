<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KintaiApp</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/admin/request/index.css') }}">

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
            <h2 class="main-title">| 申請一覧 </h2>

            <!-- タブ切替 -->
            <div class="filter-tabs">
                <a href="{{ route('admin.requests', ['status' => 'pending']) }}"
                    class="filter-tabs-a {{ $status === 'pending' ? 'active' : '' }}">
                    承認待ち
                </a>
                <a href="{{ route('admin.requests', ['status' => 'approved']) }}"
                    class="filter-tabs-a {{ $status === 'approved' ? 'active' : '' }}">
                    承認済み
                </a>
            </div>

            <!-- 一覧テーブル -->
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
                        @forelse ($requests as $req)
                        <tr class="application-update-tbody">
                            <!--  状態  -->
                            <td class="application-update-td">
                                @if($req->status === 'pending')
                                承認待ち
                                @elseif($req->status === 'approved')
                                承認済み
                                @else
                                {{ $req->status }}
                                @endif
                            </td>

                            <!-- 名前 -->
                            <td class="application-update-td">{{ $req->user->name }}</td>

                            <!-- 対象日時 -->
                            <td class="application-update-td">
                                {{ optional($req->attendance)->attendance_date 
                            ? \Carbon\Carbon::parse($req->attendance->attendance_date)->format('Y/m/d')
                            : '-' }}
                            </td>

                            <!-- 申請理由 -->
                            <td class="application-update-td">
                                {{ $req->request_reason }}
                            </td>

                            <!-- 申請日時 -->
                            <td class="application-update-td">
                                {{ $req->created_at->format('Y/m/d') }}
                            </td>

                            <!-- 詳細ボタン -->
                            <td>
                                <a class="detail-button" href="{{ route('admin.request.detail', $req->id) }}">詳 細</a>
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

        </div>
    </main>
</body>

</html>