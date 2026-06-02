<?php

declare(strict_types=1);

namespace App\Services\SolarApi\Data;

use App\Services\SolarApi\Data\Concerns\CastsValues;
use App\Support\ObjectType;

final readonly class SearchResult
{
    use CastsValues;

    public function __construct(
        public string $id,
        public string $name,
        public ?string $designation,
        public ?string $objectType,
        public ?string $parentId,
        public ?string $discoverer,
    ) {}

    /** @param array<string,mixed> $d */
    public static function fromArray(array $d): self
    {
        return new self(
            id: (string) ($d['id'] ?? ''),
            name: self::str($d, 'name') ?? (self::str($d, 'designation') ?? 'Unnamed object'),
            designation: self::str($d, 'designation'),
            objectType: self::str($d, 'object_type'),
            parentId: self::str($d, 'parent_id'),
            discoverer: self::str($d, 'discoverer'),
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
}
