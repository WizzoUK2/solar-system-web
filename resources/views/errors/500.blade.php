@php app(\App\Support\Seo::class)->title(__('Something went wrong'))->noindex(); @endphp
<x-layouts.app>
    <x-error-page :code="500" :heading="__('Something went wrong')">
        {{ __('A problem on our end stopped this page from loading. It’s been logged. Please try again in a moment.') }}
    </x-error-page>
</x-layouts.app>
