<?php $__env->startSection('title', 'Venue Management'); ?>
<?php $__env->startSection('subheading', 'Review and manage the venues available for booking.'); ?>

<?php $__env->startSection('content'); ?>
        <?php if($errors->any()): ?>
            <div class="od-alert od-alert--error"><?php echo e($errors->first()); ?></div>
        <?php endif; ?>

        <section class="od-card">
            <div class="od-card__head">
                <h2 class="od-card__title">Add Venue</h2>
            </div>
            <form method="POST" action="<?php echo e(route('planning_office.venues.store')); ?>" class="po-inline-form">
                <?php echo csrf_field(); ?>
                <label class="od-field" for="venue-name">
                    <span class="od-label">Venue Name</span>
                    <input id="venue-name" type="text" name="name" required class="od-input" placeholder="Enter venue name">
                </label>
                <button type="submit" class="od-btn od-btn--primary">Add Venue</button>
            </form>
        </section>

        <section class="od-card">
            <div class="od-card__head">
                <h2 class="od-card__title">Active Venues</h2>
                <span class="od-pill"><?php echo e($venues->count()); ?> venues</span>
            </div>

            <div class="od-table-wrap">
                <table class="od-table">
                    <thead>
                        <tr>
                            <th>Venue Name</th>
                            <th>Approved Events</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $venues; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $venue): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php
                                $venueId = data_get($venue, 'id', $loop->index + 1);
                                $venueName = data_get($venue, 'name', data_get($venue, 'venue_name'));
                                $venueDisplayName = data_get($venue, 'venue_name', $venueName);
                                $eventCount = data_get($venue, 'events_count', 0);
                            ?>
                            <tr>
                                <td class="od-table__title">
                                    <form method="POST" action="<?php echo e(route('planning_office.venues.update', $venueId)); ?>" class="po-inline-form" style="flex-wrap:nowrap">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('PUT'); ?>
                                        <input type="text" name="name" value="<?php echo e($venueName); ?>" aria-label="Venue name" class="od-input od-input--sm">
                                        <button type="submit" class="od-btn od-btn--ghost od-btn--sm">Save</button>
                                    </form>
                                </td>
                                <td><?php echo e($eventCount); ?></td>
                                <td>
                                    <div class="od-table__actions">
                                        <a href="<?php echo e(route('planning_office.venues.events', ['venue' => $venueDisplayName])); ?>" class="od-btn od-btn--primary od-btn--sm">View Events</a>
                                        <form method="POST" action="<?php echo e(route('planning_office.venues.destroy', $venueId)); ?>" onsubmit="return confirm('Delete this venue?');">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="od-btn od-btn--danger od-btn--sm">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="3" class="od-table__empty">No active venues yet.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.planning-office', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\PSUniCalendarWeb\resources\views/superadmin/manage-venues.blade.php ENDPATH**/ ?>