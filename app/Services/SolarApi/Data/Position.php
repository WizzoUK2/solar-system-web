<?php

declare(strict_types=1);

namespace App\Services\SolarApi\Data;

use App\Services\SolarApi\Data\Concerns\CastsValues;

/**
 * A computed heliocentric position (two-body Kepler propagation) from
 * /positions/{id}?date=…. Coordinates are AU in the ecliptic J2000 frame.
 */
final readonly class Position
{
    use CastsValues;

    public function __construct(
        public ?string $name,
        public ?string $designation,
        public ?string $inputDate,
        public ?float $xAu,
        public ?float $yAu,
        public ?float $zAu,
        public ?float $distanceFromSunAu,
        public ?float $trueAnomalyDeg,
        public ?float $eccentricAnomalyDeg,
        public ?float $meanAnomalyDeg,
        public ?float $jd,
        public ?string $frame,
        public ?string $accuracyNote,
    ) {}

    public static function fromArray(array $d): self
    {
        return new self(
            name: self::str($d, 'name'),
            designation: self::str($d, 'designation'),
            inputDate: self::str($d, 'input_date'),
            xAu: self::float($d, 'x_au'),
            yAu: self::float($d, 'y_au'),
            zAu: self::float($d, 'z_au'),
            distanceFromSunAu: self::float($d, 'distance_from_sun_au'),
            trueAnomalyDeg: self::float($d, 'true_anomaly_deg'),
            eccentricAnomalyDeg: self::float($d, 'eccentric_anomaly_deg'),
            meanAnomalyDeg: self::float($d, 'mean_anomaly_deg'),
            jd: self::float($d, 'jd'),
            frame: self::str($d, 'frame'),
            accuracyNote: self::str($d, 'accuracy_note'),
        );
    }
}
