<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign in · Axiom Membership</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-900">
    <main class="flex min-h-screen items-center justify-center px-4 py-10">
        <section class="w-full max-w-md">
            <div class="mb-8 text-center">
                <p class="text-sm font-semibold uppercase tracking-[0.18em] text-indigo-700">Axiom Membership</p>
                <h1 class="mt-3 text-3xl font-bold tracking-tight text-slate-950">Welcome back</h1>
                <p class="mt-2 text-sm text-slate-600">Sign in to manage the learning membership directory.</p>
            </div>

            @if (session('success'))
                <div class="mb-5 rounded-lg bg-emerald-50 p-4 text-sm text-emerald-800">{{ session('success') }}</div>
            @endif

            @if ($errors->any())
                <div class="mb-5 rounded-lg bg-red-50 p-4 text-sm text-red-800">
                    <ul class="list-disc space-y-1 pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login.store') }}" class="space-y-5 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
                @csrf

                <div>
                    <label for="email" class="mb-2 block text-sm font-semibold text-slate-800">Email address</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required autofocus
                        class="block w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-indigo-600 focus:ring-2 focus:ring-indigo-100"
                        placeholder="admin@axiom.test">
                </div>

                <div>
                    <label for="password" class="mb-2 block text-sm font-semibold text-slate-800">Password</label>
                    <input id="password" name="password" type="password" autocomplete="current-password" required
                        class="block w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-indigo-600 focus:ring-2 focus:ring-indigo-100"
                        placeholder="Enter your password">
                </div>

                <label class="flex items-center gap-2 text-sm text-slate-600">
                    <input type="checkbox" name="remember" value="1" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                    Remember me
                </label>

                <button type="submit" class="w-full rounded-lg bg-indigo-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    Sign in
                </button>
            </form>

            <p class="mt-5 text-center text-xs text-slate-500">Demo access is provided through the seeded administrator account.</p>
        </section>
    </main>
</body>
</html>
