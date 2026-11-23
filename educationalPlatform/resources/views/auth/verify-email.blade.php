@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-xl rounded-xl bg-white p-8 text-center shadow-sm">
    <p class="text-sm font-semibold uppercase tracking-wide text-indigo-700">Account security</p>
    <h1 class="mt-2 text-3xl font-bold">Verify your email address</h1>
    <p class="mt-4 text-slate-600">We sent a verification link to <span class="font-semibold">{{ auth()->user()->email }}</span>. Open it to confirm that you own this email address.</p>
    <form method="POST" action="{{ route('verification.send') }}" class="mt-6">@csrf<button class="rounded-lg bg-indigo-600 px-5 py-3 font-semibold text-white hover:bg-indigo-700">Resend verification email</button></form>
    <a href="{{ route('member.portal') }}" class="mt-5 inline-block text-sm font-semibold text-slate-600 hover:underline">Continue to my account</a>
</div>
@endsection
