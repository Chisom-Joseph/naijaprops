<?php

namespace App\Http\Requests\Report;

use Illuminate\Foundation\Http\FormRequest;

class StoreReportRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null && $this->user()->status === 'active';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'reason' => ['required', 'string', 'min:10', 'max:1500'],
        ];
    }

    /**
     * Custom validation messages for property report submission.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'reason.required' => 'Please provide a reason for reporting this property listing.',
            'reason.min' => 'Please describe the issue in at least 10 characters so our administrators can investigate.',
            'reason.max' => 'Your report reason cannot exceed 1,500 characters.',
        ];
    }
}
