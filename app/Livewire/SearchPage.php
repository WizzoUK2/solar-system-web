<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Services\SolarApi\Exceptions\SolarApiException;
use App\Services\SolarApi\SolarApiClient;
use App\Support\Seo;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

/**
 * Search results, backed by /api/v1/search. The query lives in the URL so a
 * search is a shareable, back-button-friendly link. Search-as-you-type updates
 * via a debounced live binding; with JS off the page still works as a plain
 * server-rendered results page for whatever ?q= it was loaded with.
 */
#[Layout('components.layouts.app')]
final class SearchPage extends Component
{
    #[Url(as: 'q', except: '')]
    public string $q = '';

    public function render(SolarApiClient $api): View
    {
        $query = trim($this->q);

        app(Seo::class)
            ->title($query !== '' ? __('Search: :q', ['q' => $query]) : __('Search'))
            ->description(__('Search the catalogue of solar-system objects by name, designation or discoverer.'))
            ->noindex();

        $results = [];
        $apiDown = false;

        if ($query !== '') {
            try {
                $results = $api->search($query, 40);
            } catch (SolarApiException) {
                $apiDown = true;
            }
        }

        return view('livewire.search-page', [
            'results' => $results,
            'apiDown' => $apiDown,
            'query' => $query,
        ]);
    }
}
