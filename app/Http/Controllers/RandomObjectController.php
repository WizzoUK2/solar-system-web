<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\SolarApi\Exceptions\SolarApiException;
use App\Services\SolarApi\SolarApiClient;
use Illuminate\Http\RedirectResponse;

/**
 * Sends the visitor to a random catalogue object. Pulls a small page from a
 * random offset within the named objects and redirects to its detail page.
 */
final class RandomObjectController extends Controller
{
    public function __invoke(SolarApiClient $api): RedirectResponse
    {
        try {
            // Stay within the named, well-described objects for a pleasant
            // landing rather than a bare asteroid designation.
            $offset = random_int(0, 250);
            $page = $api->objects(['named_only' => true], 1, $offset);

            $object = $page->items[0] ?? null;
        } catch (SolarApiException) {
            $object = null;
        }

        if ($object === null) {
            return redirect()->route('objects.index');
        }

        return redirect()->route('objects.show', $object->slug());
    }
}
