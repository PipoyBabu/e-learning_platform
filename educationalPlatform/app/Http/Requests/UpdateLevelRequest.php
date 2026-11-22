<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLevelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return ['name' => ['required', 'string', 'max:255', Rule::unique('membership_levels', 'name')->ignore($this->route('level'))], 'fee' => ['required', 'numeric', 'min:0'], 'validity_months' => ['required', 'integer', 'min:1'], 'benefits' => ['nullable', 'string']];
    }
}
