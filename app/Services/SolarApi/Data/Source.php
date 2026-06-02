<?php

declare(strict_types=1);

namespace App\Services\SolarApi\Data;

use App\Services\SolarApi\Data\Concerns\CastsValues;

/**
 * A provenance record: where a given table's data for an object came from.
 * The catalogue-wide /sources endpoint returns aggregated rows (with `n`,
 * `first_seen`, `last_seen`) which this DTO also accommodates.
 */
final readonly class Source
{
    use CastsValues;

    public function __construct(
        public ?string $tableName,
        public string $sourceName,
        public ?string $sourceUrl,
        public ?string $retrievedAt,
        public ?int $count = null,
    ) {}

    /** @param array<string,mixed> $d */
    public static function fromArray(array $d): self
    {
        return new self(
            tableName: self::str($d, 'table_name'),
            sourceName: self::str($d, 'source_name') ?? 'Unknown source',
            sourceUrl: self::str($d, 'source_url'),
            retrievedAt: self::str($d, 'retrieved_at') ?? self::str($d, 'last_seen'),
            count: self::int($d, 'n'),
        );
    }
}
