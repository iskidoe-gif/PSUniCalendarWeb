<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Planning Office Login - UniCalendar</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="{{ asset('css/ui-feedback.css') }}" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="{{ asset('css/office-login.css') }}" rel="stylesheet">
</head>
<body class="min-h-screen flex flex-col bg-slate-50 text-slate-800">
    <!-- Banner (PSU portal style, same as the Office login) -->
    <header class="psu-banner relative overflow-hidden">
        <div class="relative mx-auto flex max-w-6xl items-center justify-between gap-4 px-4 py-4 sm:px-8">
            <div class="flex items-center gap-3 sm:gap-4">
                <div class="h-14 w-14 flex-none sm:h-20 sm:w-20">
                    <img src="{{ asset('images/psu-logo.png') }}" alt="Pangasinan State University seal" class="h-full w-full object-contain"
                         onerror="this.replaceWith(document.getElementById('psu-seal-fallback').content.cloneNode(true))" />
                    <template id="psu-seal-fallback">
                        <div class="flex h-full w-full items-center justify-center rounded-full border border-white/60 bg-[#2f3a78]">
                            <span class="psu-serif text-sm font-bold text-[#f2c318] sm:text-lg">PSU</span>
                        </div>
                    </template>
                </div>
                <div>
                    <p class="psu-serif text-base leading-tight text-white sm:text-xl">PANGASINAN STATE UNIVERSITY</p>
                    <p class="mt-0.5 text-[11px] font-medium uppercase tracking-[0.25em] text-[#f7d54a] sm:text-xs">UniCalendar · Venue &amp; Event Scheduling</p>
                </div>
            </div>
            <p class="psu-portal hidden text-3xl md:block lg:text-4xl">PLANNING OFFICE</p>
        </div>
        <div class="psu-stripe h-1"></div>
    </header>

    <main class="flex-1">
        <div class="mx-auto grid max-w-6xl items-center gap-10 px-4 py-10 sm:px-8 md:grid-cols-2 md:py-16">
            <!-- Welcome -->
            <section>
                <p class="psu-portal text-3xl md:hidden">PLANNING OFFICE</p>
                <h1 class="psu-serif mt-2 text-2xl font-bold text-slate-900 md:mt-0 lg:text-3xl">Welcome, Planning Office</h1>
                                <p class="mt-4 max-w-md text-slate-600">
                    Sign in to review venue requests, manage university events and venues, and oversee the calendar of every PSU campus.
                </p>
                <ul class="mt-6 space-y-3 text-sm text-slate-700">
                    @foreach([
                        'Approve or reject event requests from every office',
                        'Resolve schedule conflicts before they happen',
                        'Manage venues and see all approved events on one calendar',
                    ] as $point)
                        <li class="flex items-start gap-3">
                            <span class="mt-0.5 flex h-5 w-5 flex-none items-center justify-center rounded-full bg-[#e6e9f5] text-[11px] font-bold text-[#3b4679]">✓</span>
                            {{ $point }}
                        </li>
                    @endforeach
                </ul>
            </section>

            <!-- Login card -->
            <section class="w-full max-w-md md:justify-self-end">
                <div class="overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-slate-200">
                    <div class="p-6 sm:p-8">
                        <h2 class="text-xl font-semibold text-slate-900">Login to UniCalendar</h2>
                        <p class="mt-1 text-sm text-slate-500">Enter your Planning Office email and password.</p>

                        @if ($errors->any())
                            <p class="mt-5 rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-sm text-rose-700">{{ $errors->first() }}</p>
                        @endif

                        <form method="POST" action="{{ route('planning_office.login.submit') }}" class="mt-6 space-y-4">
                            @csrf

                            <div>
                                <label for="email" class="mb-1.5 block text-sm font-semibold text-slate-700">Email</label>
                                <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                                       class="psu-input w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm transition" />
                            </div>

                            <div>
                                <label for="password" class="mb-1.5 block text-sm font-semibold text-slate-700">Password</label>
                                <input id="password" name="password" type="password" required autocomplete="current-password"
                                       class="psu-input w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm transition" />
                            </div>

                            <label class="inline-flex items-center gap-2 text-sm text-slate-600">
                                <input type="checkbox" name="remember" class="h-4 w-4 rounded border-slate-300 accent-[#4a5690]" />
                                Remember me
                            </label>

                            <div class="flex gap-3 pt-1">
                                <button type="submit" class="flex-1 rounded-lg bg-[#4a5690] px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-[#3b4679] focus:outline-none focus:ring-4 focus:ring-[#4a5690]/30">Login</button>
                            </div>
                        </form>
                    </div>
                </div>
            </section>
        </div>
    </main>

    <footer class="psu-banner">
        <div class="psu-stripe h-1"></div>
        <p class="px-4 py-3 text-center text-xs text-slate-200">© {{ date('Y') }} Pangasinan State University · UniCalendar</p>
    </footer>
    <script src="{{ asset('js/ui-feedback.js') }}"></script>
</body>
</html>
