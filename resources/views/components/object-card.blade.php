@props(['object'])

@php
    use App\Support\Format;
    /** @var \App\Services\SolarApi\Data\ObjectSummary $object */
    $facts = array_filter([
        Format::km($object->diameterKm()) ? __('Ø :v', ['v' => Format::km($object->diameterKm())]) : null,
        Format::au($object->semiMajorAxisAu, 2) ? __(':v from Sun', ['v' => Format::au($object->semiMajorAxisAu, 2)]) : null,
        Format::periodDays($object->orbitalPeriodDays) ? __(':v orbit', ['v' => Format::periodDays($object->orbitalPeriodDays)]) : null,
    ]);
@endphp

<a href="{{ route('objects.show', $object->slug()) }}"
   class="group surface flex flex-col gap-3 p-4 transition-colors"
   style="border-color: var(--border);"
   onmouseover="this.style.borderColor='color-mix(in srgb, var(--accent) 50%, var(--border))'"
   onmouseout="this.style.borderColor='var(--border)'">
    <div class="flex items-start justify-between gap-3">
        <div class="min-w-0">
            <h3 class="truncate font-serif text-lg font-medium" style="color: var(--text);">{{ $object->name }}</h3>
            @if ($object->designation && $object->designation !== $object->name)
                <p class="truncate text-xs" style="color: var(--color-faint);">{{ $object->designation }}</p>
            @endif
        </div>
        @if ($object->typeLabel())
            <x-badge class="shrink-0">{{ $object->typeLabel() }}</x-badge>
        @endif
    </div>

    @if ($facts)
        <dl class="mt-auto flex flex-wrap gap-x-4 gap-y-1 text-xs tabular-nums" style="color: var(--muted);">
            @foreach ($facts as $fact)
                <dd>{{ $fact }}</dd>
            @endforeach
        </dl>
    @endif

    @if ($object->isPha)
        <div><x-badge tone="danger">{{ __('Potentially hazardous') }}</x-badge></div>
    @endif
</a>
