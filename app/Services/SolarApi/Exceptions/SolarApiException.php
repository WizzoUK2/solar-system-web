<?php

declare(strict_types=1);

namespace App\Services\SolarApi\Exceptions;

use RuntimeException;

/**
 * The backend was reached but could not satisfy the request (5xx, malformed
 * payload, etc.). A 404 is NOT this — that surfaces as a null return so callers
 * can show a clean "not found" page rather than an error panel.
 */
class SolarApiException extends RuntimeException {}
