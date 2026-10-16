<?php $__env->startSection('title', 'Review Requests'); ?>
<?php $__env->startSection('heading', 'Request review'); ?>
<?php $__env->startSection('subheading', 'Check the schedule, resolve any conflicts, and record the planning decision.'); ?>

<?php $__env->startSection('topbar-actions'); ?>
    <a href="<?php echo e(route('planning_office.dashboard')); ?>" class="od-btn od-btn--ghost od-btn--sm">Back to dashboard</a>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
        <?php if($errors->any()): ?>
            <div class="od-alert od-alert--error"><?php echo e($errors->first()); ?></div>
        <?php endif; ?>

        <?php $__empty_1 = true; $__currentLoopData = $requests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $request): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <article id="request-<?php echo e($request->id); ?>" class="od-card">
                <div class="po-review__head">
                    <div>
                        <div class="od-card__title" style="flex-wrap:wrap">
                            <h2 class="po-review__title"><?php echo e($request->title); ?></h2>
                            <span class="od-badge od-badge--<?php echo e($request->status === 'conflict' ? 'conflict' : 'pending'); ?>"><?php echo e($request->status === 'conflict' ? 'Conflict detected' : 'Awaiting review'); ?></span>
                        </div>
                        <p class="po-review__meta"><?php echo e($request->name); ?> · <?php echo e($request->email); ?> · <?php echo e($request->campus ?? 'Campus not specified'); ?></p>
                    </div>
                    <?php if($request->sdg_number): ?>
                        <span class="od-pill">SDG <?php echo e($request->sdg_number); ?></span>
                    <?php else: ?>
                        <span class="od-pill">Not aligned to SDG</span>
                    <?php endif; ?>
                </div>

                <div class="po-review__body">
                    <div class="po-review__info">
                        <p><strong>Requested schedule:</strong><br><?php echo e($request->start_datetime->format('D, M j, Y · g:i A')); ?> – <?php echo e($request->end_datetime->format('g:i A')); ?></p>
                        <p><strong>Venue:</strong> <?php echo e($request->venue_name); ?></p>
                        <p class="od-muted" style="font-size:13.5px"><?php echo e($request->description ?: 'No event description provided.'); ?></p>
                        <?php if($request->planning_note): ?><p class="od-timeline__note" style="margin:0">Previous note: <?php echo e($request->planning_note); ?></p><?php endif; ?>
                        <?php if($request->digital_documents): ?>
                            <div class="po-review__docs">
                                <?php $__currentLoopData = $request->digital_documents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $document): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <a class="od-link" href="<?php echo e(asset('storage/' . $document)); ?>" target="_blank" rel="noopener">Document <?php echo e($loop->iteration); ?></a>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        <?php endif; ?>
                        <?php if($request->conflicts->isNotEmpty()): ?>
                            <div class="po-conflict">
                                <p><strong style="color:inherit">Overlapping event request(s)</strong></p>
                                <?php $__currentLoopData = $request->conflicts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $conflict): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <p><?php echo e($conflict->title); ?> · <?php echo e($conflict->start_datetime->format('M j, g:i A')); ?> – <?php echo e($conflict->end_datetime->format('g:i A')); ?></p>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        <?php else: ?>
                            <p class="po-review__ok">No venue or time conflicts detected.</p>
                        <?php endif; ?>
                    </div>

                    <form method="POST" action="<?php echo e(route('planning_office.approve', ['id' => $request->id])); ?>" class="po-review__form">
                        <?php echo csrf_field(); ?>
                        <label class="od-field">
                            <span class="od-label">Venue</span>
                            <input name="venue_name" value="<?php echo e($request->venue_name); ?>" required class="od-input od-input--sm">
                        </label>
                        <label class="od-field">
                            <span class="od-label">Campus</span>
                            <select name="campus" required class="od-input od-input--sm">
                                <?php $__currentLoopData = [...\App\Models\User::MAIN_CAMPUSES, 'All Campus']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $campus): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($campus); ?>" <?php echo e($request->campus === $campus ? 'selected' : ''); ?>><?php echo e($campus); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </label>
                        <label class="od-field po-span">
                            <span class="od-label">Who is this event for?</span>
                            <select name="is_university_wide" class="od-input od-input--sm">
                                <option value="0" <?php echo e($request->is_university_wide ? '' : 'selected'); ?>>This campus only — only offices of the selected campus see it</option>
                                <option value="1" <?php echo e($request->is_university_wide ? 'selected' : ''); ?>>All campuses — every campus sees it, wherever it's held</option>
                            </select>
                        </label>
                        <label class="od-field">
                            <span class="od-label">Start</span>
                            <input type="datetime-local" name="start_datetime" value="<?php echo e($request->start_datetime->format('Y-m-d\\TH:i')); ?>" required class="od-input od-input--sm">
                        </label>
                        <label class="od-field">
                            <span class="od-label">End</span>
                            <input type="datetime-local" name="end_datetime" value="<?php echo e($request->end_datetime->format('Y-m-d\\TH:i')); ?>" required class="od-input od-input--sm">
                        </label>
                        <label class="od-field po-span">
                            <span class="od-label">SDG alignment</span>
                            <select name="sdg_number" class="od-input od-input--sm">
                                <option value="">Not aligned to an SDG</option>
                                <?php $__currentLoopData = ['No Poverty', 'Zero Hunger', 'Good Health and Well-being', 'Quality Education', 'Gender Equality', 'Clean Water and Sanitation', 'Affordable and Clean Energy', 'Decent Work and Economic Growth', 'Industry, Innovation and Infrastructure', 'Reduced Inequalities', 'Sustainable Cities and Communities', 'Responsible Consumption and Production', 'Climate Action', 'Life Below Water', 'Life on Land', 'Peace, Justice and Strong Institutions', 'Partnerships for the Goals']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $sdg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($index + 1); ?>" <?php echo e((string) $request->sdg_number === (string) ($index + 1) ? 'selected' : ''); ?>>SDG <?php echo e($index + 1); ?>: <?php echo e($sdg); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </label>
                        <label class="od-field po-span">
                            <span class="od-label">Planning note</span>
                            <textarea name="planning_note" rows="2" class="od-input od-input--sm" placeholder="Optional note for the requester"><?php echo e($request->planning_note); ?></textarea>
                        </label>
                        <div class="po-review__actions po-span">
                            <button type="submit" class="od-btn od-btn--primary od-btn--sm">Save and approve</button>
                            <button formaction="<?php echo e(route('planning_office.return', ['id' => $request->id])); ?>" formmethod="post" formnovalidate type="submit" class="od-btn od-btn--ghost od-btn--sm" title="Uses the planning note as the revision instructions">Return for revision</button>
                            <button formaction="<?php echo e(route('planning_office.reject', ['id' => $request->id])); ?>" formmethod="post" type="submit" class="od-btn od-btn--danger od-btn--sm">Reject request</button>
                        </div>
                    </form>
                </div>
            </article>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="od-card"><p class="od-empty">No requests are awaiting review.</p></div>
        <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.planning-office', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\PSUniCalendarWeb\resources\views/office/pending-approvals.blade.php ENDPATH**/ ?>