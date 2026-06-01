@props(['options' => [], 'selected' => null])

<select {{ $attributes->merge(['class' => 'w-full rounded-lg border px-3 py-2 text-sm focus:outline-none']) }}
        style="border-color: var(--border); background-color: var(--bg); color: var(--text);">
    @foreach ($options as $value => $label)
        <option value="{{ $value }}" @selected((string) $value === (string) $selected)>{{ $label }}</option>
    @endforeach
</select>
