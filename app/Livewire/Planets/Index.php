<?php

declare(strict_types=1);

namespace App\Livewire\Planets;

use App\Services\SolarApi\Exceptions\SolarApiException;
use App\Services\SolarApi\SolarApiClient;
use App\Support\Seo;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
final class Index extends Component
{
    public function render(SolarApiClient $api)
    {
        app(Seo::class)
            ->title(__('The planets'))
            ->description(__('The eight planets of the solar system, in order from the Sun — size, orbit and a way in to each one.'));

        $planets = [];
        $apiDown = false;

        try {
            // The eight planets, already ordered by semi-major axis upstream.
            $planets = $api->objects(['type' => 'planet'], 12, 0)->items;
        } catch (SolarApiException) {
            $apiDown = true;
        }

        return view('livewire.planets.index', [
            'planets' => $planets,
            'apiDown' => $apiDown,
        ]);
    }
}
