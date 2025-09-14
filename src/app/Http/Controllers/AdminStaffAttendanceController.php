<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use Carbon\Carbon;
use App\Models\User;

class AdminStaffAttendanceController extends Controller
{
    /* ユーザー別勤怠一覧 */
    public function index($id, Request $request)
    {
        $user = User::findOrFail($id);

        // 年月取得（リクエストまたは今月）
        $year = $request->input('year', now()->year);
        $month = $request->input('month', now()->month);

        // 指定スタッフの勤怠を取得
        $attendances = Attendance::where('user_id', $user->id)
            ->whereYear('attendance_date', $year)
            ->whereMonth('attendance_date', $month)
            ->orderBy('attendance_date')
            ->get();

        // 各勤怠に休憩時間・合計時間を追加
        foreach ($attendances as $attendance) {
            $breakMinutes = $attendance->breakTimes->sum(function ($b) {
                return $b->end_time ? $b->end_time->diffInMinutes($b->start_time) : 0;
            });

            $workMinutes = ($attendance->start_time && $attendance->end_time)
                ? $attendance->end_time->diffInMinutes($attendance->start_time) - $breakMinutes
                : 0;

            $attendance->breakMinutes = $breakMinutes;
            $attendance->workMinutes = $workMinutes;
        }

        // 表示用の日付配列（1ヶ月分）
        $currentMonth = Carbon::create($year, $month, 1);
        $dates = [];
        for ($i = 1; $i <= $currentMonth->daysInMonth; $i++) {
            $dates[] = $currentMonth->copy()->day($i);
        }

        $attendancesByDate = $attendances->keyBy(fn($a) => $a->attendance_date->toDateString());

        return view('admin.user.attendances.index', compact('user', 'currentMonth', 'dates', 'attendancesByDate'));
    }

    public function export($user)
    { /* CSV出力 */
    }

}