<?php

use App\Enums\PaymentMethod;
use App\Enums\TransactionStatus;
use App\Models\Member;
use App\Models\MembershipCategory;
use App\Models\MembershipLevel;
use App\Models\User;
use App\Services\TransactionService;

beforeEach(function (): void {
    $this->actingAs(User::factory()->create());
    $category = MembershipCategory::factory()->create();
    $level = MembershipLevel::factory()->create(['fee' => 1000]);
    $member = Member::factory()->create([
        'membership_category_id' => $category->id,
        'membership_level_id' => $level->id,
    ]);
    $this->transaction = app(TransactionService::class)->createApplication($member, $level);
});

it('shows a printable receipt only after full payment', function (): void {
    app(TransactionService::class)->recordPayment($this->transaction, 1000, PaymentMethod::GCash);

    $response = $this->get(route('transactions.receipt', $this->transaction));

    $response->assertOk()
        ->assertSee('Payment receipt')
        ->assertSee($this->transaction->fresh()->reference_no)
        ->assertSee('Print receipt');
});

it('blocks receipts for pending transactions', function (): void {
    $this->get(route('transactions.receipt', $this->transaction))
        ->assertForbidden();

    expect($this->transaction->fresh()->status)->toBe(TransactionStatus::Pending);
});
