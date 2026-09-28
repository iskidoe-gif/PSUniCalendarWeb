<?php $__env->startSection('title', 'Settings'); ?>

<?php ($inputClass = 'mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm font-normal focus:outline-none focus:ring-2 focus:ring-emerald-600'); ?>

<?php $__env->startSection('content'); ?>
    <div class="mb-6">
        <p class="text-xs font-bold uppercase tracking-[0.2em] text-emerald-800">System</p>
        <h1 class="mt-1 text-2xl font-bold text-gray-800">Settings</h1>
        <p class="mt-1 text-sm text-gray-500">Manage the Planning Office account.</p>
    </div>

    <div class="grid max-w-5xl gap-6 lg:grid-cols-2">
        <form method="POST" action="<?php echo e(route('planning_office.settings.profile')); ?>" class="space-y-4 rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PATCH'); ?>
            <h2 class="text-lg font-bold text-gray-800">Account details</h2>

            <?php if($errors->profile->any()): ?>
                <div class="rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-sm text-rose-700"><?php echo e($errors->profile->first()); ?></div>
            <?php endif; ?>

            <label class="block text-sm font-medium text-slate-700">Display name
                <input name="name" value="<?php echo e(old('name', $user->name)); ?>" required class="<?php echo e($inputClass); ?>">
            </label>
            <label class="block text-sm font-medium text-slate-700">Email
                <input type="email" name="email" value="<?php echo e(old('email', $user->email)); ?>" required class="<?php echo e($inputClass); ?>">
            </label>
            <label class="block text-sm font-medium text-slate-700">Current password <span class="font-normal text-slate-400">(to confirm changes)</span>
                <input type="password" name="current_password" required class="<?php echo e($inputClass); ?>">
            </label>
            <button type="submit" class="rounded-lg bg-emerald-700 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-800">Save details</button>
        </form>

        <form method="POST" action="<?php echo e(route('planning_office.settings.password')); ?>" class="space-y-4 rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PATCH'); ?>
            <h2 class="text-lg font-bold text-gray-800">Change password</h2>

            <?php if($errors->password->any()): ?>
                <div class="rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-sm text-rose-700"><?php echo e($errors->password->first()); ?></div>
            <?php endif; ?>

            <label class="block text-sm font-medium text-slate-700">Current password
                <input type="password" name="current_password" required class="<?php echo e($inputClass); ?>">
            </label>
            <label class="block text-sm font-medium text-slate-700">New password
                <input type="password" name="new_password" required minlength="8" class="<?php echo e($inputClass); ?>">
            </label>
            <label class="block text-sm font-medium text-slate-700">Confirm new password
                <input type="password" name="new_password_confirmation" required minlength="8" class="<?php echo e($inputClass); ?>">
            </label>
            <button type="submit" class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">Update password</button>
        </form>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.planning-office', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\capstone system\PSUniCalendarWeb\resources\views\superadmin\settings.blade.php ENDPATH**/ ?>