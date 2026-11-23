<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Receipt {{ $transaction->reference_no }} · Axiom Membership</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @media print {
            @page { margin: 14mm; }
            body { background: white !important; }
            .no-print { display: none !important; }
            .receipt-sheet { box-shadow: none !important; border: 0 !important; max-width: none !important; }
        }
    </style>
</head>
<body class="min-h-screen bg-slate-100 text-slate-900">
    <div class="no-print mx-auto mb-4 flex max-w-3xl items-center justify-between gap-4 rounded-xl border border-slate-200 bg-white p-3 shadow-sm sm:p-4">
        <a href="{{ route('transactions.show', $transaction) }}" class="inline-flex items-center rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">Back to transaction</a>
        <button type="button" onclick="window.print()" class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700">Print receipt</button>
    </div>

    <main class="receipt-sheet mx-auto my-6 max-w-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-10">
        <header class="flex flex-wrap items-start justify-between gap-6 border-b-2 border-indigo-600 pb-6">
            <div><p class="text-sm font-semibold uppercase tracking-[0.2em] text-indigo-600">Axiom Membership</p><h1 class="mt-2 text-3xl font-bold">Payment receipt</h1><p class="mt-2 text-sm text-slate-500">Learning membership record</p></div>
            <div class="text-left sm:text-right"><p class="text-xs uppercase tracking-wide text-slate-500">Reference number</p><p class="mt-1 font-mono text-sm font-semibold">{{ $transaction->reference_no }}</p><span class="mt-3 inline-flex border border-emerald-300 bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-800">{{ $transaction->status->value }}</span></div>
        </header>

        <section class="mt-8 grid gap-6 sm:grid-cols-2">
            <div><h2 class="text-xs font-semibold uppercase tracking-wide text-slate-500">Member</h2><p class="mt-2 text-lg font-semibold">{{ $transaction->member->full_name }}</p><p class="text-sm text-slate-600">{{ $transaction->member->email }}</p><p class="text-sm text-slate-600">{{ $transaction->member->phone ?: 'No phone number' }}</p></div>
            <div><h2 class="text-xs font-semibold uppercase tracking-wide text-slate-500">Membership</h2><p class="mt-2 text-lg font-semibold">{{ $transaction->member->membership_number }}</p><p class="text-sm text-slate-600">{{ $transaction->member->category->name }} · {{ $transaction->level->name }}</p><p class="text-sm text-slate-600">{{ $transaction->type->value }}</p></div>
        </section>

        <section class="mt-8 overflow-hidden border border-slate-200"><div class="grid grid-cols-2 border-b bg-slate-50 px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500"><span>Description</span><span class="text-right">Amount</span></div><div class="grid grid-cols-2 px-4 py-4"><span>{{ $transaction->level->name }} membership fee</span><span class="text-right font-semibold">₱{{ number_format($transaction->amount_due, 2) }}</span></div><div class="grid grid-cols-2 border-t px-4 py-4 text-emerald-700"><span>Successful payments</span><span class="text-right font-semibold">₱{{ number_format($paidAmount, 2) }}</span></div><div class="grid grid-cols-2 border-t bg-indigo-50 px-4 py-4 text-lg font-bold"><span>Balance</span><span class="text-right">₱{{ number_format(max(0, (float) $transaction->amount_due - $paidAmount), 2) }}</span></div></section>

        <section class="mt-8"><h2 class="text-lg font-semibold">Payment details</h2><div class="mt-3 overflow-x-auto"><table class="min-w-full text-left text-sm"><thead class="border-b text-xs uppercase tracking-wide text-slate-500"><tr><th class="py-3 pr-4">Date</th><th class="py-3 pr-4">Method</th><th class="py-3 pr-4">Gateway reference</th><th class="py-3 text-right">Amount</th></tr></thead><tbody class="divide-y">@foreach ($transaction->payments->where('status', \App\Enums\PaymentStatus::Succeeded) as $payment)<tr><td class="py-3 pr-4">{{ $payment->paid_at?->format('M j, Y g:i A') ?: '—' }}</td><td class="py-3 pr-4">{{ $payment->method->value }}</td><td class="py-3 pr-4 font-mono text-xs">{{ $payment->gateway_reference }}</td><td class="py-3 text-right font-semibold">₱{{ number_format($payment->amount, 2) }}</td></tr>@endforeach</tbody></table></div></section>

        <section class="mt-8 grid gap-6 border-t pt-6 text-sm sm:grid-cols-2"><div><p class="text-slate-500">Transaction created</p><p class="mt-1 font-semibold">{{ $transaction->created_at->format('M j, Y g:i A') }}</p></div><div><p class="text-slate-500">Membership validity</p><p class="mt-1 font-semibold">{{ $transaction->starts_on?->format('M j, Y') ?: 'Assigned after approval' }}{{ $transaction->expires_on ? ' → '.$transaction->expires_on->format('M j, Y') : '' }}</p></div></section>
        <footer class="mt-10 border-t pt-5 text-center text-xs text-slate-500">This receipt was generated by Axiom Membership. The payment gateway used for this project is simulated.</footer>
    </main>
</body>
</html>
