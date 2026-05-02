<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StudentActivateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // activation_code is the lookup key — must exist in non-deleted students rows
            'activation_code' => [
                'required',
                'string',
                Rule::exists('students', 'activation_code')->whereNull('deleted_at'),
            ],
            // email is registered for the first time at activation — must be unique
            'email'           => 'required|email|unique:students,email',
            'password'        => 'required|string|min:8|confirmed',
        ];
    }
}
