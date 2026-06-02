<?php

declare(strict_types=1);

it('warms the hot caches', function () {
    fakeSolar();

    $this->artisan('solar:warm-cache')
        ->expectsOutputToContain('Warmed')
        ->assertSuccessful();
});

it('still succeeds (degrades) when the backend is unreachable', function () {
    fakeSolarDown();

    // A degraded backend is not a scheduler failure — the command tries and
    // reports, but exits 0 so the schedule keeps running.
    $this->artisan('solar:warm-cache')->assertSuccessful();
});
