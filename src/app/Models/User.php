<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

use App\Models\Attendance;
use App\Models\AttendanceRequest;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function attendanceRequests()
    {
        return $this->hasMany(AttendanceRequest::class);
    }


    public function currentAttendanceStatus(): string
    {
        // 最新の勤怠を取得
        $latestAttendance = $this->attendances()->latest()->first();

        if (!$latestAttendance) {
            return '未出勤';
        }

        if (is_null($latestAttendance->end_time)) {
            if ($latestAttendance->breaks()->whereNull('end_time')->exists()) {
                return '休憩中';
            }
            return '出勤中';
        }

        return '退勤済み';
    }
}
