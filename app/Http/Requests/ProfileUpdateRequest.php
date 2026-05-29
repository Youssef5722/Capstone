<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ProfileUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        $userId = Auth::id();
        $isDoctor = Auth::user()?->role?->name === 'doctor';

        $rules = [
            'name'  => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', "unique:users,email,{$userId}"],
        ];

        if ($isDoctor) {
            $rules['phone'] = ['nullable', 'digits:11'];
        }

        return $rules;
    }
}
