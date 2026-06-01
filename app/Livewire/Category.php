<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Services\SolarApi\Data\Paginated;
use App\Services\SolarApi\Exceptions\SolarApiException;
use App\Services\SolarApi\SolarApiClient;
use App\Support\Seo;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

/**
 * A category landing page — a filtered, copy-led view over the catalogue. One
 * component drives /dwarf-planets, /asteroids, /comets and /tnos; the `kind`
 * comes from the route definition.
 */
#[Layout('components.layouts.app')]
final class Category extends Component
{
    private const PER_PAGE = 24;

    public string $kind;

    #[Url(as: 'page', except: 1)]
    public int $page = 1;

    public function mount(string $kind): void
    {
        $this->kind = $kind;
    }

    public function render(SolarApiClient $api)
    {
        $copy = $this->copy();

        app(Seo::class)->title($copy['title'])->description($copy['lead']);

        $page = max(1, $this->page);
        $offset = ($page - 1) * self::PER_PAGE;

        $apiDown = false;
        $results = new Paginated([], self::PER_PAGE, $offset, false);

        try {
            if ($this->kind === 'dwarf_planet') {
                // Small, curated set — show them all, candidates included.
                $items = $api->dwarfPlanets(true);
                $results = new Paginated($items, max(self::PER_PAGE, count($items)), 0, false);
            } else {
                $results = $api->objects(['type' => $this->kind], self::PER_PAGE, $offset);
            }
        } catch (SolarApiException) {
            $apiDown = true;
        }

        return view('livewire.category', [
            'results' => $results,
            'apiDown' => $apiDown,
            'copy' => $copy,
            'paginated' => $this->kind !== 'dwarf_planet',
        ]);
    }

    /** @return array{title:string,eyebrow:string,lead:string} */
    private function copy(): array
    {
        return match ($this->kind) {
            'dwarf_planet' => [
                'title' => __('Dwarf planets'),
                'eyebrow' => __('Rounded by their own gravity'),
                'lead' => __('Worlds massive enough to pull themselves round, but which never cleared their orbital neighbourhood — Ceres in the asteroid belt, and Pluto, Eris, Haumea and Makemake out beyond Neptune. Candidates are included.'),
            ],
            'asteroid' => [
                'title' => __('Asteroids'),
                'eyebrow' => __('Rocky remnants'),
                'lead' => __('The rocky leftovers of planet formation — most circling the Sun in the main belt between Mars and Jupiter, some swinging close to Earth.'),
            ],
            'comet' => [
                'title' => __('Comets'),
                'eyebrow' => __('Icy visitors'),
                'lead' => __('Balls of ice and dust on long, elongated orbits that grow tails as they near the Sun — from short-period regulars like Halley to one-time passers-by.'),
            ],
            'tno' => [
                'title' => __('Trans-Neptunian objects'),
                'eyebrow' => __('Beyond Neptune'),
                'lead' => __('The frozen bodies of the outer system — the Kuiper Belt, the scattered disc and the centaurs that wander between the giant planets.'),
            ],
            default => [
                'title' => __('Objects'),
                'eyebrow' => __('Catalogue'),
                'lead' => __('A selection from the catalogue.'),
            ],
        };
    }
}
