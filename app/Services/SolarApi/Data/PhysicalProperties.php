<?php

declare(strict_types=1);

namespace App\Services\SolarApi\Data;

use App\Services\SolarApi\Data\Concerns\CastsValues;

final readonly class PhysicalProperties
{
    use CastsValues;

    public function __construct(
        public ?float $radiusKm,
        public ?float $equatorialRadiusKm,
        public ?float $polarRadiusKm,
        public ?float $massKg,
        public ?float $densityGCm3,
        public ?float $rotationPeriodHours,
        public ?float $axialTiltDeg,
        public ?float $surfaceGravityMS2,
        public ?float $escapeVelocityKmS,
    ) {}

    /** @param array<string,mixed> $d */
    public static function fromArray(array $d): self
    {
        return new self(
            radiusKm: self::float($d, 'radius_km'),
            equatorialRadiusKm: self::float($d, 'equatorial_radius_km'),
            polarRadiusKm: self::float($d, 'polar_radius_km'),
            massKg: self::float($d, 'mass_kg'),
            densityGCm3: self::float($d, 'density_g_cm3'),
            rotationPeriodHours: self::float($d, 'rotation_period_hours'),
            axialTiltDeg: self::float($d, 'axial_tilt_deg'),
            surfaceGravityMS2: self::float($d, 'surface_gravity_m_s2'),
            escapeVelocityKmS: self::float($d, 'escape_velocity_km_s'),
        );
    }

    public function hasAny(): bool
    {
        return $this->radiusKm !== null
            || $this->massKg !== null
            || $this->densityGCm3 !== null
            || $this->rotationPeriodHours !== null;
    }

    /** Mean diameter in km, where a radius is known. */
    public function diameterKm(): ?float
    {
        return $this->radiusKm !== null ? $this->radiusKm * 2 : null;
    }
}
