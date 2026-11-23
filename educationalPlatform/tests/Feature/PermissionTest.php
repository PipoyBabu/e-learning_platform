<?php

use App\Models\Member;
use App\Models\MembershipCategory;
use App\Models\MembershipLevel;
use App\Models\MembershipTransaction;
use App\Models\User;
use App\Services\TransactionService;

function permissionTransaction(string $email = 'member@example.com'): MembershipTransaction
{
    $category = MembershipCategory::factory()->create();
    $level = MembershipLevel::factory()->create(['fee' => 1000]);
    $member = Member::factory()->create([
        'email' => $email,
        'membership_category_id' => $category->id,
        'membership_level_id' => $level->id,
    ]);

    return app(TransactionService::class)->createApplication($member, $level);
}

it('prevents members from opening administrator pages', function (): void {
    $user = User::factory()->create(['role' => 'member', 'email' => 'member@example.com']);

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertForbidden();
});

it('prevents administrators from initiating member payments', function (): void {
    $transaction = permissionTransaction();
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)
        ->post(route('transactions.pay', $transaction), ['amount' => 100])
        ->assertForbidden();
});

it('prevents a member from paying another member transaction', function (): void {
    $transaction = permissionTransaction('other-member@example.com');
    $user = User::factory()->create(['role' => 'member', 'email' => 'member@example.com']);

    $this->actingAs($user)
        ->post(route('transactions.pay', $transaction), ['amount' => 100])
        ->assertForbidden();
});
