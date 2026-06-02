<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Services\SolarApi\Data\Position;
use App\Services\SolarApi\SolarApiClient;
use App\Support\Seo;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

/**
 * A 2D top-down orrery: the inner solar system out to Pluto, positioned for a
 * chosen date from the backend's /positions endpoint. A flourish, kept light —
 * positions come from one concurrent batch and the drawing is plain SVG.
 *
 * Distances use a square-root radial scale so Mercury and Pluto are both
 * legible on one canvas; it is deliberately not to scale.
 */
#[Layout('components.layouts.app')]
final class Orrery extends Component
{
    /**
     * The bodies plotted, in orbit order, with a representative colour for
     * rendering (the /positions endpoint carries no colour, and this is a
     * fixed curated set, so colours live here rather than costing extra calls).
     *
     * @var array<string,array{label:string,colour:string}>
     */
    private const BODIES = [
        'planet-mercury' => ['label' => 'Mercury', 'colour' => '#9b8a7a'],
        'planet-venus' => ['label' => 'Venus', 'colour' => '#e8cda2'],
        'planet-earth' => ['label' => 'Earth', 'colour' => '#6b93d6'],
        'planet-mars' => ['label' => 'Mars', 'colour' => '#c1440e'],
        'planet-jupiter' => ['label' => 'Jupiter', 'colour' => '#d8ca9d'],
        'planet-saturn' => ['label' => 'Saturn', 'colour' => '#ead6a0'],
        'planet-uranus' => ['label' => 'Uranus', 'colour' => '#b9e4e7'],
        'planet-neptune' => ['label' => 'Neptune', 'colour' => '#5b76e0'],
        'dwarf-ceres' => ['label' => 'Ceres', 'colour' => '#8c8377'],
        'dwarf-pluto' => ['label' => 'Pluto', 'colour' => '#c9b8a0'],
    ];

    #[Url(as: 'date', except: '')]
    public string $date = '';

    public function mount(): void
    {
        $this->date = $this->normalisedDate($this->date);
    }

    public function updatedDate(): void
    {
        $this->date = $this->normalisedDate($this->date);
    }

    public function today(): void
    {
        $this->date = CarbonImmutable::now('UTC')->toDateString();
    }

    public function step(int $days): void
    {
        $this->date = CarbonImmutable::parse($this->date)->addDays($days)->toDateString();
    }

    public function render(SolarApiClient $api): View
    {
        app(Seo::class)
            ->title(__('Orrery'))
            ->description(__('An interactive 2D map of the solar system for any date — the planets and Pluto, positioned from live ephemeris data.'));

        $apiDown = ! $api->reachable();
        $bodies = [];

        if (! $apiDown) {
            $positions = $api->positionsBatch(array_keys(self::BODIES), $this->date);
            $bodies = $this->plot($positions);
        }

        return view('livewire.orrery', [
            'apiDown' => $apiDown,
            'bodies' => $bodies,
            'date' => $this->date,
            'prettyDate' => CarbonImmutable::parse($this->date)->isoFormat('D MMMM YYYY'),
        ]);
    }

    /**
     * Turn positions into SVG-space points on a 600×600 canvas.
     *
     * @param  array<string,?Position>  $positions
     * @return list<array{slug:string,label:string,colour:string,cx:float,cy:float,r:float,distance:float}>
     */
    private function plot(array $positions): array
    {
        $centre = 300.0;
        $maxRadius = 270.0;

        // Scale to the furthest body we actually got a position for.
        $maxDistance = 0.0;
        foreach ($positions as $position) {
            if ($position?->distanceFromSunAu !== null) {
                $maxDistance = max($maxDistance, $position->distanceFromSunAu);
            }
        }
        if ($maxDistance <= 0.0) {
            return [];
        }

        $bodies = [];
        foreach (self::BODIES as $id => $meta) {
            $position = $positions[$id] ?? null;
            if ($position?->xAu === null || $position->yAu === null || $position->distanceFromSunAu === null) {
                continue;
            }

            // Square-root radial scale spreads inner and outer worlds legibly.
            $radius = sqrt($position->distanceFromSunAu) / sqrt($maxDistance) * $maxRadius;
            $angle = atan2($position->yAu, $position->xAu);

            $bodies[] = [
                'slug' => $id,
                'label' => $meta['label'],
                'colour' => $meta['colour'],
                'cx' => round($centre + $radius * cos($angle), 2),
                'cy' => round($centre - $radius * sin($angle), 2), // flip y for screen space
                'r' => round($radius, 2),
                'distance' => $position->distanceFromSunAu,
            ];
        }

        return $bodies;
    }

    private function normalisedDate(string $date): string
    {
        try {
            return CarbonImmutable::parse($date !== '' ? $date : 'now')->toDateString();
        } catch (\Throwable) {
            return CarbonImmutable::now('UTC')->toDateString();
        }
    }
}
