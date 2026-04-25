<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProjectIdeaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authorization handled by doctor.level middleware + ownership check in controller
    }

    public function rules(): array
    {
        return [
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
        ];
    }
}
