<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Event Request - UniCalendar</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="{{ asset('css/ui-feedback.css') }}" rel="stylesheet">
</head>
<body class="min-h-screen bg-slate-100 text-slate-900">
    <div class="mx-auto max-w-3xl px-4 py-10">
        <div class="rounded-lg bg-white p-8 shadow-sm border border-slate-200">
            <div class="mb-6">
                <h1 class="text-3xl font-semibold">Submit Event Request</h1>
                <p class="mt-2 text-slate-500">Upload the required digital documents for your event request.</p>
            </div>

            @include('partials.flash')

            @if($errors->any())
                <div class="mb-6 rounded-lg border border-rose-200 bg-rose-50 p-4 text-rose-700">
                    <ul class="list-disc list-inside text-sm">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('event-requests.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <div class="grid gap-4 md:grid-cols-2">
                    <label class="block">
                        <span class="text-sm font-medium text-slate-700">Full Name</span>
                        <input type="text" name="name" value="{{ old('name', auth()->user()->name ?? '') }}" {{ auth()->check() ? 'readonly' : '' }} required class="mt-2 w-full rounded-lg border border-slate-300 bg-slate-50 px-4 py-3 outline-none focus:ring-2 focus:ring-[#4a5690]/30 focus:border-[#4a5690]" />
                    </label>
                    <label class="block">
                        <span class="text-sm font-medium text-slate-700">Email</span>
                        <input type="email" name="email" value="{{ old('email', auth()->user()->email ?? '') }}" {{ auth()->check() ? 'readonly' : '' }} required class="mt-2 w-full rounded-lg border border-slate-300 bg-slate-50 px-4 py-3 outline-none focus:ring-2 focus:ring-[#4a5690]/30 focus:border-[#4a5690]" />
                    </label>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <label class="block">
                        <span class="text-sm font-medium text-slate-700">Event Title</span>
                        <input type="text" name="title" value="{{ old('title') }}" required class="mt-2 w-full rounded-lg border border-slate-300 bg-slate-50 px-4 py-3 outline-none focus:ring-2 focus:ring-[#4a5690]/30 focus:border-[#4a5690]" />
                    </label>
                    <label class="block">
                        <span class="text-sm font-medium text-slate-700">Venue Name</span>
                        <input type="text" name="venue_name" value="{{ old('venue_name') }}" required class="mt-2 w-full rounded-lg border border-slate-300 bg-slate-50 px-4 py-3 outline-none focus:ring-2 focus:ring-[#4a5690]/30 focus:border-[#4a5690]" />
                    </label>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <label class="block">
                        <span class="text-sm font-medium text-slate-700">Start Date & Time</span>
                        <input type="datetime-local" name="start_datetime" value="{{ old('start_datetime') }}" required class="mt-2 w-full rounded-lg border border-slate-300 bg-slate-50 px-4 py-3 outline-none focus:ring-2 focus:ring-[#4a5690]/30 focus:border-[#4a5690]" />
                    </label>
                    <label class="block">
                        <span class="text-sm font-medium text-slate-700">End Date & Time</span>
                        <input type="datetime-local" name="end_datetime" value="{{ old('end_datetime') }}" required class="mt-2 w-full rounded-lg border border-slate-300 bg-slate-50 px-4 py-3 outline-none focus:ring-2 focus:ring-[#4a5690]/30 focus:border-[#4a5690]" />
                    </label>
                </div>

                <label class="block">
                    <span class="text-sm font-medium text-slate-700">Description</span>
                    <textarea name="description" rows="4" class="mt-2 w-full rounded-lg border border-slate-300 bg-slate-50 px-4 py-3 outline-none focus:ring-2 focus:ring-[#4a5690]/30 focus:border-[#4a5690]">{{ old('description') }}</textarea>
                </label>

                <label class="block">
                    <span class="text-sm font-medium text-slate-700">Required Digital Documents</span>
                    <p class="text-xs text-slate-500">Upload at least one file. Allowed: pdf, jpg, jpeg, png, doc, docx.</p>
                    <input type="file" name="digital_documents[]" multiple required class="mt-3 w-full text-sm text-slate-700" />
                </label>

                <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-[#4a5690] px-6 py-3 text-sm font-semibold text-white hover:bg-[#3b4679] transition">Submit Request</button>
            </form>
        </div>
    </div>
    <script src="{{ asset('js/ui-feedback.js') }}"></script>
</body>
</html>
