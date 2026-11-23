<?php

namespace Database\Seeders;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Models\Member;
use App\Models\MembershipCategory;
use App\Models\MembershipLevel;
use App\Models\MembershipTransaction;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Axiom Admin',
            'email' => 'admin@axiom.test',
            'role' => 'admin',
            'password' => 'password',
        ]);

        $categories = collect([
            ['name' => 'Student', 'description' => 'For currently enrolled students.'],
            ['name' => 'Regular', 'description' => 'For standard individual members.'],
            ['name' => 'Senior Citizen', 'description' => 'For qualified senior citizen members.'],
            ['name' => 'Corporate', 'description' => 'For organizations and corporate partners.'],
        ])->map(fn (array $category) => MembershipCategory::create($category));

        $levels = collect([
            ['name' => 'Basic', 'fee' => 500, 'validity_months' => 3, 'benefits' => 'Course access and basic member support.'],
            ['name' => 'Silver', 'fee' => 1000, 'validity_months' => 6, 'benefits' => 'Course access, certificates, and priority support.'],
            ['name' => 'Gold', 'fee' => 1800, 'validity_months' => 12, 'benefits' => 'All Silver benefits plus workshops and mentoring.'],
            ['name' => 'Platinum', 'fee' => 3000, 'validity_months' => 12, 'benefits' => 'All benefits plus premium events and dedicated support.'],
        ])->map(fn (array $level) => MembershipLevel::create($level));

        $members = Member::factory(12)->sequence(...MemberLocationSeeder::locations())->create([
            'membership_category_id' => fn () => $categories->random()->id,
            'membership_level_id' => fn () => $levels->random()->id,
        ]);

        $transactions = collect([
            ['member' => 0, 'level' => 0, 'type' => TransactionType::Application, 'status' => TransactionStatus::Pending, 'months_ago' => 0],
            ['member' => 1, 'level' => 1, 'type' => TransactionType::Application, 'status' => TransactionStatus::Paid, 'months_ago' => 1],
            ['member' => 2, 'level' => 2, 'type' => TransactionType::Application, 'status' => TransactionStatus::Approved, 'months_ago' => 2],
            ['member' => 3, 'level' => 3, 'type' => TransactionType::Application, 'status' => TransactionStatus::Pending, 'months_ago' => 3],
            ['member' => 4, 'level' => 0, 'type' => TransactionType::Application, 'status' => TransactionStatus::Cancelled, 'months_ago' => 4],
            ['member' => 5, 'level' => 1, 'type' => TransactionType::Renewal, 'status' => TransactionStatus::Approved, 'months_ago' => 5],
            ['member' => 6, 'level' => 2, 'type' => TransactionType::Application, 'status' => TransactionStatus::Pending, 'months_ago' => 0],
        ])->map(function (array $data) use ($members, $levels): MembershipTransaction {
            $member = $members[$data['member']];
            $level = $levels[$data['level']];
            $createdAt = now()->startOfMonth()->subMonths($data['months_ago'])->addDays(3);
            $transaction = MembershipTransaction::create([
                'member_id' => $member->id,
                'membership_level_id' => $level->id,
                'type' => $data['type'],
                'status' => $data['status'],
                'amount_due' => $level->fee,
                'starts_on' => $data['type'] === TransactionType::Renewal ? $createdAt->toDateString() : null,
                'expires_on' => $data['type'] === TransactionType::Renewal ? $createdAt->copy()->addMonths($level->validity_months)->toDateString() : null,
            ]);
            $transaction->forceFill(['created_at' => $createdAt, 'updated_at' => $createdAt])->save();

            return $transaction;
        });

        foreach ([1 => 1000, 2 => 1800, 3 => 300, 5 => 1000, 6 => 750] as $index => $amount) {
            $transaction = $transactions[$index];
            $succeeded = $transaction->status !== TransactionStatus::Pending || $index === 6;
            $transaction->payments()->create([
                'amount' => $amount,
                'status' => $succeeded ? PaymentStatus::Succeeded : PaymentStatus::Failed,
                'gateway_reference' => 'SEED-'.$transaction->id,
                'method' => PaymentMethod::cases()[$index % count(PaymentMethod::cases())],
                'paid_at' => $succeeded ? $transaction->created_at : null,
                'created_at' => $transaction->created_at,
                'updated_at' => $transaction->created_at,
            ]);
        }
    }
}
