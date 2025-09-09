<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

class AttendanceUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'start_time' => ['required', 'date_format:H:i'],
            'end_time'   => ['required', 'date_format:H:i', 'after:start_time'],

            'break_start.*' => ['nullable', 'date_format:H:i'],
            'break_end.*'   => ['nullable', 'date_format:H:i', 'after:break_start.*'],

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

            foreach ($breakStarts as $index => $breakStart) {
                $breakEnd = $breakEnds[$index] ?? null;

                if ($breakStart && $breakEnd) {
                    $breakStartTime = Carbon::parse($breakStart);
                    $breakEndTime   = Carbon::parse($breakEnd);

                    if ($breakStartTime < $startTime || $breakEndTime > $endTime) {
                        $validator->errors()->add(
                            "break_start.$index",
                            "休憩時間が不適切な値です"
                        );
                    }
                    if ($breakEndTime < $breakStartTime) {
                        $validator->errors()->add(
                            "break_end.$index",
                            "休憩終了時間は開始時間以降を指定してください"
                        );
                    }
                }
            }
        });
    }
}