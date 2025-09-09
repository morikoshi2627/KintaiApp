<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AttendanceRequest;

class AdminAttendanceRequestController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status'); // 承認待ち or 承認済み

        $query = AttendanceRequest::with(['attendance', 'user']);

        if ($status === 'pending') {
            $query->where('status', 'pending');
        } elseif ($status === 'approved') {
            $query->where('status', 'approved');
        }

        $requests = $query->latest()->get();

        return view('admin.requests.index', compact('requests', 'status'));
    }


    public function show($id)
    {
        $request = AttendanceRequest::with(['attendance', 'user'])->findOrFail($id);

        return view('admin.requests.show', compact('request'));
    }


    public function update(Request $request, $id)
    {
        $attendanceRequest = AttendanceRequest::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:approved,rejected',
        ]);

        $attendanceRequest->status = $validated['status'];
        $attendanceRequest->save();

        return redirect()->route('admin.requests');
    }
}
