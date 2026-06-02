<?php

declare(strict_types=1);

use App\Livewire\Category;
use App\Livewire\Objects\Index as ObjectsIndex;
use App\Livewire\Objects\Show as ObjectsShow;
use App\Livewire\Orrery;
use App\Livewire\SearchPage;
use Livewire\Livewire;

beforeEach(fn () => fakeSolar());

it('resets to page one and reloads results when a filter changes', function () {
    Livewire::test(ObjectsIndex::class)
        ->set('page', 3)
        ->set('type', 'asteroid')
        ->assertSet('page', 1)            // updated() resets paging
        ->assertSee('Object 0');
});

it('clears all filters', function () {
    Livewire::test(ObjectsIndex::class)
        ->set('type', 'asteroid')
        ->set('neo', true)
        ->set('named', true)
        ->call('clearFilters')
        ->assertSet('type', '')
        ->assertSet('neo', false)
        ->assertSet('named', false)
        ->assertSet('page', 1);
});

it('toggles moon sort direction on the detail page', function () {
    Livewire::test(ObjectsShow::class, ['slug' => 'planet-saturn'])
        ->assertSet('sortField', 'semiMajorAxisAu')
        ->assertSet('sortDir', 'asc')
        ->call('sortBy', 'radiusKm')
        ->assertSet('sortField', 'radiusKm')
        ->assertSet('sortDir', 'asc')
        ->call('sortBy', 'radiusKm')      // same field flips direction
        ->assertSet('sortDir', 'desc');
});

it('searches as the query changes', function () {
    Livewire::test(SearchPage::class)
        ->assertSee('Search the catalogue')   // empty state
        ->set('q', 'ceres')
        ->assertSee('Ceres');
});

it('paginates a category page', function () {
    Livewire::test(Category::class, ['kind' => 'asteroid'])
        ->assertSet('page', 1)
        ->call('$set', 'page', 2)
        ->assertSet('page', 2)
        ->assertSee('Object 0');
});

it('steps the orrery date and resets to today', function () {
    $component = Livewire::test(Orrery::class, [])
        ->call('step', 10);

    $stepped = $component->get('date');
    expect($stepped)->toBeString()->not->toBe('');

    $component->call('today');
    expect($component->get('date'))->toBe(now('UTC')->toDateString());
});
