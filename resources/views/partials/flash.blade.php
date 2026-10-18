{{-- Outcome of the last action (session flash), shown as a toast so it is visible wherever the page is scrolled.
     Success/info hide themselves after a few seconds; errors stay until dismissed.
     Needs css/ui-feedback.css and js/ui-feedback.js. --}}
@php
    $flashTypes = [
        'success' => ['title' => 'Done', 'role' => 'status', 'timeout' => 6000, 'icon' => 'M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
        'info'    => ['title' => 'Note', 'role' => 'status', 'timeout' => 8000, 'icon' => 'M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z'],
        'error'   => ['title' => 'Something went wrong', 'role' => 'alert', 'timeout' => 0, 'icon' => 'M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z'],
    ];
    $flashMessages = collect($flashTypes)->filter(fn ($meta, $type) => session()->has($type));
@endphp

<div class="ui-toasts" aria-live="polite">
    @foreach($flashMessages as $type => $meta)
        <div class="ui-toast ui-toast--{{ $type }}" role="{{ $meta['role'] }}" data-toast data-timeout="{{ $meta['timeout'] }}">
            <svg class="ui-toast__icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $meta['icon'] }}" /></svg>
            <div class="ui-toast__body">
                <p class="ui-toast__title">{{ $meta['title'] }}</p>
                <p class="ui-toast__message">{{ session($type) }}</p>
            </div>
            <button type="button" class="ui-toast__close" data-toast-close aria-label="Dismiss message">&times;</button>
        </div>
    @endforeach
</div>
