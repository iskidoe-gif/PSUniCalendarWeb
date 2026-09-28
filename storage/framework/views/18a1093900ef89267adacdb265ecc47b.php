<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Office Login - UniCalendar</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;700&family=Archivo+Black&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="<?php echo e(asset('css/office-login.css')); ?>" rel="stylesheet">
</head>
<body class="min-h-screen flex flex-col bg-slate-50 text-slate-800">
    <!-- Banner (PSU portal style) -->
    <header class="psu-banner relative overflow-hidden">
        <svg class="pointer-events-none absolute inset-0 h-full w-full opacity-40" viewBox="0 0 1200 200" preserveAspectRatio="none" aria-hidden="true">
            <g fill="none" stroke="#8d98d4" stroke-width="0.8">
                <?php for($i = 0; $i < 18; $i++): ?>
                    <path d="M0 <?php echo e(150 - $i * 4); ?> C 250 <?php echo e(40 + $i * 6); ?>, 450 <?php echo e(220 - $i * 5); ?>, 700 <?php echo e(90 + $i * 3); ?> S 1050 <?php echo e(10 + $i * 7); ?>, 1200 <?php echo e(120 - $i * 2); ?>" />
                <?php endfor; ?>
            </g>
        </svg>

        <div class="relative mx-auto flex max-w-6xl items-center justify-between gap-4 px-4 py-4 sm:px-8">
            <div class="flex items-center gap-3 sm:gap-4">
                <div class="h-14 w-14 flex-none sm:h-20 sm:w-20">
                    <img src="<?php echo e(asset('images/psu-logo.png')); ?>" alt="Pangasinan State University seal" class="h-full w-full object-contain drop-shadow-lg"
                         onerror="this.replaceWith(document.getElementById('psu-seal-fallback').content.cloneNode(true))" />
                    <template id="psu-seal-fallback">
                        <div class="flex h-full w-full items-center justify-center rounded-full border-2 border-white bg-[#2f3a78] shadow-lg ring-2 ring-[#f2c318]">
                            <span class="psu-serif text-sm font-bold text-[#f2c318] sm:text-lg">PSU</span>
                        </div>
                    </template>
                </div>
                <div>
                    <p class="psu-serif text-base leading-tight text-white sm:text-2xl lg:text-3xl">PANGASINAN STATE UNIVERSITY</p>
                    <p class="mt-0.5 text-[11px] font-medium uppercase tracking-[0.25em] text-[#f7d54a] sm:text-xs">UniCalendar · Venue &amp; Event Scheduling</p>
                </div>
            </div>
            <p class="psu-portal hidden text-3xl md:block lg:text-4xl">OFFICE LOGIN</p>
        </div>
        <div class="psu-stripe relative h-1.5 shadow-[0_3px_6px_rgba(0,0,0,.3)]"></div>
    </header>

    <main class="flex-1">
        <div class="mx-auto grid max-w-6xl items-center gap-10 px-4 py-10 sm:px-8 md:grid-cols-2 md:py-16">
            <!-- Welcome -->
            <section>
                <p class="psu-portal text-3xl md:hidden">OFFICE PORTAL</p>
                <h1 class="psu-serif mt-3 text-3xl font-bold text-[#2f3a78] md:mt-0 lg:text-4xl">Welcome, PSU Offices</h1>
                <div class="mt-3 h-1 w-16 rounded-full bg-[#f2c318]"></div>
                <p class="mt-4 max-w-md text-slate-600">
                    Sign in to request campus venues, track the status of your event requests, and view the university calendar of approved events.
                </p>
                <ul class="mt-6 space-y-3 text-sm text-slate-700">
                    <?php $__currentLoopData = [
                        'Request a venue on any PSU campus',
                        'Get notified when the Planning Office reviews your request',
                        'See every approved event on one calendar',
                    ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $point): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li class="flex items-start gap-3">
                            <span class="mt-0.5 flex h-5 w-5 flex-none items-center justify-center rounded-full bg-[#4a5690] text-[11px] font-bold text-[#f7d54a]">✓</span>
                            <?php echo e($point); ?>

                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </section>

            <!-- Login card -->
            <section class="w-full max-w-md md:justify-self-end">
                <div class="overflow-hidden rounded-2xl bg-white shadow-xl ring-1 ring-slate-200">
                    <div class="psu-stripe h-1.5"></div>
                    <div class="p-6 sm:p-8">
                        <h2 class="text-xl font-bold text-[#2f3a78]">Login to UniCalendar</h2>
                        <p class="mt-1 text-sm text-slate-500">Enter your email and password to use the system.</p>

                        <?php if($errors->any()): ?>
                            <p class="mt-5 rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-sm text-rose-700"><?php echo e($errors->first()); ?></p>
                        <?php endif; ?>

                        <form method="POST" action="<?php echo e(route('office.login.submit')); ?>" class="mt-6 space-y-4">
                            <?php echo csrf_field(); ?>

                            <div>
                                <label for="email" class="mb-1.5 block text-sm font-semibold text-slate-700">Email</label>
                                <input id="email" name="email" type="email" value="<?php echo e(old('email')); ?>" required autofocus autocomplete="username"
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
                                <button type="submit" class="flex-1 rounded-lg bg-[#4a5690] px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-[#3b4679] focus:outline-none focus:ring-4 focus:ring-[#f2c318]/50">Login</button>

                            </div>
                        </form>
                    </div>
                </div>
            </section>
        </div>
    </main>

    <footer class="psu-banner">
        <div class="psu-stripe h-1"></div>
        <p class="px-4 py-3 text-center text-xs text-slate-200">© <?php echo e(date('Y')); ?> Pangasinan State University · UniCalendar</p>
    </footer>
</body>
</html>
<?php /**PATH C:\capstone system\PSUniCalendarWeb\resources\views/auth/office-login.blade.php ENDPATH**/ ?>