@php
    $url = $getUrl();
    $tag = $url ? 'a' : 'div';
    $icon = $getIcon();
    $color = $getColor() ?? 'gray';

    if (is_string($icon) && str_starts_with($icon, 'heroicon-o-')) {
        $icon = str_replace('heroicon-o-', 'heroicon-s-', $icon);
    }
@endphp

<{!! $tag !!}
    @if ($url) {{ \Filament\Support\generate_href_html($url, $shouldOpenUrlInNewTab()) }} @endif
    {{ $getExtraAttributeBag()->class(['fi-wi-stats-overview-stat fi-wi-navigation-card', "fi-color-{$color}"]) }}>
    <div class="fi-wi-stats-overview-stat-content">
        <div class="fi-sc-flex fi-align-between fi-from-default fi-vertical-align-center fi-dense">
            <div class="fi-wi-stats-overview-stat-label-ctn">
                @if ($icon)
                    <x-filament::icon :icon="$icon" class="fi-wi-stats-overview-stat-icon fi-nav-stat-icon" />
                @endif

                <span class="fi-wi-stats-overview-stat-label fi-nav-stat-title">
                    {{ $getLabel() }}
                </span>
            </div>

            <x-filament::badge :color="$color" class="fi-nav-stat-badge">
                {{ $getValue() }}
            </x-filament::badge>
        </div>

        @if ($description = $getDescription())
            <div class="fi-wi-stats-overview-stat-description">
                {{ $description }}
            </div>
        @endif
    </div>
    </{!! $tag !!}>
