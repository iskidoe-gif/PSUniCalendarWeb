<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Planning Office Login - UniCalendar</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 min-h-screen flex items-center justify-center px-4">
    <div class="w-full max-w-md bg-white rounded-xl shadow-xl border border-slate-200 p-8">
        <div class="mb-6 text-center">
            <h1 class="text-2xl font-bold text-slate-900">Planning Office</h1>
            <p class="text-sm text-slate-500 mt-2">Sign in to manage university events and venues.</p>
        </div>

        <?php if($errors->any()): ?>
            <div class="mb-4 rounded-lg bg-rose-50 border border-rose-200 p-4 text-sm text-rose-700">
                <?php echo e($errors->first()); ?>

            </div>
        <?php endif; ?>

        <form method="POST" action="<?php echo e(route('planning_office.login.submit')); ?>" class="space-y-5">
            <?php echo csrf_field(); ?>
            <div>
                <label for="email" class="block text-sm font-medium text-slate-700 mb-2">Email</label>
                <input id="email" name="email" type="email" value="<?php echo e(old('email')); ?>" required autofocus class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm text-slate-900 outline-none focus:ring-2 focus:ring-emerald-600" />
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-slate-700 mb-2">Password</label>
                <input id="password" name="password" type="password" required class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm text-slate-900 outline-none focus:ring-2 focus:ring-emerald-600" />
            </div>
            <label class="inline-flex items-center gap-2 text-sm text-slate-500">
                <input type="checkbox" name="remember" class="h-4 w-4 rounded border-slate-300 text-emerald-700 focus:ring-emerald-600" />
                Remember me
            </label>
            <button type="submit" class="w-full rounded-lg bg-emerald-800 px-4 py-3 text-sm font-semibold text-white hover:bg-emerald-900">Sign in</button>
        </form>
    </div>
</body>
</html><?php /**PATH C:\PSUniCalendarWeb\resources\views/auth/planning-office-login.blade.php ENDPATH**/ ?>