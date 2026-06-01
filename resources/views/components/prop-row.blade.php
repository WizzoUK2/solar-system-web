@props(['label', 'value' => null, 'hint' => null])

{{-- A single definition-list row. Renders nothing when there's no value, so a
     property table only shows facts the catalogue actually holds. --}}
@php $resolved = $value ?? (trim($slot) ?: null); @endphp
@if ($resolved !== null && $resolved !== '')
    <div class="flex flex-col gap-0.5 border-b py-2.5 sm:flex-row sm:items-baseline sm:justify-between sm:gap-6"
         style="border-color: var(--border);">
        <dt class="text-sm" style="color: var(--muted);">
            {{ $label }}
            @if ($hint)
                <span class="text-xs" style="color: var(--color-faint);">· {{ $hint }}</span>
            @endif
        </dt>
        <dd class="text-sm font-medium tabular-nums sm:text-right" style="color: var(--text);">
            {{ $value ?? $slot }}
        </dd>
    </div>
@endif
