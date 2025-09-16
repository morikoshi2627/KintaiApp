<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

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

    // 休憩時間（分単位）
    public function getBreakMinutesAttribute()
    {
        return $this->breakTimes->sum(function ($break) {
            if ($break->start_time && $break->end_time) {
                return Carbon::parse($break->end_time)->diffInMinutes(Carbon::parse($break->start_time));
            }
            return 0;
        });
    }

    // 勤務時間（分単位）
    public function getWorkMinutesAttribute()
    {
        if (!$this->start_time || !$this->end_time) {
            return 0;
        }

        $start = Carbon::parse($this->start_time);
        $end   = Carbon::parse($this->end_time);

        $workMinutes = $end->diffInMinutes($start);

        return $workMinutes - $this->breakMinutes;
    }

    // 「承認済みの備考」を簡単に引けるように アクセサ
    public function latestApprovedRequest()
    {
        return $this->hasOne(AttendanceRequest::class)
            ->where('status', 'approved')
            ->latestOfMany();
    }

    public function getRequestReasonAttribute()
    {
        return optional($this->latestApprovedRequest)->request_reason;
    }

    // リレーション
    
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

    public function attendanceRequest()
    {
        return $this->hasOne(AttendanceRequest::class);
    }
}
