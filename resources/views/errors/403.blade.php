@php app(\App\Support\Seo::class)->title(__('Forbidden'))->noindex(); @endphp
<x-layouts.app>
    <x-error-page :code="403" :heading="__('That’s off limits')">
        {{ __('You don’t have access to this page.') }}
    </x-error-page>
</x-layouts.app>
