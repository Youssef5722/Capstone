<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class StoreTeamRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'requested_name'   => ['nullable', 'string', 'max:255'],
            'project_idea_id'  => ['nullable', 'exists:project_ideas,id'],
        ];
    }

    /**
     * Custom after-validation rule: at least one of requested_name or
     * project_idea_id must be present (per SOP §8).
     */
    protected function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $name    = $this->input('requested_name');
            $ideaId  = $this->input('project_idea_id');

            if (empty($name) && empty($ideaId)) {
                $validator->errors()->add(
                    'requested_name',
                    __('validation.team_request_empty_change')
                );
            }
        });
    }
}
