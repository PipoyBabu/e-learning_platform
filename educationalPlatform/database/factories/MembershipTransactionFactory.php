<?php

namespace Database\Factories;

use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Models\Member;
use App\Models\MembershipLevel;
use App\Models\MembershipTransaction;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<MembershipTransaction> */
class MembershipTransactionFactory extends Factory
{
    protected $model = MembershipTransaction::class;

    public function definition(): array
    {
        return [
            'member_id' => Member::factory(),
            'membership_level_id' => MembershipLevel::factory(),
            'type' => TransactionType::Application,
            'status' => TransactionStatus::Pending,
            'amount_due' => 1000,
            'starts_on' => now()->toDateString(),
            'expires_on' => now()->addYear()->toDateString(),
        ];
    }
}
