<?php

declare(strict_types=1);

use App\Support\ObjectType;

it('labels and pluralises known types', function () {
    expect(ObjectType::label('dwarf_planet'))->toBe('Dwarf planet')
        ->and(ObjectType::plural('dwarf_planet'))->toBe('Dwarf planets')
        ->and(ObjectType::label('tno'))->toBe('Trans-Neptunian object')
        ->and(ObjectType::plural('moon'))->toBe('Moons');
});

it('humanises an unknown type gracefully', function () {
    expect(ObjectType::label('some_new_type'))->toBe('Some new type')
        ->and(ObjectType::exists('some_new_type'))->toBeFalse()
        ->and(ObjectType::exists('comet'))->toBeTrue();
});

it('expands classification acronyms', function () {
    expect(ObjectType::classification('NEO'))->toBe('Near-Earth Object')
        ->and(ObjectType::classification('PHA'))->toBe('Potentially Hazardous Asteroid')
        ->and(ObjectType::classification('KBO'))->toBe('Kuiper-Belt Object')
        ->and(ObjectType::classification('Trojan'))->toBe('Trojan'); // unknown passes through
});

it('only offers sensible types in the filter list', function () {
    expect(ObjectType::FILTERABLE)->toContain('planet', 'moon', 'comet')
        ->and(ObjectType::FILTERABLE)->not->toContain('star', 'neo', 'pha');
});
