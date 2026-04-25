<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Email: basic format only — no DNS check at login (faster, avoids
            // timing differences that could hint at account existence).
            'email'    => ['required', 'string', 'email', 'max:150'],

            // Password: just present and a string — full complexity rules only
            // apply at registration, not every login attempt.
            'password' => ['required', 'string', 'max:72'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required'    => __('validation.required', ['attribute' => __('validation.attributes.email')]),
            'email.email'       => __('validation.email', ['attribute' => __('validation.attributes.email')]),
            'password.required' => __('validation.required', ['attribute' => __('validation.attributes.password')]),
        ];
    }
}
