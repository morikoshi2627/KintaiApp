<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\AttendanceUpdateRequest;
use App\Models\Attendance;
use App\Models\AttendanceRequest;
use App\Models\RequestBreakTime;
use App\Models\BreakTime;
use Carbon\Carbon;

class UserAttendanceController extends Controller
{

    // 出勤登録画面
    public function index()
    {
        $user = auth()->user();
        // 本日または最新の勤怠
        $today = Attendance::where('user_id', $user->id)
            ->where('attendance_date', now()->toDateString())
            ->latest('attendance_date')
            ->first();

        $attendanceId = $today?->id; // nullsafe演算子で出勤記録がない場合は null

        // 勤怠ステータス判定
        if (!$today) {
            $status = '勤務外';
        } elseif (!$today->end_time) {
            $status = ($today->status === 'break') ? '休憩中' : '出勤中';
        } else {
            $status = '退勤済';
        }

        return view('attendance.form', compact('status', 'attendanceId'));
    }

    // 打刻処理（store で出勤・退勤・休憩入・休憩戻を分岐）
    public function store(Request $request)
    {
        $user = auth()->user();

        $attendance = Attendance::where('user_id', $user->id)
            ->where('attendance_date', now()->toDateString())
            ->latest('attendance_date')
            ->first();

        if ($request->has('clock_in')) {
            Attendance::create([
                'user_id' => $user->id,
                'attendance_date' => now()->toDateString(),
                'start_time' => now(),
                'status' => 'working',
            ]);
        } elseif ($request->has('clock_out')) {
            if ($attendance) {
                $attendance->update([
                    'end_time' => now(),
                    'status' => 'finished',
                ]);
            }
        } elseif ($request->has('break_in')) {
            if ($attendance && $attendance->status === 'working') {
                $attendance->update(['status' => 'break']);
                // BreakTime テーブルに開始時間を登録
                BreakTime::create([
                    'attendance_id' => $attendance->id,
                    'start_time' => now(),
                ]);
            }
        } elseif ($request->has('break_out')) {
            if ($attendance && $attendance->status === 'break') {
                $attendance->update(['status' => 'working']);
                // BreakTime テーブルの最新レコードに終了時間を登録
                $break = BreakTime::where('attendance_id', $attendance->id)
                    ->latest('id')
                    ->first();
                if ($break) {
                    $break->update(['end_time' => now()]);
                }
            }
        }

        return redirect()->route('attendance.index');
    }

    // 勤怠一覧
    public function list(Request $request)
    {
        $user = auth()->user();

        // クエリから指定された年月を取得（なければ現在の年月）
        $year = $request->input('year', now()->year);
        $month = $request->input('month', now()->month);

        // 指定月の勤怠を取得
        $attendances = Attendance::where('user_id', $user->id)
            ->whereYear('attendance_date', $year)
            ->whereMonth('attendance_date', $month)
            ->orderBy('attendance_date', 'asc')
            ->get();


        // 各勤怠に休憩時間・合計時間を追加
        foreach ($attendances as $attendance) {
            // 休憩合計（分）
            $breakMinutes = $attendance->breakTimes->sum(function ($b) {
                return $b->end_time ? $b->end_time->diffInMinutes($b->start_time) : 0;
            });

            // 合計労働時間（分）
            $workMinutes = ($attendance->end_time && $attendance->start_time)
                ? $attendance->end_time->diffInMinutes($attendance->start_time) - $breakMinutes
                : 0;

            // 動的プロパティをセット（Blade で使える）
            $attendance->breakMinutes = $breakMinutes;
            $attendance->workMinutes = $workMinutes;
        }

        // 表示用に Carbon インスタンス
        $currentMonth = Carbon::create($year, $month, 1);

        // 1ヶ月分の日付配列
        $daysInMonth = $currentMonth->daysInMonth;
        $dates = [];
        for ($i = 1; $i <= $currentMonth->daysInMonth; $i++) {
            $dates[] = $currentMonth->copy()->day($i);
        }
        // 勤怠データを日付キーで整理
        $attendancesByDate = $attendances->keyBy(fn($a) => $a->attendance_date->toDateString());

        // ヘッダー用に今日の勤怠ID
        $today = Attendance::where('user_id', $user->id)
            ->where('attendance_date', now()->toDateString())
            ->latest('attendance_date')
            ->first();

        $attendanceId = $today?->id;

        return view('attendance.index', compact('attendances', 'currentMonth', 'attendanceId', 'attendancesByDate', 'dates'));
    }

    // 勤怠詳細表示
    public function show($id)
    {
        $user = auth()->user();

        $attendance = Attendance::with(['breakTimes', 'attendanceRequests.requestBreakTimes'])
            ->where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $attendanceId = $attendance->id;

        // 最新の修正申請（未承認のもの）
        $pendingRequest = $attendance->attendanceRequests()
            ->where('status', 'pending')
            ->latest()
            ->with('requestBreakTimes')
            ->first();

        $displayData = (object) [
            'date' => $attendance->attendance_date,
            'start_time' => $pendingRequest?->requested_start_time ?? $attendance->start_time,
            'end_time'   => $pendingRequest?->requested_end_time ?? $attendance->end_time,
            'breakTimes' => $pendingRequest?->requestBreakTimes->isNotEmpty()
                ? $pendingRequest->requestBreakTimes
                : $attendance->breakTimes,
            'newBreak' => (object) [ // 新規入力用（old対応）
                'start' => old('break_start.new'),
                'end'   => old('break_end.new'),
            ],
            'request_reason' => $pendingRequest?->request_reason ?? $attendance->request_reason,
            'is_pending' => (bool) $pendingRequest,
        ];

        return view('attendance.show', compact(
            'attendance',
            'attendanceId',
            'pendingRequest',
            'displayData'
        ));
    }

    // 勤怠修正申請更新
    public function update(AttendanceUpdateRequest $request, $id): RedirectResponse
    {
        $attendance = Attendance::with('attendanceRequests')->findOrFail($id);

        $pendingRequest = $attendance->attendanceRequests()
            ->where('status', 'pending')
            ->latest()
            ->first();

        if ($pendingRequest) {
            return redirect()
                ->route('attendance.detail', ['id' => $id])
                ->with('error', '承認待ちのため修正はできません。');
        }

        $validated = $request->validated();

        // 勤怠修正申請を作成
        $attendanceRequest = AttendanceRequest::create([
            'user_id' => auth()->id(),
            'attendance_id' => $attendance->id,
            'request_date' => $attendance->attendance_date,
            'requested_start_time' => $validated['start_time'],
            'requested_end_time'   => $validated['end_time'],
            'request_type' => '修正申請',
            'request_reason' => $validated['request_reason'] ?? '',
            'status' => 'pending',
        ]);

        // 既存休憩の修正申請
        if (isset($validated['break_start'], $validated['break_end'])) {
            foreach ($validated['break_start'] as $key => $start) {
                if (!is_numeric($key)) continue; // 'new' はスキップ
                $end = $validated['break_end'][$key] ?? null;
                if (!$start || !$end) continue;

                RequestBreakTime::create([
                    'attendance_request_id' => $attendanceRequest->id,
                    'attendance_id' => $attendance->id,
                    'break_started_at' => Carbon::parse($start),
                    'break_ended_at' => Carbon::parse($end),
                    'reason' => '',
                    'status' => 'pending',
                ]);
            }
        }

        // 新規休憩（休憩2）
        if (!empty($validated['break_start']['new'] ?? null) && !empty($validated['break_end']['new'] ?? null)) {
            RequestBreakTime::create([
                'attendance_request_id' => $attendanceRequest->id,
                'attendance_id' => $attendance->id,
                'break_started_at' => Carbon::parse($validated['break_start']['new']),
                'break_ended_at' => Carbon::parse($validated['break_end']['new']),
                'reason' => '',
                'status' => 'pending',
            ]);
        }

        return redirect()->route('attendance.detail', ['id' => $id]);
    }
}
