<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AttendanceRequest;
use App\Models\User;
use Carbon\Carbon;

class AttendanceRequestsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run(): void
    {
        $users = User::all();
        $reasons = ['遅延', '早退', '私用', '交通遅延', '体調不良'];

        foreach ($users as $user) {
            $attendances = $user->attendances()
                ->orderBy('attendance_date', 'desc')
                ->take(10) // 直近10日分から抽選
                ->get();

            foreach ($attendances->random(min(5, $attendances->count())) as $attendance) {
                AttendanceRequest::create([
                    'user_id'              => $user->id,
                    'attendance_id'        => $attendance->id,
                    'request_date'         => $attendance->attendance_date,
                    'requested_start_time' => Carbon::parse($attendance->attendance_date)->setTime(rand(8, 10), [0, 15, 30, 45][array_rand([0, 1, 2, 3])]),
                    'requested_end_time'   => Carbon::parse($attendance->attendance_date)->setTime(rand(17, 20), [0, 15, 30, 45][array_rand([0, 1, 2, 3])]),
                    'request_type'         => 'time_correction',
                    'request_reason'       => $reasons[array_rand($reasons)] . 'による修正希望',
                    'status'               => 'pending',
                ]);
            }
        }
    }
}
