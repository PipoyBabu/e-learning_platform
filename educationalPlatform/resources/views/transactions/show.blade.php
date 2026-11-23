@extends('layouts.app')

@section('content')
<div class="flex flex-wrap items-start justify-between gap-4">
    <div>
        <p class="font-mono text-sm text-slate-500">{{ $transaction->reference_no }}</p>
        <h1 class="mt-1 text-3xl font-bold">{{ $transaction->type->value }} · {{ $transaction->member->full_name }}</h1>
        <p class="mt-2 text-slate-600">{{ $transaction->level->name }} membership · created {{ $transaction->created_at->format('M j, Y') }}</p>
    </div>
    <span class="rounded-full bg-sky-100 px-3 py-1.5 text-sm font-semibold text-sky-800">{{ $transaction->status->value }}</span>
</div>

<div class="mt-8 grid gap-6 md:grid-cols-3">
    <div class="rounded-xl bg-white p-6 shadow-sm"><p class="text-sm text-slate-500">Amount due</p><p class="mt-2 text-2xl font-bold">₱{{ number_format($transaction->amount_due, 2) }}</p></div>
    <div class="rounded-xl bg-white p-6 shadow-sm"><p class="text-sm text-slate-500">Remaining balance</p><p class="mt-2 text-2xl font-bold text-amber-600">₱{{ number_format($transaction->remaining_balance, 2) }}</p></div>
    <div class="rounded-xl bg-white p-6 shadow-sm"><p class="text-sm text-slate-500">Membership dates</p><p class="mt-2 font-semibold">{{ $transaction->starts_on?->format('M j, Y') ?: 'Dates assigned after approval' }}{{ $transaction->expires_on ? ' → '.$transaction->expires_on->format('M j, Y') : '' }}</p></div>
</div>

<div class="mt-8 rounded-xl bg-white p-6 shadow-sm"><h2 class="text-xl font-semibold">Membership journey</h2><div class="mt-6 grid gap-4 sm:grid-cols-3"><div class="rounded-lg border p-4 {{ in_array($transaction->status->value, ['Pending', 'Paid', 'Approved']) ? 'border-indigo-300 bg-indigo-50' : 'border-slate-200' }}"><p class="font-semibold">1. Pending</p><p class="mt-1 text-sm text-slate-500">Application or renewal created</p></div><div class="rounded-lg border p-4 {{ in_array($transaction->status->value, ['Paid', 'Approved']) ? 'border-sky-300 bg-sky-50' : 'border-slate-200' }}"><p class="font-semibold">2. Paid</p><p class="mt-1 text-sm text-slate-500">Balance fully settled</p></div><div class="rounded-lg border p-4 {{ $transaction->status->value === 'Approved' ? 'border-emerald-300 bg-emerald-50' : 'border-slate-200' }}"><p class="font-semibold">3. Approved</p><p class="mt-1 text-sm text-slate-500">Membership accepted</p></div></div></div>

@if ($transaction->status->value === 'Pending')
    <div class="mt-8 max-w-xl rounded-xl border border-indigo-200 bg-indigo-50 p-5">
        <div class="flex flex-wrap items-start justify-between gap-3"><div><h2 class="text-lg font-semibold text-slate-900">Make a payment</h2><p class="mt-1 text-sm text-slate-600">Enter a full or partial amount.</p></div><p class="text-sm font-semibold text-indigo-700">Balance: ₱{{ number_format($transaction->remaining_balance, 2) }}</p></div>
        <form method="POST" action="{{ route('transactions.pay', $transaction) }}" class="mt-5 grid max-w-lg grid-cols-1 gap-3 sm:grid-cols-[minmax(0,1fr)_auto] sm:items-end">@csrf<div><label for="payment-amount" class="block text-sm font-semibold text-slate-800">Payment amount <span class="font-normal text-slate-600">(editable)</span></label><div class="mt-1 flex w-full overflow-hidden rounded-lg border-2 border-slate-300 bg-white shadow-sm focus-within:border-indigo-500 focus-within:ring-2 focus-within:ring-indigo-200"><input id="payment-amount" aria-describedby="payment-amount-help" type="number" name="amount" min="0.01" max="{{ $transaction->remaining_balance }}" step="0.01" value="{{ number_format($transaction->remaining_balance, 2, '.', '') }}" class="min-w-0 flex-1 border-0 bg-transparent py-3 pl-3 pr-2 text-right font-semibold text-slate-900 focus:outline-none focus:ring-0"><span class="flex items-center border-l border-slate-200 px-3 font-semibold text-slate-500">₱</span></div><p id="payment-amount-help" class="mt-1 text-xs text-slate-500">Maximum: ₱{{ number_format($transaction->remaining_balance, 2) }}</p></div><button class="inline-flex w-full items-center justify-center rounded-lg bg-indigo-600 px-5 py-3 font-semibold text-white shadow-sm hover:bg-indigo-700 sm:w-auto">Continue to payment</button></form>
        <form method="POST" action="{{ route('transactions.cancel', $transaction) }}" class="mt-4">@csrf @method('PATCH')<button class="inline-flex rounded-lg border border-red-300 bg-white px-4 py-2 text-sm font-semibold text-red-700 hover:bg-red-50" onclick="return confirm('Cancel this transaction?')">Cancel transaction</button></form>
    </div>
@elseif ($transaction->status->value === 'Paid')
    <div class="mt-8 flex"><form method="POST" action="{{ route('transactions.approve', $transaction) }}">@csrf @method('PATCH')<button class="w-full rounded-lg bg-emerald-600 px-5 py-3 font-semibold text-white sm:w-auto">Approve transaction</button></form></div>
@endif

<div class="mt-8 rounded-xl bg-white p-6 shadow-sm"><h2 class="text-xl font-semibold">Payments</h2><div class="mt-4 divide-y">@forelse ($transaction->payments as $payment)<div class="flex flex-wrap justify-between gap-3 py-3"><div><p class="font-semibold">₱{{ number_format($payment->amount, 2) }} · {{ ucfirst($payment->status->value) }}</p><p class="mt-1 text-sm text-slate-500">{{ $payment->method->value }} · {{ $payment->gateway_reference }} · {{ $payment->paid_at?->format('M j, Y g:i A') ?: 'Not paid' }}</p></div></div>@empty<p class="py-3 text-slate-500">No payments recorded yet. Use the payment panel above to open the simulated checkout.</p>@endforelse</div></div>
@endsection
