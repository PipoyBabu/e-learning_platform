<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Axiom Membership' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-900">
    <nav class="border-b border-slate-200 bg-white">
        <div class="mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-4 px-4 py-4 sm:px-6 lg:px-8">
            <a href="{{ route('members.index') }}" class="text-xl font-bold text-indigo-700">Axiom Membership</a>
            <div class="flex flex-wrap items-center gap-4 text-sm font-medium">
                <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'text-indigo-700' : 'text-slate-600' }} rounded-md px-2 py-1 hover:bg-indigo-50 hover:text-indigo-700">Dashboard</a>
                <a href="{{ route('members.index') }}" class="{{ request()->routeIs('members.*') ? 'text-indigo-700' : 'text-slate-600' }} rounded-md px-2 py-1 hover:bg-indigo-50 hover:text-indigo-700">Members</a>
                <a href="{{ route('categories.index') }}" class="{{ request()->routeIs('categories.*') ? 'text-indigo-700' : 'text-slate-600' }} rounded-md px-2 py-1 hover:bg-indigo-50 hover:text-indigo-700">Categories</a>
                <a href="{{ route('levels.index') }}" class="{{ request()->routeIs('levels.*') ? 'text-indigo-700' : 'text-slate-600' }} rounded-md px-2 py-1 hover:bg-indigo-50 hover:text-indigo-700">Levels</a>
                <a href="{{ route('transactions.index') }}" class="{{ request()->routeIs('transactions.*') ? 'text-indigo-700' : 'text-slate-600' }} rounded-md px-2 py-1 hover:bg-indigo-50 hover:text-indigo-700">Transactions</a>
                <span class="hidden h-5 w-px bg-slate-200 sm:block"></span>
                <span class="text-slate-500">{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="rounded-md px-2 py-1 text-red-600 hover:bg-red-50">Log out</button>
                </form>
            </div>
        </div>
    </nav>
    <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        @if (session('success')) <div data-animate="flash" class="mb-6 rounded-lg bg-emerald-50 p-4 text-emerald-800">{{ session('success') }}</div> @endif
        @if (session('error')) <div data-animate="flash" class="mb-6 rounded-lg bg-red-50 p-4 text-red-800">{{ session('error') }}</div> @endif
        @if ($errors->any())
            <div class="mb-6 rounded-lg bg-red-50 p-4 text-red-800"><ul class="list-disc pl-5">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
        @endif
        @yield('content')
    </main>
</body>
</html>
