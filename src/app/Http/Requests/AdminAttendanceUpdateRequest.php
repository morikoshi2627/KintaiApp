<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminAttendanceUpdateRequest extends FormRequest
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
            'start_time' => ['required', 'date'],
            'end_time' => ['required', 'date', 'after:start_time'],
            'break_start' => ['nullable', 'date', 'before:break_end'],
            'break_end' => ['nullable', 'date', 'after:break_start'],
            'request_reason' => ['required'],
        ];
    }

    public function messages(): array
    {
        return [
            'start_time.required' => '出勤時間を入力してください',
            'end_time.after' => '出勤時間が不適切な値です',
            'break_start.before' => '休憩時間が不適切な値です',
            'break_end.after' => '休憩時間もしくは退勤時間が不適切な値です',
            'request_reason.required' => '備考を記入してください',
        ];
    }
}
