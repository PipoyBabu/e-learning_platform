@extends('layouts.app')

@section('content')
<div class="flex flex-wrap items-start justify-between gap-4">
    <div><p class="font-mono text-sm text-slate-500">{{ $member->membership_number }}</p><h1 class="mt-1 text-3xl font-bold">{{ $member->full_name }}</h1><p class="mt-2 text-slate-600">{{ $member->email }} · {{ $member->phone ?: 'No phone number' }}</p></div>
    <div class="flex w-full flex-col gap-3 sm:w-auto sm:flex-row sm:flex-wrap"><a href="{{ route('members.edit', $member) }}" class="w-full rounded-lg bg-indigo-600 px-4 py-2.5 text-center font-semibold text-white sm:w-auto">Edit</a>@if ($member->is_active)<form method="POST" action="{{ route('members.deactivate', $member) }}" class="w-full sm:w-auto">@csrf @method('PATCH')<button class="w-full rounded-lg border border-red-200 px-4 py-2.5 font-semibold text-red-700 sm:w-auto" onclick="return confirm('Deactivate this member?')">Deactivate</button></form>@else<form method="POST" action="{{ route('members.reactivate', $member) }}" class="w-full sm:w-auto">@csrf @method('PATCH')<button class="w-full rounded-lg border border-emerald-200 px-4 py-2.5 font-semibold text-emerald-700 sm:w-auto">Reactivate</button></form>@endif</div>
</div>

<div class="mt-8 grid gap-6 md:grid-cols-3"><div class="rounded-xl bg-white p-6 shadow-sm"><p class="text-sm text-slate-500">Category</p><p class="mt-2 text-xl font-semibold">{{ $member->category->name }}</p></div><div class="rounded-xl bg-white p-6 shadow-sm"><p class="text-sm text-slate-500">Level</p><p class="mt-2 text-xl font-semibold">{{ $member->level->name }}</p></div><div class="rounded-xl bg-white p-6 shadow-sm"><p class="text-sm text-slate-500">Joined</p><p class="mt-2 text-xl font-semibold">{{ $member->joined_on->format('M j, Y') }}</p></div></div>

<div class="mt-8 rounded-xl bg-white p-6 shadow-sm">
    <div class="flex flex-wrap items-start justify-between gap-3"><div><h2 class="text-xl font-semibold">Member location</h2><p class="mt-1 text-sm text-slate-500">{{ $member->address ?: 'No address saved.' }}</p></div>@if ($member->latitude && $member->longitude)<span class="font-mono text-xs text-slate-500">{{ number_format($member->latitude, 5) }}, {{ number_format($member->longitude, 5) }}</span>@endif</div>
    @if ($member->latitude && $member->longitude && config('services.google_maps.key'))
        <div id="member-map" class="mt-4 h-72 overflow-hidden rounded-xl border border-slate-200"></div>
        <p id="member-weather" class="mt-3 rounded-lg bg-sky-50 p-3 text-sm text-sky-800">Loading weather...</p>
        <script>
            function initMemberDetailMap() {
                const location = { lat: {{ $member->latitude }}, lng: {{ $member->longitude }} };
                const map = new google.maps.Map(document.getElementById('member-map'), { center: location, zoom: 13 });
                new google.maps.Marker({ position: location, map });
                fetch(`{{ route('weather.show') }}?lat=${location.lat}&lon=${location.lng}`, { headers: { Accept: 'application/json' } }).then(response => response.ok ? response.json() : Promise.reject()).then(data => document.getElementById('member-weather').textContent = `${data.place}: ${data.temperature ?? '—'}°C, ${data.description ?? 'Weather unavailable'}, humidity ${data.humidity ?? '—'}%`).catch(() => document.getElementById('member-weather').textContent = 'Weather is currently unavailable.');
            }
            window.initMemberDetailMap = initMemberDetailMap;
        </script>
        <script async src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.key') }}&loading=async&callback=initMemberDetailMap"></script>
    @elseif ($member->latitude && $member->longitude)
        <p class="mt-4 rounded-lg bg-amber-50 p-4 text-sm text-amber-800">Location coordinates are saved. Add <code>GOOGLE_MAPS_KEY</code> to display the map.</p>
    @else
        <p class="mt-4 rounded-lg bg-slate-50 p-4 text-sm text-slate-600">No map location has been selected. <a class="font-semibold text-indigo-700" href="{{ route('members.edit', $member) }}">Add a location</a>.</p>
    @endif
</div>

<div class="mt-8 rounded-xl bg-white p-6 shadow-sm"><div class="flex flex-wrap items-center justify-between gap-4"><h2 class="text-xl font-semibold">Transaction history</h2><a href="{{ route('transactions.create.application') }}" class="w-full rounded-lg border border-indigo-200 px-3 py-2 text-center text-sm font-semibold text-indigo-700 hover:bg-indigo-50 sm:w-auto">New application</a></div><div class="mt-4 divide-y">@forelse ($member->transactions as $transaction)<a href="{{ route('transactions.show', $transaction) }}" class="flex flex-wrap items-center justify-between gap-3 py-4 hover:bg-slate-50"><div><p class="font-mono text-xs text-slate-500">{{ $transaction->reference_no }}</p><p class="font-semibold text-indigo-700">{{ $transaction->type->value }}</p></div><div class="text-right"><span class="rounded-full bg-sky-100 px-2.5 py-1 text-xs font-semibold text-sky-800">{{ $transaction->status->value }}</span><p class="mt-2 text-sm text-slate-600">₱{{ number_format($transaction->amount_due, 2) }}</p></div></a>@empty<p class="py-4 text-slate-500">No transactions yet.</p>@endforelse</div></div>
@endsection
