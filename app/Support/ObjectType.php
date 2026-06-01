<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Presentation metadata for the backend's `object_type` values. Kept in one
 * place so labels, plurals and category groupings stay consistent across every
 * page. Mirrors the CHECK constraint in the backend schema.
 */
final class ObjectType
{
    /** @var array<string,array{label:string,plural:string}> */
    private const TYPES = [
        'star' => ['label' => 'Star', 'plural' => 'Stars'],
        'planet' => ['label' => 'Planet', 'plural' => 'Planets'],
        'moon' => ['label' => 'Moon', 'plural' => 'Moons'],
        'dwarf_planet' => ['label' => 'Dwarf planet', 'plural' => 'Dwarf planets'],
        'dwarf_planet_candidate' => ['label' => 'Dwarf planet candidate', 'plural' => 'Dwarf planet candidates'],
        'asteroid' => ['label' => 'Asteroid', 'plural' => 'Asteroids'],
        'comet' => ['label' => 'Comet', 'plural' => 'Comets'],
        'tno' => ['label' => 'Trans-Neptunian object', 'plural' => 'Trans-Neptunian objects'],
        'centaur' => ['label' => 'Centaur', 'plural' => 'Centaurs'],
        'trojan' => ['label' => 'Trojan', 'plural' => 'Trojans'],
        'hilda' => ['label' => 'Hilda', 'plural' => 'Hildas'],
        'neo' => ['label' => 'Near-Earth object', 'plural' => 'Near-Earth objects'],
        'pha' => ['label' => 'Potentially hazardous asteroid', 'plural' => 'Potentially hazardous asteroids'],
    ];

    /** Types offered in the /objects filter dropdown, in a sensible reading order. */
    public const FILTERABLE = [
        'planet', 'dwarf_planet', 'moon', 'asteroid', 'comet', 'tno', 'centaur',
    ];

    public static function label(string $type): string
    {
        return self::TYPES[$type]['label'] ?? self::humanise($type);
    }

    public static function plural(string $type): string
    {
        return self::TYPES[$type]['plural'] ?? self::humanise($type).'s';
    }

    public static function exists(string $type): bool
    {
        return isset(self::TYPES[$type]);
    }

    /** Human label for a classification label such as NEO, PHA, KBO. */
    public static function classification(string $label): string
    {
        return match (strtoupper($label)) {
            'NEO' => 'Near-Earth Object',
            'PHA' => 'Potentially Hazardous Asteroid',
            'MBA' => 'Main-Belt Asteroid',
            'KBO' => 'Kuiper-Belt Object',
            'SDO' => 'Scattered-Disc Object',
            default => $label,
        };
    }

    private static function humanise(string $type): string
    {
        return ucfirst(str_replace('_', ' ', $type));
    }
}
