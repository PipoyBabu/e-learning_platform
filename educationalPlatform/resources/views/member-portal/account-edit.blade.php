@extends('layouts.app')

@section('content')
<div class="max-w-2xl">
    <p class="text-sm font-semibold uppercase tracking-wide text-indigo-700">Account settings</p>
    <h1 class="mt-1 text-3xl font-bold">Edit your account</h1>
    <p class="mt-2 text-slate-600">Update your sign-in details. Changing your email will require verification again.</p>

    <form method="POST" action="{{ route('member.account.update') }}" class="mt-8 space-y-6 rounded-xl bg-white p-6 shadow-sm">
        @csrf
        @method('PATCH')
        <div>
            <label for="name" class="block text-sm font-semibold text-slate-700">Name</label>
            <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required class="mt-2 w-full rounded-lg border border-slate-300 px-3 py-2.5 focus:border-indigo-500 focus:ring-indigo-500">
        </div>
        <div>
            <label for="email" class="block text-sm font-semibold text-slate-700">Email address</label>
            <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required class="mt-2 w-full rounded-lg border border-slate-300 px-3 py-2.5 focus:border-indigo-500 focus:ring-indigo-500">
        </div>
        <div class="border-t border-slate-200 pt-6">
            <h2 class="font-semibold">Change password <span class="font-normal text-slate-500">(optional)</span></h2>
            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="password" class="block text-sm font-semibold text-slate-700">New password</label>
                    <input id="password" name="password" type="password" autocomplete="new-password" class="mt-2 w-full rounded-lg border border-slate-300 px-3 py-2.5 focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="password_confirmation" class="block text-sm font-semibold text-slate-700">Confirm password</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" class="mt-2 w-full rounded-lg border border-slate-300 px-3 py-2.5 focus:border-indigo-500 focus:ring-indigo-500">
                </div>
            </div>
            <div class="mt-4">
                <label for="current_password" class="block text-sm font-semibold text-slate-700">Current password</label>
                <input id="current_password" name="current_password" type="password" autocomplete="current-password" class="mt-2 w-full rounded-lg border border-slate-300 px-3 py-2.5 focus:border-indigo-500 focus:ring-indigo-500">
                <p class="mt-1 text-xs text-slate-500">Required only when changing your password or deleting your account.</p>
            </div>
        </div>
        <div class="flex flex-wrap gap-3">
            <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2.5 font-semibold text-white hover:bg-indigo-700">Save changes</button>
            <a href="{{ route('member.portal') }}" class="rounded-lg border border-slate-300 px-4 py-2.5 font-semibold text-slate-700 hover:bg-slate-50">Cancel</a>
        </div>
    </form>

    <section class="mt-8 rounded-xl border border-red-200 bg-red-50 p-6 text-red-900">
        <h2 class="font-semibold">Delete account</h2>
        <p class="mt-2 text-sm">This removes your login account. Membership history and transactions are retained for administrative records.</p>
        <form method="POST" action="{{ route('member.account.destroy') }}" class="mt-4 flex flex-wrap items-end gap-3" onsubmit="return confirm('Delete your account? This cannot be undone.');">
            @csrf
            @method('DELETE')
            <div>
                <label for="delete_current_password" class="block text-sm font-semibold">Current password</label>
                <input id="delete_current_password" name="current_password" type="password" required autocomplete="current-password" class="mt-2 rounded-lg border border-red-300 bg-white px-3 py-2.5 text-slate-900 focus:border-red-500 focus:ring-red-500">
            </div>
            <button type="submit" class="rounded-lg border border-red-300 px-4 py-2.5 font-semibold text-red-700 hover:bg-red-100">Delete my account</button>
        </form>
    </section>
</div>
@endsection
