<?php

declare(strict_types=1);

namespace App\Livewire\Objects;

use App\Services\SolarApi\Data\Paginated;
use App\Services\SolarApi\Exceptions\SolarApiException;
use App\Services\SolarApi\SolarApiClient;
use App\Support\ObjectType;
use App\Support\Seo;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

/**
 * Browseable, filterable catalogue. All filter and page state lives in the URL
 * (via #[Url]) so every view is linkable and back-button friendly — no hidden
 * cookie state. Paging is cursor-style because the backend returns no total.
 */
#[Layout('components.layouts.app')]
final class Index extends Component
{
    private const PER_PAGE = 24;

    #[Url(as: 'type', except: '')]
    public string $type = '';

    #[Url(as: 'parent', except: '')]
    public string $parent = '';

    #[Url(as: 'size', except: '')]
    public string $size = '';

    #[Url(as: 'neo', except: false)]
    public bool $neo = false;

    #[Url(as: 'named', except: false)]
    public bool $named = false;

    #[Url(as: 'page', except: 1)]
    public int $page = 1;

    /** Reset to the first page whenever a filter changes. */
    public function updated(string $property): void
    {
        if ($property !== 'page') {
            $this->page = 1;
        }
    }

    public function clearFilters(): void
    {
        $this->reset(['type', 'parent', 'size', 'neo', 'named', 'page']);
    }

    public function render(SolarApiClient $api): View
    {
        app(Seo::class)
            ->title(__('Browse all objects'))
            ->description(__('Filter the full catalogue of solar-system objects by type, parent body, size and near-Earth status.'));

        $page = max(1, $this->page);
        $offset = ($page - 1) * self::PER_PAGE;

        $apiDown = false;
        $results = new Paginated([], self::PER_PAGE, $offset, false);

        try {
            $results = $api->objects($this->filters(), self::PER_PAGE, $offset);
        } catch (SolarApiException) {
            $apiDown = true;
        }

        return view('livewire.objects.index', [
            'results' => $results,
            'apiDown' => $apiDown,
            'typeOptions' => $this->typeOptions(),
            'parentOptions' => $this->parentOptions(),
            'sizeOptions' => $this->sizeOptions(),
            'hasFilters' => $this->hasActiveFilters(),
        ]);
    }

    /** @return array<string,mixed> */
    private function filters(): array
    {
        $filters = [
            'type' => $this->type ?: null,
            'parent' => $this->parent ?: null,
            'neo' => $this->neo ?: null,
            'named_only' => $this->named ?: null,
        ];

        // Size buckets map onto the backend's radius range.
        $range = $this->sizeRange($this->size);
        $filters['min_radius_km'] = $range['min'];
        $filters['max_radius_km'] = $range['max'];

        return array_filter($filters, static fn ($v) => $v !== null);
    }

    /** @return array{min:?float,max:?float} */
    private function sizeRange(string $bucket): array
    {
        return match ($bucket) {
            'giant' => ['min' => 25000.0, 'max' => null],
            'large' => ['min' => 1000.0, 'max' => 25000.0],
            'medium' => ['min' => 100.0, 'max' => 1000.0],
            'small' => ['min' => 1.0, 'max' => 100.0],
            'tiny' => ['min' => null, 'max' => 1.0],
            default => ['min' => null, 'max' => null],
        };
    }

    /** @return array<string,string> */
    private function typeOptions(): array
    {
        $options = ['' => __('Any type')];
        foreach (ObjectType::FILTERABLE as $type) {
            $options[$type] = ObjectType::plural($type);
        }

        return $options;
    }

    /** @return array<string,string> */
    private function parentOptions(): array
    {
        return [
            '' => __('Any parent body'),
            'Sun' => __('The Sun'),
            'Mercury' => 'Mercury', 'Venus' => 'Venus', 'Earth' => 'Earth',
            'Mars' => 'Mars', 'Jupiter' => 'Jupiter', 'Saturn' => 'Saturn',
            'Uranus' => 'Uranus', 'Neptune' => 'Neptune', 'Pluto' => 'Pluto',
        ];
    }

    /** @return array<string,string> */
    private function sizeOptions(): array
    {
        return [
            '' => __('Any size'),
            'giant' => __('Giant (≥ 25,000 km radius)'),
            'large' => __('Large (1,000–25,000 km)'),
            'medium' => __('Medium (100–1,000 km)'),
            'small' => __('Small (1–100 km)'),
            'tiny' => __('Tiny (< 1 km)'),
        ];
    }

    private function hasActiveFilters(): bool
    {
        return $this->type !== '' || $this->parent !== '' || $this->size !== ''
            || $this->neo || $this->named;
    }
}
