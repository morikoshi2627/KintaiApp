<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Attendance;


class AdminAttendanceController extends Controller
{
    /* 日次勤怠一覧 */
    public function index(Request $request)
    {
        // 日付をリクエストから取得。なければ今日の日付を使う
        $date = $request->input('date', Carbon::today()->toDateString());

        // 指定した日付の勤怠を全ユーザー分取得
        $attendances = Attendance::with('user')
            ->whereDate('attendance_date', $date)
            ->orderBy('user_id')
            ->get();

        return view('admin.attendances.index', compact('attendances', 'date'));
    }



    
    /* 勤怠詳細 */
    public function show($id)
    {
        $attendance = Attendance::with('user')->findOrFail($id);

        return view('admin.attendances.show', compact('attendance'));
    }

    /* 管理者による修正 */
    public function update(Request $request, $id)
    {
        $attendance = Attendance::findOrFail($id);

        // バリデーション
        $request->validate([
            'start_time' => ['nullable', 'date'],
            'end_time'   => ['nullable', 'date', 'after_or_equal:start_time'],
            'break_time' => ['nullable', 'integer', 'min:0'],
        ]);

        $attendance->update([
            'start_time' => $request->start_time,
            'end_time'   => $request->end_time,
            'break_time' => $request->break_time,
        ]);

        return redirect()->route('admin.attendances', ['date' => $attendance->date]);
    }
}
