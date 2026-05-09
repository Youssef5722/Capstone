<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DistributeTeamsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'team_size' => ['required', 'integer', 'min:2', 'max:20'],
            'mode'      => ['required', 'in:balanced,fixed'],
        ];
    }
}
