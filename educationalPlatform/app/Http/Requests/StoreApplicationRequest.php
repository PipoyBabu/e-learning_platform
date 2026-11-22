<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return ['member_id' => ['required', 'exists:members,id'], 'membership_level_id' => ['required', 'exists:membership_levels,id']];
    }
}
