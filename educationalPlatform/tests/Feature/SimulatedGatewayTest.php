<?php

use App\Enums\PaymentStatus;
use App\Enums\TransactionStatus;
use App\Models\Member;
use App\Models\MembershipCategory;
use App\Models\MembershipLevel;
use App\Models\Payment;
use App\Models\User;
use App\Services\TransactionService;

beforeEach(function (): void {
    $category = MembershipCategory::factory()->create();
    $level = MembershipLevel::factory()->create(['fee' => 1000]);
    $member = Member::factory()->create([
        'email' => 'payer@example.com',
        'membership_category_id' => $category->id,
        'membership_level_id' => $level->id,
    ]);
    $this->actingAs(User::factory()->create([
        'email' => $member->email,
        'role' => 'member',
    ]));
    $this->transaction = app(TransactionService::class)->createApplication($member, $level);
});

it('opens a simulated checkout and records a successful callback', function (): void {
    $response = $this->post(route('transactions.pay', $this->transaction), ['amount' => 1000]);
    $response->assertRedirect();

    $checkoutUrl = $response->headers->get('Location');
    $checkoutResponse = $this->get($checkoutUrl);
    $checkoutResponse->assertOk()->assertSee('Secure checkout simulation');

    preg_match('/name="gateway_reference" value="([^"]+)"/', $checkoutResponse->getContent(), $matches);
    $gatewayReference = $matches[1];

    $this->post(route('gateway.callback'), [
        'gateway_reference' => $gatewayReference,
        'method' => 'GCash',
        'outcome' => 'success',
    ])->assertRedirect(route('transactions.show', $this->transaction));

    expect($this->transaction->fresh()->status)->toBe(TransactionStatus::Paid)
        ->and(Payment::where('gateway_reference', $gatewayReference)->first()->status)->toBe(PaymentStatus::Succeeded);
});

it('records a failed callback without changing transaction status', function (): void {
    $response = $this->post(route('transactions.pay', $this->transaction), ['amount' => 300]);
    preg_match('/gateway\/checkout\/([^"?]+)/', $response->headers->get('Location'), $matches);
    $gatewayReference = $matches[1];

    $this->post(route('gateway.callback'), [
        'gateway_reference' => $gatewayReference,
        'method' => 'Card',
        'outcome' => 'failed',
    ]);

    expect($this->transaction->fresh()->status)->toBe(TransactionStatus::Pending)
        ->and(Payment::where('gateway_reference', $gatewayReference)->first()->status)->toBe(PaymentStatus::Failed);
});

it('does not double record a repeated callback', function (): void {
    $response = $this->post(route('transactions.pay', $this->transaction), ['amount' => 300]);
    preg_match('/gateway\/checkout\/([^"?]+)/', $response->headers->get('Location'), $matches);
    $gatewayReference = $matches[1];
    $callback = ['gateway_reference' => $gatewayReference, 'method' => 'Cash at counter', 'outcome' => 'success'];

    $this->post(route('gateway.callback'), $callback);
    $this->post(route('gateway.callback'), $callback);

    expect(Payment::where('gateway_reference', $gatewayReference)->count())->toBe(1);
});
