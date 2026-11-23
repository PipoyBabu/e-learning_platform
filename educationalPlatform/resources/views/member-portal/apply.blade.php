@extends('layouts.app')

@section('content')
<div class="max-w-3xl">
    <a href="{{ route('member.portal') }}" class="text-sm font-semibold text-indigo-700 hover:underline">← Back to my account</a>
    <p class="mt-6 text-sm font-semibold uppercase tracking-wide text-indigo-700">Membership application</p>
    <h1 class="mt-1 text-3xl font-bold">Choose your membership</h1>
    <p class="mt-2 text-slate-600">Complete your profile, choose an offer, and continue to payment after your application is created.</p>
</div>

<form method="POST" action="{{ route('member.application.store') }}" class="mt-8 max-w-3xl space-y-6 rounded-xl bg-white p-6 shadow-sm sm:p-8">
    @csrf
    <div class="grid gap-6 sm:grid-cols-2">
        <div><label for="first-name" class="mb-2 block text-sm font-semibold">First name</label><input id="first-name" name="first_name" value="{{ old('first_name', auth()->user()->name) }}" required class="w-full rounded-lg border-slate-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-200"></div>
        <div><label for="last-name" class="mb-2 block text-sm font-semibold">Last name</label><input id="last-name" name="last_name" value="{{ old('last_name') }}" required class="w-full rounded-lg border-slate-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-200"></div>
    </div>
    <div><label for="application-phone" class="mb-2 block text-sm font-semibold">Phone number <span class="font-normal text-slate-500">(optional)</span></label><input id="application-phone" name="phone" value="{{ old('phone') }}" class="w-full rounded-lg border-slate-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-200"></div>
    <div><label for="application-category" class="mb-2 block text-sm font-semibold">Membership category</label><select id="application-category" name="membership_category_id" required class="w-full rounded-lg border-slate-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-200"><option value="">Choose a category</option>@foreach ($categories as $category)<option value="{{ $category->id }}" @selected(old('membership_category_id') == $category->id)>{{ $category->name }}</option>@endforeach</select></div>
    <fieldset><legend class="text-sm font-semibold">Membership level</legend><div class="mt-3 grid gap-3 sm:grid-cols-2">@foreach ($levels as $level)<label class="flex cursor-pointer gap-3 rounded-lg border border-slate-200 p-4 hover:border-indigo-400 has-[:checked]:border-indigo-500 has-[:checked]:bg-indigo-50"><input type="radio" name="membership_level_id" value="{{ $level->id }}" @checked(old('membership_level_id') == $level->id) required class="mt-1 text-indigo-600 focus:ring-indigo-500"><span><span class="block font-semibold">{{ $level->name }} · ₱{{ number_format($level->fee, 2) }}</span><span class="mt-1 block text-xs text-slate-500">{{ $level->validity_months }} months · {{ $level->benefits ?: 'Membership benefits included.' }}</span></span></label>@endforeach</div></fieldset>
    <div class="rounded-lg bg-sky-50 p-4 text-sm text-sky-800">Your application starts as <strong>Pending</strong>. After submitting, you can pay the selected membership fee through the simulated checkout.</div>
    <button class="w-full rounded-lg bg-indigo-600 px-5 py-3 font-semibold text-white hover:bg-indigo-700 sm:w-auto">Create membership application</button>
</form>
@endsection
