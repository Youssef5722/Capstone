<?php

namespace App\Http\Requests;

use App\Models\AcademicYear;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StoreProjectIdeaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authorization handled by doctor.level middleware
    }

    public function rules(): array
    {
        return [
            // PREP-3: Title unique per doctor + level + academic year scope.
            'title' => [
                'required',
                'string',
                'max:255',
                Rule::unique('project_ideas')
                    ->where('doctor_id',        Auth::id())
                    ->where('level_id',         $this->route('level'))
                    ->where('academic_year_id', AcademicYear::active()?->id),
            ],
            'description' => 'nullable|string',
        ];
    }
}
