@php app(\App\Support\Seo::class)->title(__('Down for maintenance'))->noindex(); @endphp
<x-layouts.app>
    <x-error-page :code="503" :heading="__('Briefly down for maintenance')">
        {{ __('We’re doing some quick work behind the scenes. Please check back shortly.') }}
    </x-error-page>
</x-layouts.app>
