@props(['eccentricity' => 0.0, 'trueAnomaly' => null, 'label' => null])

@php
    // A schematic, top-down view of the orbit in its own plane: the Sun sits at
    // a focus, the object at its true anomaly. Not to scale between objects —
    // it conveys orbit shape and where the body is along it.
    $e = max(0.0, min((float) ($eccentricity ?? 0.0), 0.95));
    $A = 78.0;                       // svg semi-major axis
    $b = $A * sqrt(max(0.0, 1 - $e * $e));
    $c = $A * $e;                    // focus offset
    $sunX = 110.0; $sunY = 75.0;
    $cx = $sunX + $c;                // ellipse centre (sun is the left focus)

    $marker = null;
    if ($trueAnomaly !== null) {
        $nu = deg2rad((float) $trueAnomaly);
        $r = $A * (1 - $e * $e) / (1 + $e * cos($nu));
        $marker = [
            'x' => $sunX - $r * cos($nu),
            'y' => $sunY + $r * sin($nu),
        ];
    }
@endphp

<svg viewBox="0 0 220 150" class="h-auto w-full" role="img"
     aria-label="{{ $label ?? __('Orbit diagram') }}" style="max-width: 320px;">
    {{-- Orbit path --}}
    <ellipse cx="{{ round($cx, 1) }}" cy="{{ $sunY }}" rx="{{ round($A, 1) }}" ry="{{ round($b, 1) }}"
             fill="none" stroke="var(--border)" stroke-width="1.2" />
    {{-- Sun at focus --}}
    <circle cx="{{ $sunX }}" cy="{{ $sunY }}" r="5"
            fill="var(--accent)" />
    <circle cx="{{ $sunX }}" cy="{{ $sunY }}" r="9"
            fill="none" stroke="var(--accent)" stroke-width="0.6" opacity="0.4" />
    {{-- Object marker --}}
    @if ($marker)
        <line x1="{{ $sunX }}" y1="{{ $sunY }}" x2="{{ round($marker['x'], 1) }}" y2="{{ round($marker['y'], 1) }}"
              stroke="var(--link)" stroke-width="0.8" stroke-dasharray="2 2" opacity="0.6" />
        <circle cx="{{ round($marker['x'], 1) }}" cy="{{ round($marker['y'], 1) }}" r="4"
                fill="var(--link)" />
    @endif
</svg>
