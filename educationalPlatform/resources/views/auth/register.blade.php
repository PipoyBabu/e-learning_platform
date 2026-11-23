<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Create account · Axiom Membership</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-900">
    <main class="flex min-h-screen items-center justify-center px-4 py-10">
        <section class="w-full max-w-md">
            <div class="mb-8 text-center">
                <p class="text-sm font-semibold uppercase tracking-[0.18em] text-indigo-700">Axiom Membership</p>
                <h1 class="mt-3 text-3xl font-bold tracking-tight text-slate-950">Create your account</h1>
                <p class="mt-2 text-sm text-slate-600">Use an email address you can access. We’ll send you a verification link.</p>
            </div>

            @if ($errors->any())
                <div class="mb-5 rounded-lg bg-red-50 p-4 text-sm text-red-800">
                    <ul class="list-disc space-y-1 pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register.store') }}" class="space-y-5 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
                @csrf

                <div>
                    <label for="name" class="mb-2 block text-sm font-semibold">Full name</label>
                    <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus class="block w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-indigo-600 focus:ring-2 focus:ring-indigo-100">
                </div>

                <div>
                    <label for="email" class="mb-2 block text-sm font-semibold">Email address</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required class="block w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-indigo-600 focus:ring-2 focus:ring-indigo-100">
                    <p class="mt-1 text-xs text-slate-500">Gmail and other accessible email addresses are supported.</p>
                </div>

                <div>
                    <label for="password" class="mb-2 block text-sm font-semibold">Password</label>
                    <input id="password" name="password" type="password" required minlength="8" class="block w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-indigo-600 focus:ring-2 focus:ring-indigo-100">
                </div>

                <div>
                    <label for="password_confirmation" class="mb-2 block text-sm font-semibold">Confirm password</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required minlength="8" class="block w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-indigo-600 focus:ring-2 focus:ring-indigo-100">
                </div>

                <button type="submit" class="w-full rounded-lg bg-indigo-600 px-4 py-3 text-sm font-semibold text-white hover:bg-indigo-700">Create account</button>
            </form>

            <p class="mt-5 text-center text-sm text-slate-500">Already registered? <a href="{{ route('login') }}" class="font-semibold text-indigo-700 hover:underline">Sign in</a></p>
        </section>
    </main>
</body>
</html>
