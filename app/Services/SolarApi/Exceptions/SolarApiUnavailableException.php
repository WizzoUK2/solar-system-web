<?php

declare(strict_types=1);

namespace App\Services\SolarApi\Exceptions;

/**
 * The backend could not be reached at all — connection refused, DNS failure,
 * or timeout. Pages catch this and render a calm degradation panel inline
 * while keeping the rest of the response intact.
 */
class SolarApiUnavailableException extends SolarApiException {}
