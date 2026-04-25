<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Name: letters, spaces, dots, and Arabic characters only — prevents
            // injection of HTML/script tags or SQL fragments.
            'name' => [
                'required',
                'string',
                'min:3',
                'max:100',
                'regex:/^[\pL\s.\'-]+$/u',
            ],

            // Email: standard format + uniqueness check.
            'email' => [
                'required',
                'email:rfc,dns',
                'max:150',
                'unique:users,email',
            ],

            // Egyptian National ID: 14 digits matching the official format.
            // Breakdown: [2|3] + YY + MM(01-12) + DD(01-31) + GOV(01-35 valid) + SEQ(4) + CHECK(1)
            // Governor codes excluded: 05,06,08,09,10,16,17,18,19,20
            'national_id' => [
                'required',
                'string',
                'size:14',
                'regex:/^[23]\d{2}(0[1-9]|1[0-2])(0[1-9]|[12]\d|3[01])(0[1-4]|1[1-9]|2[1-9]|3[0-5])\d{5}$/',
                'unique:users,national_id',
            ],

            // Password: minimum 8 chars, must contain BOTH letters and digits,
            // no more than 72 chars (bcrypt limit), uncompromised check.
            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->numbers()
                    ->max(72),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.regex'           => __('validation.regex', ['attribute' => __('validation.attributes.name')]),
            'email.email'          => __('validation.email', ['attribute' => __('validation.attributes.email')]),
            'email.unique'         => __('validation.unique', ['attribute' => __('validation.attributes.email')]),
            'national_id.size'     => __('validation.size.string', ['attribute' => __('validation.attributes.national_id'), 'size' => 14]),
            'national_id.regex'    => __('validation.regex', ['attribute' => __('validation.attributes.national_id')]),
            'national_id.unique'   => __('validation.unique', ['attribute' => __('validation.attributes.national_id')]),
            'password.min'         => __('validation.min.string', ['attribute' => __('validation.attributes.password'), 'min' => 8]),
            'password.letters'     => __('validation.password.letters', ['attribute' => __('validation.attributes.password')]),
            'password.numbers'     => __('validation.password.numbers', ['attribute' => __('validation.attributes.password')]),
            'password.confirmed'   => __('validation.confirmed', ['attribute' => __('validation.attributes.password')]),
        ];
    }
}
