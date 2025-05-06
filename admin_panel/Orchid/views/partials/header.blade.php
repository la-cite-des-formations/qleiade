@push('head')
    <link href="/favicon.ico" id="favicon" rel="icon">
@endpush

<p class="h2 n-m font-thin v-center">
    <x-orchid-icon path="database" />

    <span class="m-l d-none d-sm-block">
        {{ __('header_title') }}
        <small class="v-top opacity">{{ __('header_subtitle') }}</small>
    </span>
</p>
