@extends('layouts.app')

@section('content')
<div class="flex flex-wrap items-end justify-between gap-4">
    <div>
        <p class="text-sm font-semibold uppercase tracking-wide text-indigo-700">Member portal</p>
        <h1 class="mt-1 text-3xl font-bold">My account</h1>
        <p class="mt-2 text-slate-600">View your membership, applications, payments, and receipts in one place.</p>
    </div>
    <span class="text-sm text-slate-500">Signed in as {{ auth()->user()->email }}</span>
</div>

@if (! auth()->user()->hasVerifiedEmail())
    <div class="mt-6 flex flex-wrap items-center justify-between gap-4 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">
        <p><span class="font-semibold">Email not verified.</span> Verify your email to keep your account secure.</p>
        <a href="{{ route('verification.notice') }}" class="font-semibold text-amber-900 underline">Verify email</a>
    </div>
@endif

@if (! $member)
    <div class="mt-8 max-w-2xl rounded-xl border border-amber-200 bg-amber-50 p-6 text-amber-900">
        <h2 class="text-lg font-semibold">Your member profile is not linked yet.</h2>
        <p class="mt-2 text-sm">Ask an administrator to create or update a member record using this account email address.</p>
    </div>
@else
    <div class="mt-8 grid gap-6 lg:grid-cols-3">
        <section class="rounded-xl bg-white p-6 shadow-sm lg:col-span-1">
            <p class="text-sm font-semibold uppercase tracking-wide text-slate-500">Member profile</p>
            <h2 class="mt-3 text-2xl font-bold">{{ $member->full_name }}</h2>
            <p class="mt-1 text-sm text-slate-500">{{ $member->membership_number }}</p>
            <dl class="mt-6 space-y-4 text-sm">
                <div><dt class="text-slate-500">Email</dt><dd class="mt-1 font-medium">{{ $member->email }}</dd></div>
                <div><dt class="text-slate-500">Phone</dt><dd class="mt-1 font-medium">{{ $member->phone ?: 'Not provided' }}</dd></div>
                <div><dt class="text-slate-500">Category</dt><dd class="mt-1 font-medium">{{ $member->category->name }}</dd></div>
                <div><dt class="text-slate-500">Level</dt><dd class="mt-1 font-medium">{{ $member->level->name }}</dd></div>
                <div><dt class="text-slate-500">Status</dt><dd class="mt-1 font-medium {{ $member->is_active ? 'text-emerald-700' : 'text-red-700' }}">{{ $member->is_active ? 'Active' : 'Inactive' }}</dd></div>
            </dl>
        </section>

        <section class="rounded-xl bg-white p-6 shadow-sm lg:col-span-2">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div><p class="text-sm font-semibold uppercase tracking-wide text-slate-500">Membership activity</p><h2 class="mt-1 text-2xl font-bold">My transactions</h2></div>
                <span class="text-sm text-slate-500">Your administrator manages applications.</span>
            </div>
            <p class="mt-6 text-xs text-slate-500 sm:hidden">Swipe horizontally to view all transaction details.</p>
            <div class="mt-2 overflow-x-auto">
                <table class="min-w-full text-left text-sm">
                    <thead class="border-b border-slate-200 text-slate-500"><tr><th class="whitespace-nowrap pb-3 pr-4">Reference</th><th class="whitespace-nowrap pb-3 pr-4">Level</th><th class="whitespace-nowrap pb-3 pr-4">Status</th><th class="whitespace-nowrap pb-3 pr-4">Balance</th><th class="whitespace-nowrap pb-3 text-right">Action</th></tr></thead>
                    <tbody class="divide-y divide-slate-100">
                    @forelse ($member->transactions as $transaction)
                        <tr>
                            <td class="whitespace-nowrap py-4 pr-4 font-mono text-xs">{{ $transaction->reference_no }}</td>
                            <td class="whitespace-nowrap py-4 pr-4">{{ $transaction->level->name }}</td>
                            <td class="whitespace-nowrap py-4 pr-4"><span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $transaction->status->badgeClasses() }}">{{ $transaction->status->value }}</span></td>
                            <td class="whitespace-nowrap py-4 pr-4">₱{{ number_format($transaction->remaining_balance, 2) }}</td>
                            <td class="whitespace-nowrap py-4 text-right"><a href="{{ route('transactions.show', $transaction) }}" class="font-semibold text-indigo-700 hover:underline">View</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-10 text-center"><p class="font-semibold text-slate-700">No membership activity yet.</p><p class="mt-1 text-sm text-slate-500">Your administrator can create an application or renewal for your account.</p></td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
@endif
@endsection
