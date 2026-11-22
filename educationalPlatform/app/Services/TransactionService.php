<?php

namespace App\Services;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Exceptions\TransactionRuleException;
use App\Models\Member;
use App\Models\MembershipLevel;
use App\Models\MembershipTransaction;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TransactionService
{
    public function createApplication(Member $member, MembershipLevel $level): MembershipTransaction
    {
        return MembershipTransaction::create([
            'member_id' => $member->id,
            'membership_level_id' => $level->id,
            'type' => TransactionType::Application,
            'status' => TransactionStatus::Pending,
            'amount_due' => $level->fee,
        ]);
    }

    public function createRenewal(Member $member): MembershipTransaction
    {
        $member->loadMissing('level');
        $level = $member->level;
        $startsOn = $member->transactions()->latest('expires_on')->value('expires_on');
        $startsOn = $startsOn && ! now()->greaterThan($startsOn) ? $startsOn : now()->toDateString();

        return MembershipTransaction::create([
            'member_id' => $member->id,
            'membership_level_id' => $level->id,
            'type' => TransactionType::Renewal,
            'status' => TransactionStatus::Pending,
            'amount_due' => $level->fee,
            'starts_on' => $startsOn,
            'expires_on' => now()->parse($startsOn)->addMonths($level->validity_months)->toDateString(),
        ]);
    }

    public function recordPayment(
        MembershipTransaction $transaction,
        float $amount,
        PaymentMethod|string $method,
        PaymentStatus $status = PaymentStatus::Succeeded,
        ?string $gatewayReference = null,
    ): Payment {
        return DB::transaction(function () use ($transaction, $amount, $method, $status, $gatewayReference): Payment {
            $transaction->refresh();
            $amount = round($amount, 2);

            if ($amount <= 0) {
                throw new TransactionRuleException('Payment amount must be greater than zero.');
            }

            if (in_array($transaction->status, [TransactionStatus::Cancelled, TransactionStatus::Paid, TransactionStatus::Approved], true)) {
                throw new TransactionRuleException('This transaction is not accepting payments.');
            }

            if ($amount > $transaction->remaining_balance) {
                throw new TransactionRuleException('Payment amount cannot exceed the remaining balance.');
            }

            $payment = $transaction->payments()->create([
                'amount' => $amount,
                'status' => $status,
                'gateway_reference' => $gatewayReference ?? $this->gatewayReference(),
                'method' => $method instanceof PaymentMethod ? $method : PaymentMethod::from($method),
                'paid_at' => $status === PaymentStatus::Succeeded ? now() : null,
            ]);

            if ($status === PaymentStatus::Succeeded && $transaction->fresh()->remaining_balance <= 0) {
                $transaction->update(['status' => TransactionStatus::Paid]);
            }

            return $payment;
        });
    }

    public function approve(MembershipTransaction $transaction): MembershipTransaction
    {
        $transaction->approve();

        return $transaction->fresh();
    }

    public function cancel(MembershipTransaction $transaction): MembershipTransaction
    {
        $transaction->cancel();

        return $transaction->fresh();
    }

    private function gatewayReference(): string
    {
        do {
            $reference = 'SIM-'.Str::upper(Str::random(6));
        } while (Payment::where('gateway_reference', $reference)->exists());

        return $reference;
    }
}
