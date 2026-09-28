<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Event Request - UniCalendar</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-100 text-slate-900">
    <div class="mx-auto max-w-3xl px-4 py-10">
        <div class="rounded-3xl bg-white p-8 shadow-xl border border-slate-200">
            <div class="mb-6">
                <h1 class="text-3xl font-semibold">Submit Event Request</h1>
                <p class="mt-2 text-slate-500">Upload the required digital documents for your event request.</p>
            </div>

            <?php if(session('success')): ?>
                <div class="mb-6 rounded-3xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-700">
                    <?php echo e(session('success')); ?>

                </div>
            <?php endif; ?>

            <?php if($errors->any()): ?>
                <div class="mb-6 rounded-3xl border border-rose-200 bg-rose-50 p-4 text-rose-700">
                    <ul class="list-disc list-inside text-sm">
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($error); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form action="<?php echo e(route('event-requests.store')); ?>" method="POST" enctype="multipart/form-data" class="space-y-6">
                <?php echo csrf_field(); ?>

                <div class="grid gap-4 md:grid-cols-2">
                    <label class="block">
                        <span class="text-sm font-medium text-slate-700">Full Name</span>
                        <input type="text" name="name" value="<?php echo e(old('name', auth()->user()->name ?? '')); ?>" <?php echo e(auth()->check() ? 'readonly' : ''); ?> required class="mt-2 w-full rounded-2xl border border-slate-300 bg-slate-50 px-4 py-3 outline-none focus:ring-2 focus:ring-indigo-500" />
                    </label>
                    <label class="block">
                        <span class="text-sm font-medium text-slate-700">Email</span>
                        <input type="email" name="email" value="<?php echo e(old('email', auth()->user()->email ?? '')); ?>" <?php echo e(auth()->check() ? 'readonly' : ''); ?> required class="mt-2 w-full rounded-2xl border border-slate-300 bg-slate-50 px-4 py-3 outline-none focus:ring-2 focus:ring-indigo-500" />
                    </label>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <label class="block">
                        <span class="text-sm font-medium text-slate-700">Event Title</span>
                        <input type="text" name="title" value="<?php echo e(old('title')); ?>" required class="mt-2 w-full rounded-2xl border border-slate-300 bg-slate-50 px-4 py-3 outline-none focus:ring-2 focus:ring-indigo-500" />
                    </label>
                    <label class="block">
                        <span class="text-sm font-medium text-slate-700">Venue Name</span>
                        <input type="text" name="venue_name" value="<?php echo e(old('venue_name')); ?>" required class="mt-2 w-full rounded-2xl border border-slate-300 bg-slate-50 px-4 py-3 outline-none focus:ring-2 focus:ring-indigo-500" />
                    </label>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <label class="block">
                        <span class="text-sm font-medium text-slate-700">Start Date & Time</span>
                        <input type="datetime-local" name="start_datetime" value="<?php echo e(old('start_datetime')); ?>" required class="mt-2 w-full rounded-2xl border border-slate-300 bg-slate-50 px-4 py-3 outline-none focus:ring-2 focus:ring-indigo-500" />
                    </label>
                    <label class="block">
                        <span class="text-sm font-medium text-slate-700">End Date & Time</span>
                        <input type="datetime-local" name="end_datetime" value="<?php echo e(old('end_datetime')); ?>" required class="mt-2 w-full rounded-2xl border border-slate-300 bg-slate-50 px-4 py-3 outline-none focus:ring-2 focus:ring-indigo-500" />
                    </label>
                </div>

                <label class="block">
                    <span class="text-sm font-medium text-slate-700">Description</span>
                    <textarea name="description" rows="4" class="mt-2 w-full rounded-2xl border border-slate-300 bg-slate-50 px-4 py-3 outline-none focus:ring-2 focus:ring-indigo-500"><?php echo e(old('description')); ?></textarea>
                </label>

                <label class="block">
                    <span class="text-sm font-medium text-slate-700">Required Digital Documents</span>
                    <p class="text-xs text-slate-500">Upload at least one file. Allowed: pdf, jpg, jpeg, png, doc, docx.</p>
                    <input type="file" name="digital_documents[]" multiple required class="mt-3 w-full text-sm text-slate-700" />
                </label>

                <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-indigo-600 px-6 py-3 text-sm font-semibold text-white hover:bg-indigo-700 transition">Submit Request</button>
            </form>
        </div>
    </div>
</body>
</html>
<?php /**PATH C:\capstone system\PSUniCalendarWeb\resources\views\event-requests\create.blade.php ENDPATH**/ ?>