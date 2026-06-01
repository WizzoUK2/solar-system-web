<div>
    <x-page-header :title="__('Search')" :eyebrow="__('Find an object')" />

    <div class="mb-8">
        <label for="search-q" class="sr-only">{{ __('Search the catalogue') }}</label>
        <div class="flex items-center rounded-xl border px-4"
             style="border-color: var(--border); background-color: var(--bg-elevated);">
            <svg class="h-5 w-5 shrink-0" style="color: var(--muted)" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                <circle cx="9" cy="9" r="6" stroke="currentColor" stroke-width="1.6"/>
                <path d="m18 18-4.5-4.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
            </svg>
            <input id="search-q" type="search" wire:model.live.debounce.300ms="q"
                   placeholder="{{ __('Try “Saturn”, “Halley”, “Piazzi”…') }}" autocomplete="off" autofocus
                   class="w-full bg-transparent px-3 py-3.5 text-lg focus:outline-none" style="color: var(--text);">
            <span wire:loading wire:target="q" class="text-xs" style="color: var(--accent);">{{ __('Searching…') }}</span>
        </div>
    </div>

    @if ($apiDown)
        <x-api-down :section="__('Search')" />
    @elseif ($query === '')
        <x-empty-state :title="__('Search the catalogue')">
            {{ __('Start typing to find planets, moons, asteroids, comets and more by name, designation or discoverer.') }}
        </x-empty-state>
    @elseif (count($results) === 0)
        <x-empty-state :title="__('No matches for “:q”', ['q' => $query])">
            {{ __('Check the spelling, or try a designation like “1P” or a discoverer’s name.') }}
        </x-empty-state>
    @else
        <p class="mb-4 text-sm" style="color: var(--muted);">{{ trans_choice(':count result|:count results', count($results), ['count' => count($results)]) }}</p>
        <ul class="surface divide-y" style="border-color: var(--border);">
            @foreach ($results as $result)
                <li style="border-color: var(--border);">
                    <a href="{{ route('objects.show', $result->slug()) }}"
                       class="flex items-center justify-between gap-4 px-4 py-3.5 transition-colors"
                       style="border-color: var(--border);"
                       onmouseover="this.style.backgroundColor='var(--bg-elevated-2)'"
                       onmouseout="this.style.backgroundColor='transparent'">
                        <div class="min-w-0">
                            <p class="truncate font-medium" style="color: var(--text);">{{ $result->name }}</p>
                            @if ($result->designation && $result->designation !== $result->name)
                                <p class="truncate text-xs" style="color: var(--color-faint);">{{ $result->designation }}</p>
                            @endif
                        </div>
                        @if ($result->typeLabel())
                            <x-badge class="shrink-0">{{ $result->typeLabel() }}</x-badge>
                        @endif
                    </a>
                </li>
            @endforeach
        </ul>
    @endif
</div>
