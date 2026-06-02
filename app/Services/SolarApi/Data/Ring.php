<?php

declare(strict_types=1);

namespace App\Services\SolarApi\Data;

use App\Services\SolarApi\Data\Concerns\CastsValues;

final readonly class Ring
{
    use CastsValues;

    public function __construct(
        public ?int $id,
        public string $name,
        public ?float $innerRadiusKm,
        public ?float $outerRadiusKm,
        public ?float $widthKm,
        public ?float $thicknessKm,
        public ?string $notes,
    ) {}

    /** @param array<string,mixed> $d */
    public static function fromArray(array $d): self
    {
        return new self(
            id: self::int($d, 'id'),
            name: self::str($d, 'name') ?? 'Ring',
            innerRadiusKm: self::float($d, 'inner_radius_km'),
            outerRadiusKm: self::float($d, 'outer_radius_km'),
            widthKm: self::float($d, 'width_km'),
            thicknessKm: self::float($d, 'thickness_km'),
            notes: self::str($d, 'notes'),
        );
    }

    /** Derive a width when not given but both radii are known. */
    public function effectiveWidthKm(): ?float
    {
        if ($this->widthKm !== null) {
            return $this->widthKm;
        }

        if ($this->innerRadiusKm !== null && $this->outerRadiusKm !== null) {
            return $this->outerRadiusKm - $this->innerRadiusKm;
        }

        return null;
    }
}
