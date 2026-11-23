<?php

namespace Database\Factories;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\MembershipTransaction;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Payment> */
class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        return [
            'membership_transaction_id' => MembershipTransaction::factory(),
            'amount' => 1000,
            'status' => PaymentStatus::Succeeded,
            'gateway_reference' => 'SIM-'.fake()->unique()->bothify('######'),
            'method' => PaymentMethod::GCash,
            'paid_at' => now(),
        ];
    }
}
