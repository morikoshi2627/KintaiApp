<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;

class AttendancesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $users = User::all();

        foreach ($users as $user) {
            // 直近30日分
            for ($i = 0; $i < 90; $i++) {
                $date = Carbon::today()->subDays($i);

                // 出勤時間をランダムに設定（8:00〜10:00 の範囲）
                $startHour = rand(8, 10);
                $startMinute = [0, 15, 30, 45][array_rand([0, 15, 30, 45])];
                $startTime = $date->copy()->setTime($startHour, $startMinute);

                // 勤務時間は 7〜9時間程度にランダム設定
                $workDuration = rand(7, 9) * 60; // 分に変換
                $endTime = $startTime->copy()->addMinutes($workDuration);

                Attendance::create([
                    'user_id'          => $user->id,
                    'attendance_date'  => $date->toDateString(),
                    'start_time'       => $startTime,
                    'end_time'         => $endTime,
                ]);
            }
        }
    }
}
