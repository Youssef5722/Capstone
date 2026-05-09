<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTeamRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'          => ['nullable', 'string', 'max:255'],
            'leader_id'     => ['required', 'exists:students,id'],
            'student_ids'   => ['required', 'array', 'min:1'],
            'student_ids.*' => ['exists:students,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'leader_id.required'     => __('cms.teams.leader_required'),
            'student_ids.required'   => __('cms.teams.members_required'),
            'student_ids.min'        => __('cms.teams.members_required'),
        ];
    }
}
