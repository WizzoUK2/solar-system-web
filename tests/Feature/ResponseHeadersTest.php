<?php

declare(strict_types=1);

beforeEach(fn () => fakeSolar());

it('sends baseline security headers on every page', function () {
    $this->get('/')
        ->assertOk()
        ->assertHeader('X-Content-Type-Options', 'nosniff')
        ->assertHeader('X-Frame-Options', 'SAMEORIGIN')
        ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin')
        ->assertHeader('Permissions-Policy', 'geolocation=(), camera=(), microphone=(), interest-cohort=()');
});

it('makes the sitemap and robots.txt publicly cacheable', function () {
    expect($this->get('/sitemap.xml')->assertOk()->headers->get('Cache-Control'))
        ->toContain('public');

    expect($this->get('/robots.txt')->assertOk()->headers->get('Cache-Control'))
        ->toContain('public');
});
