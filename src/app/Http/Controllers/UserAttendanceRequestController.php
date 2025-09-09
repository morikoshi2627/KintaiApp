<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\AttendanceUpdateRequest;
use App\Models\Attendance;
use App\Models\AttendanceRequest;
use Illuminate\Support\Facades\Auth;

class UserAttendanceRequestController extends Controller
{

    public function index(Request $request)
    {
        $status = $request->query('status', 'pending'); // クエリパラメータが無ければ "pending" をデフォルトにして承認待ち or 承認済み

        $query = AttendanceRequest::with(['attendance', 'user'])
            ->where('user_id', Auth::id());

        if ($status === 'pending') {
            $query->where('status', 'pending');
        } elseif ($status === 'approved') {
            $query->where('status', 'approved');
        }

        $requests = $query->latest()->paginate(10);

        return view('attendance_request.index', compact('requests', 'status'));
    }

    public function store(AttendanceUpdateRequest $request, $attendanceId)
    {
        // 該当勤怠を取得
        $attendance = Attendance::findOrFail($attendanceId);

        // 修正申請を新規作成（承認待ちステータス）
        AttendanceRequest::create([
            'user_id' => Auth::id(),
            'attendance_id' => $attendance->id,
            'requested_start_time' => $request->requested_start_time,
            'requested_end_time' => $request->requested_end_time,
            'request_reason' => $request->request_reason,
            'status' => 'pending', // 承認待ち
        ]);

        return redirect()
            ->route('attendance_request.list');
    }

    public function show($id)
    {
        $attendanceRequest = AttendanceRequest::with(['attendance', 'breakTimes'])
            ->findOrFail($id);

        return view('attendance_requests.show', compact('attendanceRequest'));
    }
}