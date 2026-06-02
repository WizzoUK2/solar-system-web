<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    fakeSolar();
    Storage::fake(config('og.disk'));
});

it('renders, caches and serves a per-object share card', function () {
    $path = 'og/'.config('og.version').'/'.sha1('planet-saturn').'.png';

    $response = $this->get('/og/objects/planet-saturn.png')
        ->assertOk()
        ->assertHeader('Content-Type', 'image/png');

    expect($response->headers->get('Cache-Control'))->toContain('immutable');
    Storage::disk(config('og.disk'))->assertExists($path);

    // The bytes are a real PNG.
    expect(substr($response->getContent(), 0, 4))->toBe("\x89PNG");
});

it('serves the cached card on subsequent requests', function () {
    $this->get('/og/objects/planet-saturn.png')->assertOk();
    $this->get('/og/objects/planet-saturn.png')->assertOk()->assertHeader('Content-Type', 'image/png');
});

it('falls back to the static card for an unknown object', function () {
    $this->get('/og/objects/missing-object.png')
        ->assertOk()
        ->assertHeader('Content-Type', 'image/png');
});

it('points the object page og:image at the per-object card', function () {
    $this->get('/objects/planet-saturn')
        ->assertOk()
        ->assertSee('/og/objects/planet-saturn.png', escape: false);
});
