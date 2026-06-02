<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Adds baseline security headers to every response.
 *
 * No Content-Security-Policy is set: the site relies on a couple of inline
 * scripts (the no-FOUC theme switch) and inline event handlers, so a strict
 * CSP would need refactoring first — out of scope for a read-only public site.
 *
 * HTML page caching is intentionally left to Livewire (which sets `no-store`
 * on full-page responses); the cacheable, non-Livewire surfaces — the sitemap
 * and robots.txt — set their own Cache-Control in their controllers.
 */
final class SetResponseHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $headers = [
            'X-Content-Type-Options' => 'nosniff',
            'X-Frame-Options' => 'SAMEORIGIN',
            'Referrer-Policy' => 'strict-origin-when-cross-origin',
            'Permissions-Policy' => 'geolocation=(), camera=(), microphone=(), interest-cohort=()',
            'Cross-Origin-Opener-Policy' => 'same-origin',
        ];

        foreach ($headers as $name => $value) {
            if (! $response->headers->has($name)) {
                $response->headers->set($name, $value);
            }
        }

        return $response;
    }
}
