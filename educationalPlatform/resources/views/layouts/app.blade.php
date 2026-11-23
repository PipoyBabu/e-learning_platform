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
        <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-4 sm:px-6 lg:px-8">
            <a href="{{ route(auth()->user()->isAdmin() ? 'dashboard' : 'member.portal') }}" class="text-xl font-bold text-indigo-700">Axiom Membership</a>
            <div class="relative">
                <input id="navigation-toggle" type="checkbox" class="peer sr-only">
                <label for="navigation-toggle" class="flex cursor-pointer items-center gap-2 rounded-lg border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 lg:hidden">
                    Menu
                    <span aria-hidden="true" class="text-xs">▼</span>
                </label>
                <div class="absolute right-0 top-full z-30 mt-2 hidden min-w-60 rounded-xl border border-slate-200 bg-white p-3 text-sm font-medium shadow-lg peer-checked:block lg:static lg:mt-0 lg:block lg:min-w-0 lg:border-0 lg:bg-transparent lg:p-0 lg:shadow-none">
                    <div class="flex flex-col gap-1 lg:flex-row lg:items-center lg:gap-4">
                        @if (auth()->user()->isAdmin())
                            <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'text-indigo-700' : 'text-slate-600' }} rounded-md px-2 py-2 hover:bg-indigo-50 hover:text-indigo-700">Dashboard</a>
                            <a href="{{ route('members.index') }}" class="{{ request()->routeIs('members.*') ? 'text-indigo-700' : 'text-slate-600' }} rounded-md px-2 py-2 hover:bg-indigo-50 hover:text-indigo-700">Members</a>
                            <a href="{{ route('categories.index') }}" class="{{ request()->routeIs('categories.*') ? 'text-indigo-700' : 'text-slate-600' }} rounded-md px-2 py-2 hover:bg-indigo-50 hover:text-indigo-700">Categories</a>
                            <a href="{{ route('levels.index') }}" class="{{ request()->routeIs('levels.*') ? 'text-indigo-700' : 'text-slate-600' }} rounded-md px-2 py-2 hover:bg-indigo-50 hover:text-indigo-700">Levels</a>
                            <a href="{{ route('transactions.index') }}" class="{{ request()->routeIs('transactions.*') ? 'text-indigo-700' : 'text-slate-600' }} rounded-md px-2 py-2 hover:bg-indigo-50 hover:text-indigo-700">Transactions</a>
                        @else
                            <a href="{{ route('member.portal') }}" class="{{ request()->routeIs('member.portal') ? 'text-indigo-700' : 'text-slate-600' }} rounded-md px-2 py-2 hover:bg-indigo-50 hover:text-indigo-700">My account</a>
                        @endif
                        <span class="hidden h-5 w-px bg-slate-200 lg:block"></span>
                        <span class="px-2 py-2 text-slate-500 lg:px-0 lg:py-1">{{ auth()->user()->name }}</span>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full rounded-md px-2 py-2 text-left text-red-600 hover:bg-red-50 lg:w-auto lg:py-1">Log out</button>
                        </form>
                    </div>
                </div>
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
