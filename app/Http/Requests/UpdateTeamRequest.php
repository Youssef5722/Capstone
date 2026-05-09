<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTeamRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'      => ['nullable', 'string', 'max:255'],
            'leader_id' => ['required', 'exists:students,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'leader_id.required' => __('cms.teams.leader_required'),
        ];
    }
}
