<?php $__env->startSection('title', 'Notifications'); ?>

<?php $__env->startSection('content'); ?>
    <div class="mb-6">
        <p class="text-xs font-bold uppercase tracking-[0.2em] text-emerald-800">System</p>
        <h1 class="mt-1 text-2xl font-bold text-gray-800">Notifications</h1>
        <p class="mt-1 text-sm text-gray-500">Requests that need a decision, events coming up this week, and recent office activity.</p>
    </div>

    <div class="grid gap-6 xl:grid-cols-2">
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-lg font-bold text-gray-800">Needs review</h2>
                <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-800"><?php echo e($needsReview->count()); ?></span>
            </div>
            <ul class="space-y-3">
                <?php $__empty_1 = true; $__currentLoopData = $needsReview; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $eventRequest): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <li class="flex items-start justify-between gap-4 rounded-lg border <?php echo e($eventRequest->status === 'conflict' ? 'border-rose-200 bg-rose-50' : 'border-slate-200 bg-slate-50'); ?> p-4">
                        <div>
                            <p class="font-semibold text-slate-800"><?php echo e($eventRequest->title); ?></p>
                            <p class="mt-0.5 text-xs text-slate-500"><?php echo e($eventRequest->name); ?> · <?php echo e($eventRequest->venue_name); ?> · <?php echo e($eventRequest->start_datetime?->format('M j, g:i A')); ?></p>
                            <?php if($eventRequest->status === 'conflict'): ?>
                                <p class="mt-1 text-xs font-semibold text-rose-700">Schedule conflict detected</p>
                            <?php endif; ?>
                        </div>
                        <span class="shrink-0 text-xs text-slate-400"><?php echo e($eventRequest->created_at?->diffForHumans()); ?></span>
                    </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <li class="rounded-lg border border-dashed border-slate-300 px-4 py-6 text-center text-sm text-slate-500">You're all caught up.</li>
                <?php endif; ?>
            </ul>
            <?php if($needsReview->isNotEmpty()): ?>
                <a href="<?php echo e(route('planning_office.pending')); ?>" class="mt-4 inline-block rounded-lg bg-emerald-700 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-800">Go to review</a>
            <?php endif; ?>
        </div>

        <div class="space-y-6">
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <h2 class="mb-4 text-lg font-bold text-gray-800">Happening in the next 7 days</h2>
                <ul class="space-y-3">
                    <?php $__empty_1 = true; $__currentLoopData = $upcomingSoon; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $event): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <li class="flex items-center justify-between gap-4 text-sm">
                            <div>
                                <p class="font-semibold text-slate-800"><?php echo e($event->title); ?></p>
                                <p class="text-xs text-slate-500"><?php echo e($event->venue_name); ?> · <?php echo e($event->campus ?? 'All Campus'); ?></p>
                            </div>
                            <span class="shrink-0 text-xs font-medium text-slate-600"><?php echo e($event->start_datetime->format('D, M j · g:i A')); ?></span>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <li class="text-sm text-slate-500">No approved events this week.</li>
                    <?php endif; ?>
                </ul>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <h2 class="mb-4 text-lg font-bold text-gray-800">Recent office activity</h2>
                <ul class="space-y-3">
                    <?php $__empty_1 = true; $__currentLoopData = $recentActivity; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <li class="border-l-2 border-emerald-200 pl-3">
                            <p class="text-sm text-slate-700"><span class="font-semibold"><?php echo e($officeNames[$activity->email] ?? $activity->email); ?></span> — <?php echo e($activity->description); ?></p>
                            <p class="text-xs text-slate-400"><?php echo e($activity->created_at?->diffForHumans()); ?></p>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <li class="text-sm text-slate-500">No activity recorded yet.</li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.planning-office', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\capstone system\PSUniCalendarWeb\resources\views\superadmin\notifications.blade.php ENDPATH**/ ?>