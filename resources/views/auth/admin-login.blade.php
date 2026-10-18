<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - UniCalendar</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="{{ asset('css/ui-feedback.css') }}" rel="stylesheet">
</head>
<body class="bg-slate-100 min-h-screen flex items-center justify-center px-4">
    <div class="w-full max-w-md bg-white rounded-lg shadow-sm border border-slate-200 p-8">
        <div class="mb-6 text-center">
            <h1 class="text-2xl font-bold text-slate-900">Admin Login</h1>
            <p class="text-sm text-slate-500 mt-2">Sign in to access the admin portal.</p>
        </div>

        @if ($errors->any())
            <div class="mb-4 rounded-lg bg-rose-50 border border-rose-200 p-4 text-sm text-rose-700">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.login.submit') }}" class="space-y-5">
            @csrf

            <div>
                <label for="email" class="block text-sm font-medium text-slate-700 mb-2">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm text-slate-900 outline-none focus:ring-2 focus:ring-[#4a5690]/30 focus:border-[#4a5690]" />
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-slate-700 mb-2">Password</label>
                <input id="password" name="password" type="password" required class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm text-slate-900 outline-none focus:ring-2 focus:ring-[#4a5690]/30 focus:border-[#4a5690]" />
            </div>

            <div class="flex items-center justify-between text-sm text-slate-500">
                <label class="inline-flex items-center gap-2">
                    <input type="checkbox" name="remember" class="h-4 w-4 rounded border-slate-300 text-[#4a5690] focus:ring-[#4a5690]/30 focus:border-[#4a5690]" />
                    Remember me
                </label>
                <a href="#" class="text-[#4a5690] hover:text-[#2f3a78]">Need help?</a>
            </div>

            <button type="submit" class="w-full rounded-lg bg-[#4a5690] px-4 py-3 text-sm font-semibold text-white hover:bg-[#3b4679] transition">Sign in</button>
        </form>

        <p class="mt-6 text-center text-sm text-slate-500">Only authorized admins may access this portal.</p>
    </div>
    <script src="{{ asset('js/ui-feedback.js') }}"></script>
</body>
</html>
