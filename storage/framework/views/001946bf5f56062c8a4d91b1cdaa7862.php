<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Venues - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css" rel="stylesheet">
</head>
<body class="bg-slate-100 min-h-screen">
    <div class="mx-auto max-w-6xl p-6">
        <main>
            <?php echo $__env->make('partials.page-header', [
                'title' => 'Manage Venues',
                'subtitle' => 'View active venues and availability.',
            ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

            <nav class="bg-white rounded-xl p-3 shadow-sm border border-gray-200 mb-6">
                <div class="max-w-6xl mx-auto flex justify-between items-center">
                    <div class="flex gap-2 items-center">
                        <a href="<?php echo e(route('admin.calendar')); ?>" class="px-4 py-2 rounded text-sm bg-slate-100 text-slate-800">Calendar</a>
                        <a href="<?php echo e(route('admin.dashboard')); ?>#request-form" class="px-4 py-2 rounded text-sm bg-slate-100 text-slate-800">Request event</a>
                        <a href="<?php echo e(route('admin.venues')); ?>" class="px-4 py-2 rounded text-sm bg-indigo-600 text-white">View Venue Availability</a>
                        <a href="<?php echo e(route('admin.dashboard')); ?>#request-status" class="px-4 py-2 rounded text-sm bg-slate-100 text-slate-800">Request Status</a>
                    </div>
                    <form method="POST" action="<?php echo e(route('admin.logout')); ?>">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="px-3 py-2 rounded bg-rose-500 text-white text-sm">Logout</button>
                    </form>
                </div>
            </nav>

            <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-6">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-gray-200 text-xs text-gray-400 uppercase">
                                <th class="py-3 px-4">Venue Name</th>
                                <th class="py-3 px-4">Approved Events</th>
                                <th class="py-3 px-4">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            <?php $__empty_1 = true; $__currentLoopData = $venues; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $venue): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td class="py-3 px-4 font-semibold text-gray-800"><?php echo e($venue->venue_name); ?></td>
                                    <td class="py-3 px-4"><?php echo e($venue->events_count); ?></td>
                                    <td class="py-3 px-4">
                                        <a href="#" class="px-3 py-1 bg-indigo-600 hover:bg-indigo-700 text-white rounded text-xs font-bold">View Events</a>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="3" class="py-6 px-4 text-center text-sm text-slate-500">No active venues yet.</td>
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
<?php /**PATH C:\Users\Kaye Fernandez\Herd\capstone10\resources\views/admin/manage-venues.blade.php ENDPATH**/ ?>