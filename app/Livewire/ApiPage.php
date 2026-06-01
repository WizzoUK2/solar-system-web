<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Support\Links;
use App\Support\Seo;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
final class ApiPage extends Component
{
    public function render()
    {
        app(Seo::class)
            ->title(__('Use the API'))
            ->description(__('A free, read-only REST API for the solar-system catalogue — with a worked example and a link to the full OpenAPI docs.'));

        return view('livewire.api-page', [
            'baseUrl' => (string) config('services.solar.base_url'),
            'docsUrl' => Links::apiDocs(),
            'openApiUrl' => Links::openApi(),
        ]);
    }
}
