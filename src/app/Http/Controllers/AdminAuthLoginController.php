<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminLoginRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AdminAuthLoginController extends Controller
{

    // 管理者ログイン画面表示
    public function create()
    {
        return view('admin.auth.login');
    }

    // 管理者ログイン処理
    public function store(AdminLoginRequest $request)
    {
        // ログイン後リダイレクト先
        return redirect()->intended(route('admin.attendances'));
    }

    // 管理者ログアウト処理
    public function destroy(Request $request)
    {
        Auth::guard('admin')->logout();

        // セッションをリセット
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
