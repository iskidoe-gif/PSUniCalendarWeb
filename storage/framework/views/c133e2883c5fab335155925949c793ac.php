<?php $__env->startSection('title', $office->name); ?>

<?php
    $statusBadge = [
        'approved' => 'bg-emerald-100 text-emerald-800',
        'pending' => 'bg-amber-100 text-amber-800',
        'conflict' => 'bg-rose-100 text-rose-800',
        'rejected' => 'bg-slate-200 text-slate-700',
        'cancelled' => 'bg-slate-100 text-slate-500',
    ];
?>

<?php $__env->startSection('content'); ?>
    <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.2em] text-emerald-800"><?php echo e($office->campus ?? 'Unassigned campus'); ?></p>
            <h1 class="mt-1 text-2xl font-bold text-gray-800"><?php echo e($office->name); ?></h1>
            <p class="mt-1 text-sm text-gray-500">
                <?php echo e($office->email); ?>

                <?php if($office->contact_person): ?> · <?php echo e($office->contact_person); ?> <?php endif; ?>
                <?php if($office->contact_number): ?> · <?php echo e($office->contact_number); ?> <?php endif; ?>
            </p>
        </div>

        <form method="POST" action="<?php echo e(route('planning_office.offices.campus', $office)); ?>" class="flex items-end gap-2">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PATCH'); ?>
            <label class="text-xs font-semibold uppercase tracking-wide text-slate-500">Campus
                <select name="campus" class="mt-1 block rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-normal normal-case tracking-normal text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-600">
                    <?php if (! ($office->campus)): ?>
                        <option value="" selected disabled>Select campus</option>
                    <?php endif; ?>
                    <?php $__currentLoopData = \App\Models\User::CAMPUSES; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $campus): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($campus); ?>" <?php echo e($office->campus === $campus ? 'selected' : ''); ?>><?php echo e($campus); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </label>
            <button type="submit" class="rounded-lg bg-emerald-700 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-800">Save</button>
        </form>
    </div>

    <?php if($errors->any()): ?>
        <div class="mb-6 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700"><?php echo e($errors->first()); ?></div>
    <?php endif; ?>

    <div class="mb-8 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-amber-700">Awaiting review</p>
            <p class="mt-3 text-3xl font-bold text-amber-900"><?php echo e($statusCounts['pending']); ?></p>
        </div>
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-emerald-700">Approved</p>
            <p class="mt-3 text-3xl font-bold text-emerald-900"><?php echo e($statusCounts['approved']); ?></p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-slate-100 p-4 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-700">Rejected</p>
            <p class="mt-3 text-3xl font-bold text-slate-900"><?php echo e($statusCounts['rejected']); ?></p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Cancelled</p>
            <p class="mt-3 text-3xl font-bold text-slate-700"><?php echo e($statusCounts['cancelled']); ?></p>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-[minmax(0,2fr)_minmax(0,1fr)]">
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-lg font-bold text-gray-800">Event requests</h2>
                <?php if($statusCounts['pending']): ?>
                    <a href="<?php echo e(route('planning_office.pending')); ?>" class="text-sm font-semibold text-emerald-700 hover:underline">Review pending →</a>
                <?php endif; ?>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full border-collapse text-left">
                    <thead>
                        <tr class="border-b border-gray-200 text-xs uppercase text-gray-400">
                            <th class="px-3 py-3">Event</th>
                            <th class="px-3 py-3">Venue</th>
                            <th class="px-3 py-3">Schedule</th>
                            <th class="px-3 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        <?php $__empty_1 = true; $__currentLoopData = $requests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $eventRequest): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td class="px-3 py-3">
                                    <p class="font-semibold text-gray-800"><?php echo e($eventRequest->title); ?></p>
                                    <p class="text-xs text-slate-500">Submitted <?php echo e($eventRequest->created_at?->format('M j, Y')); ?></p>
                                </td>
                                <td class="px-3 py-3"><?php echo e($eventRequest->venue_name); ?><br><span class="text-xs text-slate-500"><?php echo e($eventRequest->campus ?? '—'); ?></span></td>
                                <td class="px-3 py-3"><?php echo e($eventRequest->start_datetime?->format('M j, Y')); ?><br><span class="text-xs text-slate-500"><?php echo e($eventRequest->start_datetime?->format('g:i A')); ?> – <?php echo e($eventRequest->end_datetime?->format('g:i A')); ?></span></td>
                                <td class="px-3 py-3"><span class="rounded-full px-2.5 py-1 text-xs font-semibold capitalize <?php echo e($statusBadge[$eventRequest->status] ?? 'bg-slate-100 text-slate-600'); ?>"><?php echo e($eventRequest->status); ?></span></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="4" class="px-3 py-8 text-center text-sm text-slate-500">This office hasn't submitted any requests yet.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <h2 class="mb-4 text-lg font-bold text-gray-800">Recent activity</h2>
            <ul class="space-y-3">
                <?php $__empty_1 = true; $__currentLoopData = $recentActivity; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <li class="border-l-2 border-emerald-200 pl-3">
                        <p class="text-sm text-slate-700"><?php echo e($activity->description); ?></p>
                        <p class="text-xs text-slate-400"><?php echo e($activity->created_at?->diffForHumans()); ?></p>
                    </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <li class="text-sm text-slate-500">No activity recorded.</li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.planning-office', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\PSUniCalendarWeb\resources\views/superadmin/office.blade.php ENDPATH**/ ?>