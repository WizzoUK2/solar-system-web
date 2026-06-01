<?php

declare(strict_types=1);

namespace App\Services\SolarApi\Data\Concerns;

/**
 * Null-tolerant casting helpers shared by the DTOs. The backend returns plenty
 * of nulls (a comet has no radius, a moon has no albedo) so every accessor here
 * copes with a missing key gracefully rather than throwing.
 */
trait CastsValues
{
    protected static function float(array $data, string $key): ?float
    {
        $value = $data[$key] ?? null;

        return is_numeric($value) ? (float) $value : null;
    }

    protected static function str(array $data, string $key): ?string
    {
        $value = $data[$key] ?? null;

        if ($value === null || $value === '') {
            return null;
        }

        return (string) $value;
    }

    protected static function bool(array $data, string $key): bool
    {
        return (bool) ($data[$key] ?? false);
    }

    protected static function int(array $data, string $key): ?int
    {
        $value = $data[$key] ?? null;

        return is_numeric($value) ? (int) $value : null;
    }
}
