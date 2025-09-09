<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RequestBreakTime;
use App\Models\Attendance;
use App\Models\AttendanceRequest;
use Carbon\Carbon;

class RequestBreakTimesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run(): void
    {
        $requests = AttendanceRequest::all();

        foreach ($requests as $request) {
            $date = $request->request_date;

            // 休憩1
            RequestBreakTime::create([
                'attendance_id'         => $request->attendance_id,
                'attendance_request_id' => $request->id,
                'break_started_at'      => Carbon::parse($date)->setTime(12, 0, 0),
                'break_ended_at'        => Carbon::parse($date)->setTime(12, 45, 0),
                'reason'                => '昼休憩',
                'status'                => 'pending',
            ]);

            // 休憩2（ランダムに追加することも可能）
            if (rand(0, 1)) {
                RequestBreakTime::create([
                    'attendance_id'         => $request->attendance_id,
                    'attendance_request_id' => $request->id,
                    'break_started_at'      => Carbon::parse($date)->setTime(15, 0, 0),
                    'break_ended_at'        => Carbon::parse($date)->setTime(15, 15, 0),
                    'reason'                => '小休憩',
                    'status'                => 'pending',
                ]);
            }
        }
    }
}
