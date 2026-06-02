<?php

declare(strict_types=1);

namespace App\Services\SolarApi\Data;

use App\Services\SolarApi\Data\Concerns\CastsValues;

/**
 * Keplerian orbital elements for a single object. Any field may be null.
 */
final readonly class OrbitalElements
{
    use CastsValues;

    public function __construct(
        public ?string $epoch,
        public ?float $epochJd,
        public ?string $frame,
        public ?string $centre,
        public ?float $semiMajorAxisAu,
        public ?float $eccentricity,
        public ?float $inclinationDeg,
        public ?float $longitudeAscendingNodeDeg,
        public ?float $argumentPeriapsisDeg,
        public ?float $meanAnomalyDeg,
        public ?float $orbitalPeriodDays,
        public ?float $perihelionAu,
        public ?float $aphelionAu,
        public ?float $meanMotionDegPerDay,
    ) {}

    /** @param array<string,mixed> $d */
    public static function fromArray(array $d): self
    {
        return new self(
            epoch: self::str($d, 'epoch'),
            epochJd: self::float($d, 'epoch_jd'),
            frame: self::str($d, 'frame'),
            centre: self::str($d, 'centre'),
            semiMajorAxisAu: self::float($d, 'semi_major_axis_au'),
            eccentricity: self::float($d, 'eccentricity'),
            inclinationDeg: self::float($d, 'inclination_deg'),
            longitudeAscendingNodeDeg: self::float($d, 'longitude_ascending_node_deg'),
            argumentPeriapsisDeg: self::float($d, 'argument_periapsis_deg'),
            meanAnomalyDeg: self::float($d, 'mean_anomaly_deg'),
            orbitalPeriodDays: self::float($d, 'orbital_period_days'),
            perihelionAu: self::float($d, 'perihelion_au'),
            aphelionAu: self::float($d, 'aphelion_au'),
            meanMotionDegPerDay: self::float($d, 'mean_motion_deg_per_day'),
        );
    }

    /** Does this record carry enough to be worth rendering / propagating? */
    public function hasAny(): bool
    {
        return $this->semiMajorAxisAu !== null
            || $this->eccentricity !== null
            || $this->orbitalPeriodDays !== null
            || $this->perihelionAu !== null;
    }

    /** Enough elements present to compute a position via /positions? */
    public function isPropagatable(): bool
    {
        return $this->semiMajorAxisAu !== null
            && $this->eccentricity !== null
            && $this->orbitalPeriodDays !== null;
    }
}
