@props(['section' => null])

{{-- A calm, inline degradation panel shown when the backend can't be reached.
     The rest of the page keeps working; this never throws. (BRIEF.md §5) --}}
<div role="status" class="surface flex items-start gap-3 p-5"
     style="background-color: var(--bg-elevated-2);">
    <svg class="mt-0.5 h-5 w-5 shrink-0" style="color: var(--accent)" viewBox="0 0 20 20" fill="none" aria-hidden="true">
        <circle cx="10" cy="10" r="8" stroke="currentColor" stroke-width="1.5"/>
        <path d="M10 6v5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
        <circle cx="10" cy="14" r="0.9" fill="currentColor"/>
    </svg>
    <div>
        <p class="font-medium" style="color: var(--text);">
            {{ $slot->isEmpty() ? __('We can’t reach the catalogue right now') : $slot }}
        </p>
        <p class="mt-1 text-sm" style="color: var(--muted);">
            @if ($section)
                {{ __(':section is temporarily unavailable. Please try again in a moment.', ['section' => $section]) }}
            @else
                {{ __('This data is temporarily unavailable. Please try again in a moment — the rest of the page still works.') }}
            @endif
        </p>
    </div>
</div>
