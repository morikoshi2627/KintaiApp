<?php

namespace App\Http\Controllers;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class AdminStaffController extends Controller
{
    /* スタッフ一覧 */
    public function index()
    {        
        // 一般ユーザーを全件取得
        $users = User::orderBy('id')->get();

        return view('admin.user.index', compact('users'));
    }

    public function attendances($user)
    { /* 特定スタッフの勤怠一覧 */
    }
}
