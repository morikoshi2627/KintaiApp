<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'attendance_date',
        'start_time',
        'end_time',
        'status',
    ];

    // DBから取得した値を自動的に型変換する
    protected $casts = [
        'attendance_date' => 'date',      // 日付型（CarbonImmutable）
        'start_time'      => 'datetime:H:i',  // 時刻型（Carbon）
        'end_time'        => 'datetime:H:i',  // 時刻型（Carbon）
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    // 既存休憩
    public function breakTimes()
    {
        return $this->hasMany(BreakTime::class);
    }

    // 修正申請リレーション
    public function attendanceRequests()
    {
        return $this->hasMany(AttendanceRequest::class);
    }

    // 修正申請休憩リレーション
    public function requestBreakTimes()
    {
        return $this->hasMany(RequestBreakTime::class);
    }

    public function request()
    {
        return $this->hasOne(AttendanceRequest::class);
    }
}
