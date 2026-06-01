<?php

declare(strict_types=1);

namespace App\Services\SolarApi\Data;

use App\Services\SolarApi\Data\Concerns\CastsValues;
use App\Support\ObjectType;

/**
 * The full record for a single object: the base row plus its orbital,
 * physical and visual blocks, classification labels and provenance.
 */
final readonly class ObjectDetail
{
    use CastsValues;

    /**
     * @param  list<string>  $classifications
     * @param  list<Source>  $sources
     */
    public function __construct(
        public string $id,
        public string $name,
        public ?string $designation,
        public ?string $objectType,
        public ?string $parentId,
        public ?string $discoverer,
        public ?string $discoveryDate,
        public ?string $wikipediaUrl,
        public ?string $notes,
        public ?string $updatedAt,
        public ?OrbitalElements $orbital,
        public ?PhysicalProperties $physical,
        public ?VisualProperties $visual,
        public array $classifications,
        public array $sources,
    ) {}

    public static function fromArray(array $d): self
    {
        $orbital = is_array($d['orbital'] ?? null) ? OrbitalElements::fromArray($d['orbital']) : null;
        $physical = is_array($d['physical'] ?? null) ? PhysicalProperties::fromArray($d['physical']) : null;
        $visual = is_array($d['visual'] ?? null) ? VisualProperties::fromArray($d['visual']) : null;

        return new self(
            id: (string) ($d['id'] ?? ''),
            name: self::str($d, 'name') ?? (self::str($d, 'designation') ?? 'Unnamed object'),
            designation: self::str($d, 'designation'),
            objectType: self::str($d, 'object_type'),
            parentId: self::str($d, 'parent_id'),
            discoverer: self::str($d, 'discoverer'),
            discoveryDate: self::str($d, 'discovery_date'),
            wikipediaUrl: self::str($d, 'wikipedia_url'),
            notes: self::str($d, 'notes'),
            updatedAt: self::str($d, 'updated_at'),
            orbital: $orbital,
            physical: $physical,
            visual: $visual,
            classifications: array_values(array_map('strval', (array) ($d['classifications'] ?? []))),
            sources: array_values(array_map(
                static fn (array $s) => Source::fromArray($s),
                array_filter((array) ($d['sources'] ?? []), 'is_array'),
            )),
        );
    }

    public function slug(): string
    {
        return $this->id;
    }

    public function typeLabel(): ?string
    {
        return $this->objectType ? ObjectType::label($this->objectType) : null;
    }

    /** Does the parent link point at a real catalogue object (not the Sun root)? */
    public function hasParent(): bool
    {
        return $this->parentId !== null && $this->parentId !== '' && $this->parentId !== 'sun';
    }

    public function isNeo(): bool
    {
        return in_array('NEO', $this->classifications, true);
    }

    public function isPha(): bool
    {
        return in_array('PHA', $this->classifications, true);
    }

    /** This object can host moons/rings sub-listings. */
    public function isPlanetLike(): bool
    {
        return in_array($this->objectType, ['planet', 'dwarf_planet', 'dwarf_planet_candidate'], true);
    }
}
