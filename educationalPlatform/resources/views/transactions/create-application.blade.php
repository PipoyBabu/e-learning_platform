@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-4xl">
    <div class="mb-8"><p class="font-semibold text-sky-600">Learning journey · New start</p><h1 class="mt-1 text-3xl font-bold">Create membership application</h1><p class="mt-2 max-w-2xl text-slate-600">Start a new learning membership by selecting the member and the level they want to join. The selected level fee is saved with this transaction.</p></div>

    <form method="POST" action="{{ route('transactions.store.application') }}" class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
        @csrf
        <div class="flex items-center gap-3 border-b border-slate-200 pb-5"><span class="flex h-9 w-9 items-center justify-center rounded-full bg-indigo-100 font-bold text-indigo-700">1</span><div><h2 class="font-semibold">Choose membership details</h2><p class="text-sm text-slate-500">Select an active member and a learning level.</p></div></div>
        <div class="mt-6 grid gap-6 sm:grid-cols-2">
            <div><label for="application-member" class="block text-sm font-semibold text-slate-800">Member</label><p class="mt-1 text-xs text-slate-500">Who is starting the membership?</p><select id="application-member" name="member_id" class="mt-2 w-full rounded-lg border-2 border-slate-300 bg-white px-3 py-3 text-slate-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200" required><option value="">Choose a member</option>@foreach ($members as $member)<option value="{{ $member->id }}" @selected(old('member_id') == $member->id)>{{ $member->full_name }} · {{ $member->membership_number }}</option>@endforeach</select></div>
            <div><label for="application-level" class="block text-sm font-semibold text-slate-800">Membership level</label><p class="mt-1 text-xs text-slate-500">The fee will be copied to the transaction.</p><select id="application-level" name="membership_level_id" class="mt-2 w-full rounded-lg border-2 border-slate-300 bg-white px-3 py-3 text-slate-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200" required><option value="">Choose a level</option>@foreach ($levels as $level)<option value="{{ $level->id }}" @selected(old('membership_level_id') == $level->id)>{{ $level->name }} · ₱{{ number_format($level->fee, 2) }} / {{ $level->validity_months }} months</option>@endforeach</select></div>
        </div>
        <div class="mt-8 grid gap-4 border-t border-slate-200 pt-6 sm:grid-cols-3"><div class="rounded-lg bg-sky-50 p-4 sm:col-span-2"><p class="font-semibold text-sky-900">What happens next?</p><p class="mt-1 text-sm text-sky-800">The application starts as <strong>Pending</strong>. You can record a full or partial payment from the transaction page using the simulated gateway.</p></div><div class="rounded-lg bg-slate-50 p-4"><p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Status</p><p class="mt-1 font-semibold text-amber-700">Pending</p></div></div>
        <div class="mt-8 flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-between"><a href="{{ route('transactions.index') }}" class="text-center text-sm font-semibold text-slate-600 hover:text-indigo-700 sm:text-left">Cancel and return to transactions</a><button class="w-full rounded-lg bg-indigo-600 px-5 py-3 font-semibold text-white shadow-sm hover:bg-indigo-700 sm:w-auto">Create application</button></div>
    </form>
</div>
@endsection
