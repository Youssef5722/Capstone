<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImportStudentsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authorization handled by doctor.level middleware
    }

    public function rules(): array
    {
        return [
            'file'                => 'required|file|mimes:xlsx,xls|max:5120', // 5 MB max
            'activation_deadline' => 'required|date|after:today',             // Fix 1
        ];
    }

    public function messages(): array
    {
        return [
            'file.required'                => 'Please select an Excel file to upload.',
            'file.mimes'                   => 'Only Excel files (.xlsx, .xls) are allowed.',
            'file.max'                     => 'The file must not exceed 5 MB.',
            'activation_deadline.required' => 'An activation deadline is required.',
            'activation_deadline.date'     => 'The activation deadline must be a valid date.',
            'activation_deadline.after'    => 'The activation deadline must be a future date.',
        ];
    }
}
