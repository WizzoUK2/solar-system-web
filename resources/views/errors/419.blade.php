@php app(\App\Support\Seo::class)->title(__('Page expired'))->noindex(); @endphp
<x-layouts.app>
    <x-error-page :code="419" :heading="__('Page expired')">
        {{ __('Your session timed out. Please refresh and try again.') }}
    </x-error-page>
</x-layouts.app>
