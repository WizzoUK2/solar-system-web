@php use App\Support\Format; @endphp
<div>
    <x-page-header :title="__('The planets')"
                   :eyebrow="__('Eight worlds')"
                   :lead="__('From scorched, sunward Mercury to deep-blue Neptune on the cold edge of the system — the eight planets in order of their distance from the Sun.')" />

    @if ($apiDown)
        <x-api-down :section="__('The planets')" />
    @else
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($planets as $i => $planet)
                <a href="{{ route('objects.show', $planet->slug()) }}"
                   class="surface group flex flex-col gap-4 p-6 transition-colors"
                   onmouseover="this.style.borderColor='color-mix(in srgb, var(--accent) 45%, var(--border))'"
                   onmouseout="this.style.borderColor='var(--border)'">
                    <div class="flex items-center justify-between">
                        <span aria-hidden="true" class="block h-14 w-14 rounded-full"
                              style="background: radial-gradient(circle at 32% 30%, color-mix(in srgb, var(--accent) 70%, white), var(--accent) 60%, #05070d);"></span>
                        <span class="text-xs tabular-nums" style="color: var(--color-faint);">{{ str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    <div>
                        <h2 class="font-serif text-2xl font-medium" style="color: var(--text);">{{ $planet->name }}</h2>
                        <dl class="mt-2 space-y-0.5 text-xs tabular-nums" style="color: var(--muted);">
                            @if ($d = Format::km($planet->diameterKm()))
                                <div>{{ __('Ø :v', ['v' => $d]) }}</div>
                            @endif
                            @if ($a = Format::au($planet->semiMajorAxisAu, 2))
                                <div>{{ __(':v from Sun', ['v' => $a]) }}</div>
                            @endif
                            @if ($p = Format::periodDays($planet->orbitalPeriodDays))
                                <div>{{ __(':v orbit', ['v' => $p]) }}</div>
                            @endif
                        </dl>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</div>
