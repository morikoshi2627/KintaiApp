<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'attendance_id',
        'request_date',
        'requested_start_time',
        'requested_end_time',
        'request_type',
        'request_reason',
        'status',
    ];

    protected $casts = [
        'requested_start_time' => 'datetime:H:i',
        'requested_end_time' => 'datetime:H:i',
    ];

    // ユーザーとのリレーション
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 勤怠とのリレーション
    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }

    // 休憩申請とのリレーション
    public function breakTimes()
    {
        return $this->hasMany(RequestBreakTime::class);
    }

    //  勤怠申請に紐づく休憩申請（複数）
    
    public function requestBreakTimes()
    {
        return $this->hasMany(RequestBreakTime::class, 'attendance_request_id');
    }
}
