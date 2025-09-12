<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

class AdminAttendanceUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'start_time' => ['required', 'date_format:H:i'],
            'end_time'   => ['required', 'date_format:H:i', 'after:start_time'],

            'break_start.*' => ['nullable', 'date_format:H:i'],
            'break_end.*'   => ['nullable', 'date_format:H:i'],

            'request_reason' => ['required'],
        ];
    }

    public function messages(): array
    {
        return [
            'start_time.required' => '出勤時間を入力してください',
            'end_time.required'   => '退勤時間を入力してください',
            'end_time.after'      => '出勤時間もしくは退勤時間が不適切な値です',

            'request_reason.required' => '備考を記入してください',
        ];
    }

    /**
     * バリデーション後に追加チェック
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $startTime = Carbon::parse($this->input('start_time'));
            $endTime   = Carbon::parse($this->input('end_time'));

            $breakStarts = $this->input('break_start', []);
            $breakEnds   = $this->input('break_end', []);

            // 休憩時間チェック
            foreach ($breakStarts as $key => $breakStart) {
                $breakEnd = $breakEnds[$key] ?? null;

                // 両方空ならスキップ
                if (empty($breakStart) && empty($breakEnd)) {
                    continue;
                }

                if (empty($breakStart)) {
                    $validator->errors()->add("break_start.$key", "休憩開始時間を入力してください");
                    continue;
                }

                if (empty($breakEnd)) {
                    $validator->errors()->add("break_end.$key", "休憩終了時間を入力してください");
                    continue;
                }

                $breakStartTime = Carbon::parse($breakStart);
                $breakEndTime   = Carbon::parse($breakEnd);

                // 出勤・退勤範囲外
                if ($breakStartTime < $startTime || $breakEndTime > $endTime) {
                    $validator->errors()->add("break_start.$key", "休憩時間が不適切な値です");
                }

                // 開始より終了が前
                if ($breakEndTime < $breakStartTime) {
                    $validator->errors()->add("break_end.$key", "休憩終了時間は開始時間以降を指定してください");
                }
            }

            // 休憩同士の重複チェック
            $times = [];
            foreach ($breakStarts as $key => $breakStart) {
                if (!$breakStart || !($breakEnds[$key] ?? null)) continue;
                $times[] = [
                    'start' => Carbon::parse($breakStart),
                    'end'   => Carbon::parse($breakEnds[$key]),
                    'key'   => $key
                ];
            }

            for ($i = 0; $i < count($times); $i++) {
                for ($j = $i + 1; $j < count($times); $j++) {
                    if (
                        $times[$i]['start'] < $times[$j]['end'] &&
                        $times[$i]['end'] > $times[$j]['start']
                    ) {
                        $validator->errors()->add("break_start." . $times[$i]['key'], "休憩時間が重複しています");
                        $validator->errors()->add("break_start." . $times[$j]['key'], "休憩時間が重複しています");
                    }
                }
            }
        });
    }
}
