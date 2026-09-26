<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events for <?php echo e($venue); ?> - UniCalendar</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans antialiased">
    <div class="min-h-screen">
        <main class="overflow-y-auto p-8">
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Events for <?php echo e($venue); ?></h1>
                    <p class="text-sm text-gray-500 mt-1">All approved events scheduled at this venue.</p>
                </div>
                <div>
                    <a href="<?php echo e(route('planning_office.venues')); ?>" class="px-4 py-2 bg-slate-200 rounded text-sm">Back to Venues</a>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-gray-200 text-xs text-gray-400 uppercase">
                                <th class="py-3 px-4">Event Title</th>
                                <th class="py-3 px-4">Requested By</th>
                                <th class="py-3 px-4">Date & Time</th>
                                <th class="py-3 px-4">Status</th>
                                <th class="py-3 px-4">Documents</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            <?php $__empty_1 = true; $__currentLoopData = $events; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $event): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td class="py-3 px-4 font-semibold text-gray-800"><?php echo e($event->title); ?></td>
                                    <td class="py-3 px-4"><?php echo e($event->name); ?><br><span class="text-xs text-slate-500"><?php echo e($event->email); ?></span></td>
                                    <td class="py-3 px-4"><?php echo e(date('M d, Y', strtotime($event->start_datetime))); ?><br><span class="text-xs text-slate-500"><?php echo e(date('g:i A', strtotime($event->start_datetime))); ?> - <?php echo e(date('g:i A', strtotime($event->end_datetime))); ?></span></td>
                                    <td class="py-3 px-4 capitalize"><?php echo e($event->status); ?></td>
                                    <td class="py-3 px-4">
                                        <?php if(!empty($event->digital_documents)): ?>
                                            <div class="space-y-2">
                                                <?php $__currentLoopData = $event->digital_documents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <a href="<?php echo e(asset('storage/' . $doc)); ?>" target="_blank" class="block text-xs text-indigo-600 hover:underline">Document <?php echo e($loop->iteration); ?></a>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-xs text-slate-500">None</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="5" class="py-6 px-4 text-center text-sm text-slate-500">No approved events for this venue.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
<?php /**PATH C:\PSUniCalendarWeb\resources\views/superadmin/venue-events.blade.php ENDPATH**/ ?>