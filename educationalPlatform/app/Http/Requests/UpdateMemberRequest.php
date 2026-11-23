<?php

namespace App\Http\Requests;

use App\Models\Member;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $member = $this->route('member');

        return ['first_name' => ['required', 'string', 'max:255'], 'last_name' => ['required', 'string', 'max:255'], 'email' => ['required', 'email', 'max:255', Rule::unique('members', 'email')->ignore($member instanceof Member ? $member->id : $member)], 'phone' => ['nullable', 'string', 'max:50'], 'membership_category_id' => ['required', 'exists:membership_categories,id'], 'membership_level_id' => ['required', 'exists:membership_levels,id'], 'joined_on' => ['required', 'date'], 'address' => ['nullable', 'string', 'max:255'], 'latitude' => ['nullable', 'numeric', 'between:-90,90'], 'longitude' => ['nullable', 'numeric', 'between:-180,180']];
    }
}
