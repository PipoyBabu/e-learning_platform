<?php

use App\Enums\PaymentMethod;
use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Exceptions\TransactionRuleException;
use App\Models\Member;
use App\Models\MembershipCategory;
use App\Models\MembershipLevel;
use App\Services\TransactionService;

beforeEach(function (): void {
    $this->service = new TransactionService;
    $this->category = MembershipCategory::factory()->create();
    $this->level = MembershipLevel::factory()->create(['fee' => 1000, 'validity_months' => 12]);
    $this->member = Member::factory()->create([
        'membership_category_id' => $this->category->id,
        'membership_level_id' => $this->level->id,
    ]);
});

it('creates an application with a fee snapshot and pending status', function (): void {
    $transaction = $this->service->createApplication($this->member, $this->level);

    expect($transaction->type)->toBe(TransactionType::Application)
        ->and($transaction->status)->toBe(TransactionStatus::Pending)
        ->and((float) $transaction->amount_due)->toBe(1000.0)
        ->and($transaction->reference_no)->toStartWith('TXN-');
});

it('blocks a second pending transaction for the same member', function (): void {
    $this->service->createApplication($this->member, $this->level);

    expect(fn () => $this->service->createRenewal($this->member))
        ->toThrow(TransactionRuleException::class, 'This member already has a pending transaction.');
});

it('keeps a partial payment pending and marks a fully paid transaction paid', function (): void {
    $transaction = $this->service->createApplication($this->member, $this->level);

    $this->service->recordPayment($transaction, 400, PaymentMethod::GCash);
    expect($transaction->fresh()->status)->toBe(TransactionStatus::Pending)
        ->and($transaction->fresh()->remaining_balance)->toBe(600.0);

    $this->service->recordPayment($transaction, 600, PaymentMethod::Cash);
    expect($transaction->fresh()->status)->toBe(TransactionStatus::Paid)
        ->and($transaction->fresh()->remaining_balance)->toBe(0.0);
});

it('blocks invalid payment amounts and payments on closed transactions', function (): void {
    $transaction = $this->service->createApplication($this->member, $this->level);

    expect(fn () => $this->service->recordPayment($transaction, 0, PaymentMethod::GCash))
        ->toThrow(TransactionRuleException::class);
    expect(fn () => $this->service->recordPayment($transaction, 1001, PaymentMethod::GCash))
        ->toThrow(TransactionRuleException::class);

    $this->service->cancel($transaction);
    expect(fn () => $this->service->recordPayment($transaction, 100, PaymentMethod::GCash))
        ->toThrow(TransactionRuleException::class);
});

it('only approves fully paid transactions and only cancels pending transactions', function (): void {
    $transaction = $this->service->createApplication($this->member, $this->level);

    expect(fn () => $this->service->approve($transaction))
        ->toThrow(TransactionRuleException::class);

    $this->service->recordPayment($transaction, 1000, PaymentMethod::GCash);
    $this->service->approve($transaction);
    expect($transaction->fresh()->status)->toBe(TransactionStatus::Approved);

    expect(fn () => $this->service->cancel($transaction))
        ->toThrow(TransactionRuleException::class);
});

it('starts renewal at the current expiry when still valid and today when lapsed', function (): void {
    $validExpiry = now()->addMonths(2)->toDateString();
    $this->member->transactions()->create([
        'membership_level_id' => $this->level->id,
        'type' => TransactionType::Application,
        'status' => TransactionStatus::Approved,
        'amount_due' => $this->level->fee,
        'starts_on' => now()->subMonths(10)->toDateString(),
        'expires_on' => $validExpiry,
    ]);

    $renewal = $this->service->createRenewal($this->member);
    expect($renewal->starts_on->toDateString())->toBe($validExpiry)
        ->and($renewal->expires_on->toDateString())->toBe(now()->parse($validExpiry)->addYear()->toDateString());

    $renewal->update(['status' => TransactionStatus::Approved]);
    $this->member->transactions()->update(['expires_on' => now()->subDay()->toDateString()]);
    $lapsedRenewal = $this->service->createRenewal($this->member);
    expect($lapsedRenewal->starts_on->toDateString())->toBe(now()->toDateString());
});
