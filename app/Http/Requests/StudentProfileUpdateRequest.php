<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StudentProfileUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::guard('student')->check();
    }

    public function rules(): array
    {
        $studentId = Auth::guard('student')->id();

        return [
            'email' => ['required', 'email', 'max:150', "unique:students,email,{$studentId}"],
        ];
    }
}
