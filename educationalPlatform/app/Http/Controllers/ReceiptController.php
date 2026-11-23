<?php

namespace App\Http\Controllers;

use App\Enums\PaymentStatus;
use App\Enums\TransactionStatus;
use App\Models\MembershipTransaction;
use Illuminate\View\View;

class ReceiptController extends Controller
{
    public function show(MembershipTransaction $transaction): View
    {
        abort_unless(auth()->user()->isAdmin() || auth()->user()->ownsTransaction($transaction), 403, 'You may only view your own receipts.');
        abort_unless(in_array($transaction->status, [TransactionStatus::Paid, TransactionStatus::Approved], true), 403, 'A receipt is available only after full payment.');

        $transaction->load(['member.category', 'member.level', 'level', 'payments']);
        $paidAmount = $transaction->payments->where('status', PaymentStatus::Succeeded)->sum('amount');

        return view('receipts.show', [
            'transaction' => $transaction,
            'paidAmount' => (float) $paidAmount,
        ]);
    }
}
