<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StudentActivateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // activation_code is the lookup key — must exist in the students table
            'activation_code' => 'required|string|exists:students,activation_code',
            // email is registered for the first time at activation — must be unique
            'email'           => 'required|email|unique:students,email',
            'password'        => 'required|string|min:8|confirmed',
        ];
    }
}

