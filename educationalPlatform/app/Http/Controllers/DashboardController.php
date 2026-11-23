<?php

namespace App\Http\Controllers;

use App\Enums\PaymentStatus;
use App\Enums\TransactionStatus;
use App\Models\Member;
use App\Models\MembershipTransaction;
use App\Models\Payment;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $start = now()->startOfMonth()->subMonths(5);
        $months = collect(range(0, 5))->map(fn (int $offset): string => now()->startOfMonth()->addMonths($offset - 5)->format('Y-m'));
        $transactionCounts = MembershipTransaction::where('created_at', '>=', $start)->get(['created_at'])->groupBy(fn (MembershipTransaction $transaction): string => $transaction->created_at->format('Y-m'))->map->count();
        $paymentTotals = Payment::where('status', PaymentStatus::Succeeded)->where('paid_at', '>=', $start)->get(['paid_at', 'amount'])->groupBy(fn (Payment $payment): string => $payment->paid_at->format('Y-m'))->map(fn ($payments): float => (float) $payments->sum('amount'));

        return view('dashboard', [
            'totalMembers' => Member::count(),
            'activeMembers' => Member::where('is_active', true)->count(),
            'pendingTransactions' => MembershipTransaction::where('status', TransactionStatus::Pending)->count(),
            'totalCollected' => Payment::where('status', PaymentStatus::Succeeded)->sum('amount'),
            'recentTransactions' => MembershipTransaction::with(['member', 'level'])->latest()->limit(8)->get(),
            'transactionTrend' => $months->map(fn (string $month): array => ['label' => now()->createFromFormat('Y-m', $month)->format('M'), 'value' => $transactionCounts->get($month, 0)])->all(),
            'paymentTrend' => $months->map(fn (string $month): array => ['label' => now()->createFromFormat('Y-m', $month)->format('M'), 'value' => $paymentTotals->get($month, 0)])->all(),
        ]);
    }
}
