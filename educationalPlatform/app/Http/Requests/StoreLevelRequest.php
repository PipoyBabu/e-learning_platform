<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLevelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return ['name' => ['required', 'string', 'max:255', 'unique:membership_levels,name'], 'fee' => ['required', 'numeric', 'min:0'], 'validity_months' => ['required', 'integer', 'min:1'], 'benefits' => ['nullable', 'string']];
    }
}
