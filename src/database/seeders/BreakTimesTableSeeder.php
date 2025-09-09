<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BreakTime;
use App\Models\Attendance;
use Carbon\Carbon;

class BreakTimesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $attendances = Attendance::all();

        foreach ($attendances as $attendance) {
            $startTime = Carbon::parse($attendance->start_time);
            $endTime   = Carbon::parse($attendance->end_time);
            $workDuration = $startTime->diffInMinutes($endTime);

            // 休憩回数（1〜2回）
            $breakCount = rand(1, 2);

            for ($i = 0; $i < $breakCount; $i++) {
                // 出勤〜退勤の間でランダムに休憩開始
                $breakStart = $startTime->copy()->addMinutes(rand(120, $workDuration - 120));
                // 休憩時間は 30〜60分
                $breakEnd = $breakStart->copy()->addMinutes(rand(30, 60));

                // 退勤時間を超えないよう調整
                if ($breakEnd > $endTime) {
                    $breakEnd = $endTime->copy()->subMinutes(15);
                }

                BreakTime::create([
                    'attendance_id' => $attendance->id,
                    'start_time'    => $breakStart,
                    'end_time'      => $breakEnd,
                ]);
            }
        }
    }
}
