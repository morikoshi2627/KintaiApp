<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdminAttendanceUpdateRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Attendance;
use App\Models\User;


class AdminAttendanceController extends Controller
{
    /* 日次勤怠一覧 */
    public function index(Request $request)
    {

        // 日付をリクエストから取得（デフォルト: 今日）
        $date = Carbon::parse($request->input('date', Carbon::today()));

        // 全ユーザー取得
        $users = User::orderBy('id')->get();

        // この日の勤怠をまとめて取得
        $attendances = Attendance::with(['user', 'breakTimes'])
            ->whereDate('attendance_date', $date)
                    ->get()
                    ->keyBy('user_id');

        // 各ユーザーごとに「勤怠 or null」をセット
        $records = $users->map(
            function ($user) use ($attendances) {
                $attendance = $attendances->get($user->id);

                if ($attendance) {
                    // 休憩合計（分）
                    $breakMinutes = $attendance->breakTimes->sum(function ($b) {
                        return $b->end_time ? $b->end_time->diffInMinutes($b->start_time) : 0;
                    });

                    // 合計労働時間（分）
                    $workMinutes = ($attendance->end_time && $attendance->start_time)
                        ? $attendance->end_time->diffInMinutes($attendance->start_time) - $breakMinutes
                        : 0;

                    $attendance->breakMinutes = $breakMinutes;
                    $attendance->workMinutes = $workMinutes;
                }

                return [
                    'user' => $user,
                    'attendance' => $attendance,
                ];
            }
        );

        return view('admin.attendances.index', compact('records', 'date'));
    }

    /* 勤怠詳細 */
    public function show($id)
    {

        $attendance = Attendance::with(['user', 'breakTimes'])
            ->find($id);

        if (!$attendance) {
    
            return view('admin.attendances.create', [
                'date' => now()->toDateString(), // 仮に今日の日付
            ]);
        }

        return view('admin.attendances.show', compact('attendance'));
    }

    /* 新規作成画面 */
    public function create($user, $date)
    {
        $user = User::findOrFail($user);

        // 空の Attendance インスタンス
        $attendance = new Attendance([
            'user_id' => $user->id,
            'attendance_date' => $date,
        ]);

        $attendance->setRelation('breakTimes', collect());

        return view('admin.attendances.show', compact('attendance'));
    }

    /* 登録処理 */
    public function store(AdminAttendanceUpdateRequest $request)
    {
        $validated = $request->validated();

        // attendance_date の日付部分のみ取得
        $date = Carbon::parse($validated['attendance_date'])->toDateString(); // "Y-m-d"

        $attendance = Attendance::create([
            'user_id'         => $validated['user_id'],
            'attendance_date' => $validated['attendance_date'],
            'start_time'      => $validated['start_time'] ? Carbon::parse($date . ' ' . $validated['start_time']) : null,
            'end_time'        => $validated['end_time']   ? Carbon::parse($date . ' ' . $validated['end_time'])   : null,
            'request_reason' => $validated['request_reason'] ?? null,
        ]);

        // 休憩時間が入力されていたら登録
        if (!empty($validated['break_start']) && is_array($validated['break_start'])) {
            foreach ($validated['break_start'] as $key => $start) {
                $end = $validated['break_end'][$key] ?? null;
                if ($start && $end) {
                    $attendance->breakTimes()->create([
                        'start_time' => Carbon::parse($date . ' ' . $start),
                        'end_time'   => Carbon::parse($date . ' ' . $end),
                    ]);
                }
            }
        }

        return redirect()->route('admin.attendances', ['date' => $attendance->attendance_date]);
    }

    /* 更新処理 */
    public function update(AdminAttendanceUpdateRequest $request, $id)
    {
        $attendance = Attendance::findOrFail($id);
        $validated = $request->validated();

        $date = Carbon::parse($attendance->attendance_date)->toDateString();
        
        $attendance->update([
            'start_time'     => Carbon::parse($date . ' ' . $validated['start_time']),
            'end_time'       => Carbon::parse($date . ' ' . $validated['end_time']),
            'request_reason' => $validated['request_reason'],
        ]);

        // 休憩時間を更新（例: 1件のみ管理するケース）
        if (!empty($validated['break_start']) && !empty($validated['break_end'])) {
            $attendance->breakTimes()->delete(); // 既存削除

            foreach ($validated['break_start'] as $key => $start) {
                $end = $validated['break_end'][$key] ?? null;

                if (!empty($start) && !empty($end)) {
                    $attendance->breakTimes()->create([
                        'start_time' => Carbon::parse($date . ' ' . $start),
                        'end_time'   => Carbon::parse($date . ' ' . $end),
                    ]);
                }
            }

        return redirect()->route('admin.attendances.show', $attendance->id);
        }
    }
}
