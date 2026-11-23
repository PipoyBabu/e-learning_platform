<?php

namespace App\Http\Requests;

use App\Enums\PaymentMethod;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GatewayCallbackRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'gateway_reference' => ['required', 'string'],
            'method' => ['required', Rule::in(array_column(PaymentMethod::cases(), 'value'))],
            'outcome' => ['required', Rule::in(['success', 'failed'])],
        ];
    }
}
