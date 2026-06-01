@props(['title' => null])

<div class="surface flex flex-col items-center justify-center gap-2 px-6 py-16 text-center"
     style="background-color: transparent; border-style: dashed;">
    <svg class="h-8 w-8" style="color: var(--color-faint)" viewBox="0 0 24 24" fill="none" aria-hidden="true">
        <circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="1.5"/>
        <path d="m21 21-4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
    </svg>
    <p class="font-medium" style="color: var(--text);">{{ $title ?? __('Nothing to show') }}</p>
    @if (! $slot->isEmpty())
        <p class="text-sm" style="color: var(--muted);">{{ $slot }}</p>
    @endif
</div>
