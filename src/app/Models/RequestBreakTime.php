<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestBreakTime extends Model
{
    use HasFactory;

    protected $fillable = [
        'attendance_request_id',
        'attendance_id',
        'break_started_at',
        'break_ended_at',
        'reason',
        'status',
        'reviewed_at',
        'reviewed_by',
    ];

    protected $casts = [
        'break_started_at' => 'datetime',
        'break_ended_at'   => 'datetime',
    ];

    // 紐づく勤怠
    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }

    // 承認担当者（管理者）
    public function reviewer()
    {
        return $this->belongsTo(Admin::class, 'reviewed_by');
    }

    // 休憩申請が属する勤怠申請
    public function attendanceRequest()
    {
        return $this->belongsTo(AttendanceRequest::class, 'attendance_request_id');
    }
}
