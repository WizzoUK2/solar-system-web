<div>
    <x-page-header :title="__('Orrery')" :eyebrow="__('Where the worlds are')"
                   :lead="__('A top-down map of the solar system out to Pluto, positioned for any date from live ephemeris. Distances use a square-root scale so the inner and outer worlds are both legible — it is not to scale.')" />

    {{-- Date controls --}}
    <div class="mb-6 flex flex-wrap items-center gap-3">
        <div class="flex items-center gap-1">
            <button type="button" wire:click="step(-30)" class="rounded-lg border px-3 py-2 text-sm" style="border-color: var(--border); color: var(--text);" aria-label="{{ __('Back one month') }}">−30d</button>
            <button type="button" wire:click="step(-1)" class="rounded-lg border px-3 py-2 text-sm" style="border-color: var(--border); color: var(--text);" aria-label="{{ __('Back one day') }}">−1d</button>
        </div>
        <label class="sr-only" for="orrery-date">{{ __('Date') }}</label>
        <input id="orrery-date" type="date" wire:model.live="date"
               class="rounded-lg border px-3 py-2 text-sm tabular-nums focus:outline-none"
               style="border-color: var(--border); background-color: var(--bg-elevated); color: var(--text);">
        <div class="flex items-center gap-1">
            <button type="button" wire:click="step(1)" class="rounded-lg border px-3 py-2 text-sm" style="border-color: var(--border); color: var(--text);" aria-label="{{ __('Forward one day') }}">+1d</button>
            <button type="button" wire:click="step(30)" class="rounded-lg border px-3 py-2 text-sm" style="border-color: var(--border); color: var(--text);" aria-label="{{ __('Forward one month') }}">+30d</button>
        </div>
        <button type="button" wire:click="today" class="rounded-lg px-3 py-2 text-sm font-medium" style="background-color: var(--accent); color: #07090f;">{{ __('Today') }}</button>
        <span class="ml-auto text-sm tabular-nums" style="color: var(--muted);">{{ $prettyDate }}</span>
    </div>

    @if ($apiDown)
        <x-api-down :section="__('The orrery')" />
    @elseif (count($bodies) === 0)
        <x-empty-state :title="__('No positions for that date')">
            {{ __('Try a date closer to the present.') }}
        </x-empty-state>
    @else
        <div class="surface overflow-hidden p-2 sm:p-4" wire:loading.class="opacity-60">
            <svg viewBox="0 0 600 600" class="mx-auto h-auto w-full" style="max-width: 640px;"
                 role="img" aria-label="{{ __('Solar system positions for :date', ['date' => $prettyDate]) }}">
                {{-- Orbit rings --}}
                @foreach ($bodies as $body)
                    <circle cx="300" cy="300" r="{{ $body['r'] }}" fill="none"
                            stroke="var(--border)" stroke-width="1" opacity="0.5" />
                @endforeach

                {{-- The Sun --}}
                <circle cx="300" cy="300" r="9" fill="var(--accent)" />
                <circle cx="300" cy="300" r="16" fill="none" stroke="var(--accent)" stroke-width="0.75" opacity="0.35" />

                {{-- Bodies --}}
                @foreach ($bodies as $body)
                    <a href="{{ route('objects.show', $body['slug']) }}" wire:key="orrery-{{ $body['slug'] }}">
                        <circle cx="{{ $body['cx'] }}" cy="{{ $body['cy'] }}" r="6"
                                fill="{{ $body['colour'] }}" stroke="#05070d" stroke-width="0.75">
                            <title>{{ $body['label'] }} — {{ \App\Support\Format::au($body['distance']) }} {{ __('from the Sun') }}</title>
                        </circle>
                        <text x="{{ $body['cx'] + 10 }}" y="{{ $body['cy'] + 4 }}"
                              font-size="12" fill="var(--muted)" style="font-family: var(--font-sans);">{{ $body['label'] }}</text>
                    </a>
                @endforeach
            </svg>
        </div>

        <p class="mt-4 text-xs leading-relaxed" style="color: var(--color-faint); max-width: var(--container-prose);">
            {{ __('Positions are two-body Kepler propagation from J2000 elements — good to about 0.1% for the major planets over decades. For high precision use JPL Horizons. Click a body for its full record.') }}
        </p>
    @endif
</div>
