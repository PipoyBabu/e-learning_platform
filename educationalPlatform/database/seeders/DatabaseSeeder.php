<?php

namespace Database\Seeders;

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
            'name' => 'Puihaha Admin',
            'email' => 'admin@puihaha.test',
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

        $members = Member::factory(12)->create([
            'membership_category_id' => fn () => $categories->random()->id,
            'membership_level_id' => fn () => $levels->random()->id,
        ]);

        $demoMember = $members->first();
        MembershipTransaction::create([
            'member_id' => $demoMember->id,
            'membership_level_id' => $demoMember->membership_level_id,
            'type' => TransactionType::Application,
            'status' => TransactionStatus::Pending,
            'amount_due' => $demoMember->level->fee,
            'starts_on' => now()->toDateString(),
            'expires_on' => now()->addMonths($demoMember->level->validity_months)->toDateString(),
        ]);
    }
}
