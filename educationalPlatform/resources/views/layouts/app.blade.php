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
            <div class="flex flex-wrap gap-4 text-sm font-medium">
                <a href="{{ route('members.index') }}" class="text-slate-600 hover:text-indigo-700">Members</a>
                <a href="{{ route('categories.index') }}" class="text-slate-600 hover:text-indigo-700">Categories</a>
                <a href="{{ route('levels.index') }}" class="text-slate-600 hover:text-indigo-700">Levels</a>
            </div>
        </div>
    </nav>
    <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        @if (session('success')) <div class="mb-6 rounded-lg bg-emerald-50 p-4 text-emerald-800">{{ session('success') }}</div> @endif
        @if (session('error')) <div class="mb-6 rounded-lg bg-red-50 p-4 text-red-800">{{ session('error') }}</div> @endif
        @if ($errors->any())
            <div class="mb-6 rounded-lg bg-red-50 p-4 text-red-800"><ul class="list-disc pl-5">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
        @endif
        @yield('content')
    </main>
</body>
</html>
