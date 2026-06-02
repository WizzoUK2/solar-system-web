<?php

use App\Console\Commands\WarmCache;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Warm the hot caches shortly after the backend's nightly refresh, and again
// midday so an idle afternoon never serves a cold first hit. Requires the Forge
// scheduler (php artisan schedule:run every minute) — see DEPLOYMENT.md.
Schedule::command(WarmCache::class)->dailyAt('04:30')->onOneServer();
Schedule::command(WarmCache::class)->dailyAt('12:30')->onOneServer();
