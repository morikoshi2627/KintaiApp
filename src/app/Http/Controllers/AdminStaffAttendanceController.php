<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\Response;

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

    // CSVボタン
    public function export($id, Request $request)
    {
        $year = $request->query('year');
        $month = $request->query('month');

        $user = User::findOrFail($id);

        // 当該月の勤怠データを取得
        $attendances = $user->attendances()
            ->whereYear('attendance_date', $year)
            ->whereMonth('attendance_date', $month)
            ->orderBy('attendance_date')
            ->get();

        // CSVのヘッダー
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="attendance_' . $user->id . '_' . $year . '_' . $month . '.csv"',
        ];

        $columns = ['日付', '出勤時間', '退勤時間', '休憩時間', '勤務合計時間'];

        $callback = function () use ($attendances, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($attendances as $attendance) {
                $row = [
                    $attendance->attendance_date->format('Y-m-d'),
                    $attendance->start_time?->format('H:i') ?? '',
                    $attendance->end_time?->format('H:i') ?? '',
                    sprintf('%d:%02d', intdiv($attendance->breakMinutes, 60), $attendance->breakMinutes % 60),
                    sprintf('%d:%02d', intdiv($attendance->workMinutes, 60), $attendance->workMinutes % 60),
                ];
                fputcsv($file, $row);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

}