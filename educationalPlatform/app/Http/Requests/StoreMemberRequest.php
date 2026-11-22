<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return ['first_name' => ['required', 'string', 'max:255'], 'last_name' => ['required', 'string', 'max:255'], 'email' => ['required', 'email', 'max:255', 'unique:members,email'], 'phone' => ['nullable', 'string', 'max:50'], 'membership_category_id' => ['required', 'exists:membership_categories,id'], 'membership_level_id' => ['required', 'exists:membership_levels,id'], 'joined_on' => ['required', 'date']];
    }
}
