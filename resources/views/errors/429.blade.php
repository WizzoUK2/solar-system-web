@php app(\App\Support\Seo::class)->title(__('Too many requests'))->noindex(); @endphp
<x-layouts.app>
    <x-error-page :code="429" :heading="__('Slow down a moment')">
        {{ __('You’ve made a lot of requests in a short time. Please wait a few seconds and try again.') }}
    </x-error-page>
</x-layouts.app>
