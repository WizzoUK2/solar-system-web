@props(['title', 'eyebrow' => null, 'lead' => null])

<div {{ $attributes->merge(['class' => 'mb-8']) }}>
    @if ($eyebrow)
        <p class="mb-2 text-xs font-semibold uppercase tracking-[0.16em]" style="color: var(--accent);">{{ $eyebrow }}</p>
    @endif
    <h1 class="text-3xl font-medium sm:text-4xl">{{ $title }}</h1>
    @if ($lead)
        <p class="mt-3 text-base leading-relaxed" style="color: var(--muted); max-width: var(--container-prose);">{{ $lead }}</p>
    @endif
    @if (! $slot->isEmpty())
        <div class="mt-3 text-base leading-relaxed" style="color: var(--muted); max-width: var(--container-prose);">{{ $slot }}</div>
    @endif
</div>
