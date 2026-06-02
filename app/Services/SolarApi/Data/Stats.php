<?php

declare(strict_types=1);

namespace App\Services\SolarApi\Data;

use App\Services\SolarApi\Data\Concerns\CastsValues;
use Carbon\CarbonImmutable;

/**
 * Catalogue-wide statistics from /stats — counts by type plus the last
 * nightly-build timestamp.
 */
final readonly class Stats
{
    use CastsValues;

    /**
     * @param  array<string,int>  $byObjectType
     * @param  array<string,mixed>|null  $lastBuild
     */
    public function __construct(
        public int $totalObjects,
        public array $byObjectType,
        public ?array $lastBuild,
    ) {}

    /** @param array<string,mixed> $d */
    public static function fromArray(array $d): self
    {
        $counts = [];
        foreach ((array) ($d['by_object_type'] ?? []) as $type => $n) {
            $counts[(string) $type] = (int) $n;
        }

        return new self(
            totalObjects: (int) ($d['total_objects'] ?? array_sum($counts)),
            byObjectType: $counts,
            lastBuild: is_array($d['last_build'] ?? null) ? $d['last_build'] : null,
        );
    }

    public function count(string $type): int
    {
        return $this->byObjectType[$type] ?? 0;
    }

    /** Moons + dwarf planets etc. roll up cleanly via count(); convenience here. */
    public function planets(): int
    {
        return $this->count('planet');
    }

    public function moons(): int
    {
        return $this->count('moon');
    }

    public function dwarfPlanets(): int
    {
        return $this->count('dwarf_planet') + $this->count('dwarf_planet_candidate');
    }

    public function asteroids(): int
    {
        return $this->count('asteroid');
    }

    public function comets(): int
    {
        return $this->count('comet');
    }

    public function transNeptunian(): int
    {
        return $this->count('tno') + $this->count('centaur');
    }

    /** When the catalogue was last rebuilt, if known. */
    public function lastRefreshed(): ?CarbonImmutable
    {
        $ts = $this->lastBuild['finished_at'] ?? $this->lastBuild['started_at'] ?? null;

        if (! is_string($ts) || $ts === '') {
            return null;
        }

        try {
            return CarbonImmutable::parse($ts);
        } catch (\Throwable) {
            return null;
        }
    }
}
