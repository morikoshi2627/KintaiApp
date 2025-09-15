<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AttendanceRequest;
use App\Models\Attendance;

class AdminAttendanceRequestController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status'); // 承認待ち or 承認済み

        $query = AttendanceRequest::with(['attendance', 'user']);

        if ($status === 'pending') {
            $query->where('attendance_requests.status', 'pending'); // ← テーブル名を明示
        } elseif ($status === 'approved') {
            $query->where('attendance_requests.status', 'approved'); // ← 同上
        }

        // 対象日時で昇順（古い順）に並べる
        $query->leftJoin('attendances', 'attendances.id', '=', 'attendance_requests.attendance_id')
            ->orderBy('attendances.attendance_date', 'asc')
            ->orderBy('attendance_requests.created_at', 'desc') // 作成日の降順も保持
            ->select('attendance_requests.*');

        $requests = $query->latest()->get();

        return view('admin.request.index', compact('requests', 'status'));
    }


    public function show($id)
    {
        $attendanceRequest = AttendanceRequest::with(['attendance.breakTimes', 'requestBreakTimes', 'user'])
            ->findOrFail($id);

        return view('admin.request.approve', compact('attendanceRequest'));
    }


    public function update(Request $request, $id)
    {
        $attendanceRequest = AttendanceRequest::with('attendance')->findOrFail($id);

        // 承認のみ対応
        $attendanceRequest->status = 'approved';
        $attendanceRequest->save();

        // 勤怠情報を申請内容で更新する
        if ($attendanceRequest->attendance) {
            $attendance = $attendanceRequest->attendance;
            $attendance->start_time   = $attendanceRequest->start_time;
            $attendance->end_time     = $attendanceRequest->end_time;
            $attendance->break_time   = $attendanceRequest->break_time;
            $attendance->save();
        }

        return redirect()->route('admin.requests', ['status' => 'approved']);
    }
}
