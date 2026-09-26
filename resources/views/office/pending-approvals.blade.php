<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Planning Office Review · UniCalendar</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-100 text-slate-900">
    <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6">
        <header class="mb-6 flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-emerald-800">Planning Office</p>
                <h1 class="mt-2 text-3xl font-semibold">Request review</h1>
                <p class="mt-2 text-sm text-slate-600">Check the schedule, resolve any conflicts, and record the planning decision.</p>
            </div>
            <a href="{{ route('planning_office.dashboard') }}" class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold">Back to dashboard</a>
        </header>

        @if(session('success'))
            <div class="mb-5 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="mb-5 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">{{ session('error') }}</div>
        @endif
        @if($errors->any())
            <div class="mb-5 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">{{ $errors->first() }}</div>
        @endif

        <div class="space-y-4">
            @forelse($requests as $request)
                <article class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                    <div class="flex flex-wrap items-start justify-between gap-4 border-b border-slate-100 px-5 py-4">
                        <div>
                            <div class="flex flex-wrap items-center gap-2">
                                <h2 class="text-lg font-semibold">{{ $request->title }}</h2>
                                <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $request->status === 'conflict' ? 'bg-rose-100 text-rose-800' : 'bg-amber-100 text-amber-800' }}">{{ $request->status === 'conflict' ? 'Conflict detected' : 'Awaiting review' }}</span>
                            </div>
                            <p class="mt-1 text-sm text-slate-500">{{ $request->name }} · {{ $request->email }} · {{ $request->campus ?? 'Campus not specified' }}</p>
                        </div>
                        @if($request->sdg_number)
                            <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-800">SDG {{ $request->sdg_number }}</span>
                        @else
                            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">Not aligned to SDG</span>
                        @endif
                    </div>

                    <div class="grid gap-5 px-5 py-4 lg:grid-cols-[minmax(0,1fr)_minmax(0,1.2fr)]">
                        <div class="space-y-3 text-sm">
                            <p><span class="font-semibold">Requested schedule:</span><br>{{ $request->start_datetime->format('D, M j, Y · g:i A') }} – {{ $request->end_datetime->format('g:i A') }}</p>
                            <p><span class="font-semibold">Venue:</span> {{ $request->venue_name }}</p>
                            <p class="text-slate-600">{{ $request->description ?: 'No event description provided.' }}</p>
                            @if($request->planning_note)<p class="border-l-2 border-slate-300 pl-3 text-slate-600">Previous note: {{ $request->planning_note }}</p>@endif
                            @if($request->digital_documents)
                                <div class="flex flex-wrap gap-2">
                                    @foreach($request->digital_documents as $document)
                                        <a class="text-emerald-800 underline" href="{{ asset('storage/' . $document) }}" target="_blank" rel="noopener">Document {{ $loop->iteration }}</a>
                                    @endforeach
                                </div>
                            @endif
                            @if($request->conflicts->isNotEmpty())
                                <div class="rounded-lg border border-rose-200 bg-rose-50 p-3">
                                    <p class="font-semibold text-rose-800">Overlapping event request(s)</p>
                                    @foreach($request->conflicts as $conflict)
                                        <p class="mt-1 text-rose-700">{{ $conflict->title }} · {{ $conflict->start_datetime->format('M j, g:i A') }} – {{ $conflict->end_datetime->format('g:i A') }}</p>
                                    @endforeach
                                </div>
                            @else
                                <p class="font-medium text-emerald-800">No venue or time conflicts detected.</p>
                            @endif
                        </div>

                        <form method="POST" action="{{ route('planning_office.approve', ['id' => $request->id]) }}" class="grid gap-3 sm:grid-cols-2">
                            @csrf
                            <label class="text-sm font-medium">Venue
                                <input name="venue_name" value="{{ $request->venue_name }}" required class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 font-normal">
                            </label>
                            <label class="text-sm font-medium">Campus
                                <select name="campus" required class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 font-normal">
                                    @foreach(['Alaminos Campus', 'Lingayen Campus', 'Binmaley Campus', 'All Campus'] as $campus)
                                        <option value="{{ $campus }}" {{ $request->campus === $campus ? 'selected' : '' }}>{{ $campus }}</option>
                                    @endforeach
                                </select>
                            </label>
                            <label class="text-sm font-medium">Start
                                <input type="datetime-local" name="start_datetime" value="{{ $request->start_datetime->format('Y-m-d\\TH:i') }}" required class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 font-normal">
                            </label>
                            <label class="text-sm font-medium">End
                                <input type="datetime-local" name="end_datetime" value="{{ $request->end_datetime->format('Y-m-d\\TH:i') }}" required class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 font-normal">
                            </label>
                            <label class="text-sm font-medium sm:col-span-2">SDG alignment
                                <select name="sdg_number" class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 font-normal">
                                    <option value="">Not aligned to an SDG</option>
                                    @foreach(['No Poverty', 'Zero Hunger', 'Good Health and Well-being', 'Quality Education', 'Gender Equality', 'Clean Water and Sanitation', 'Affordable and Clean Energy', 'Decent Work and Economic Growth', 'Industry, Innovation and Infrastructure', 'Reduced Inequalities', 'Sustainable Cities and Communities', 'Responsible Consumption and Production', 'Climate Action', 'Life Below Water', 'Life on Land', 'Peace, Justice and Strong Institutions', 'Partnerships for the Goals'] as $index => $sdg)
                                        <option value="{{ $index + 1 }}" {{ (string) $request->sdg_number === (string) ($index + 1) ? 'selected' : '' }}>SDG {{ $index + 1 }}: {{ $sdg }}</option>
                                    @endforeach
                                </select>
                            </label>
                            <label class="text-sm font-medium sm:col-span-2">Planning note
                                <textarea name="planning_note" rows="2" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 font-normal" placeholder="Optional note for the requester">{{ $request->planning_note }}</textarea>
                            </label>
                            <div class="flex flex-wrap gap-2 sm:col-span-2">
                                <button type="submit" class="rounded-lg bg-emerald-700 px-4 py-2 text-sm font-semibold text-white">Save and approve</button>
                                <button formaction="{{ route('planning_office.reject', ['id' => $request->id]) }}" formmethod="post" type="submit" class="rounded-lg border border-rose-300 px-4 py-2 text-sm font-semibold text-rose-800">Reject request</button>
                            </div>
                        </form>
                    </div>
                </article>
            @empty
                <div class="rounded-xl border border-dashed border-slate-300 bg-white px-6 py-12 text-center text-slate-500">No requests are awaiting review.</div>
            @endforelse
        </div>
    </main>
</body>
</html>