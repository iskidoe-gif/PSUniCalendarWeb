<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Venue Management - UniCalendar</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans antialiased">
    <div class="min-h-screen">
        <main class="overflow-y-auto p-8">
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Venue Management</h1>
                <p class="text-sm text-gray-500 mt-1">Review and manage the testing venues available for booking.</p>
            </div>

            <?php echo $__env->make('partials.calendar-navbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <h2 class="text-lg font-bold text-gray-800 mb-4">Add Venue</h2>
                <form method="POST" action="<?php echo e(route('planning_office.venues.store')); ?>" class="flex gap-3 items-end">
                    <?php echo csrf_field(); ?>
                    <div class="flex-1">
                        <label for="venue-name" class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">Venue Name</label>
                        <input id="venue-name" type="text" name="name" required class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="Enter venue name">
                    </div>
                    <button type="submit" class="rounded-xl bg-indigo-600 px-4 py-3 text-sm font-semibold text-white hover:bg-indigo-700">Add Venue</button>
                </form>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-lg font-bold text-gray-800">Active Venues</h2>
                    <span class="rounded-full bg-indigo-100 px-3 py-1 text-xs font-semibold text-indigo-700"><?php echo e($venues->count()); ?> venues</span>
                </div>

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
                                <?php
                                    $venueId = data_get($venue, 'id', $loop->index + 1);
                                    $venueName = data_get($venue, 'name', data_get($venue, 'venue_name'));
                                    $venueDisplayName = data_get($venue, 'venue_name', $venueName);
                                    $eventCount = data_get($venue, 'events_count', 0);
                                ?>
                                <tr>
                                    <td class="py-3 px-4 font-semibold text-gray-800">
                                        <form method="POST" action="<?php echo e(route('planning_office.venues.update', $venueId)); ?>" class="flex gap-2 items-center">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('PUT'); ?>
                                            <input type="text" name="name" value="<?php echo e($venueName); ?>" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                            <button type="submit" class="rounded-lg bg-slate-900 px-3 py-2 text-xs font-semibold text-white hover:bg-slate-700">Save</button>
                                        </form>
                                    </td>
                                    <td class="py-3 px-4"><?php echo e($eventCount); ?></td>
                                    <td class="py-3 px-4">
                                        <div class="flex gap-2">
                                            <a href="<?php echo e(route('planning_office.venues.events', ['venue' => $venueDisplayName])); ?>" class="px-3 py-1 bg-indigo-600 hover:bg-indigo-700 text-white rounded text-xs font-bold">View Events</a>
                                            <form method="POST" action="<?php echo e(route('planning_office.venues.destroy', $venueId)); ?>" onsubmit="return confirm('Delete this venue?');">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                                <button type="submit" class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white rounded text-xs font-bold">Delete</button>
                                            </form>
                                        </div>
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
<?php /**PATH C:\PSUniCalendarWeb\resources\views/superadmin/manage-venues.blade.php ENDPATH**/ ?>