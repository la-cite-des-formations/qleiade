@props([
    'active' => false,
    'activeChildItems' => false,
    'activeIcon' => null,
    'badge' => null,
    'badgeColor' => null,
    'badgeTooltip' => null,
    'childItems' => [],
    'first' => false,
    'grouped' => false,
    'icon' => null,
    'label' => null,
    'last' => false,
    'shouldOpenUrlInNewTab' => false,
    'sidebarCollapsible' => true,
    'subGrouped' => false,
    'subNavigation' => false,
    'url',
])

@php
    use Filament\Support\Enums\IconSize;
    use Filament\Support\View\ComponentAttributeBag;
    use Illuminate\Contracts\Support\Htmlable;

    $sidebarCollapsible = $sidebarCollapsible && filament()->isSidebarCollapsibleOnDesktop();
    $tooltip = $label ?? $slot;
    $labelText = ($tooltip instanceof Htmlable) ? trim(html_entity_decode(strip_tags($tooltip->toHtml()), ENT_QUOTES | ENT_HTML5, 'UTF-8')) : $tooltip;
    $paddingStart = $subGrouped ? '2.5rem' : ($grouped ? '1.5rem' : null);
    $iconAttributes = new ComponentAttributeBag([]);
    $iconAttributes = $iconAttributes->class(['fi-sidebar-item-icon']);
@endphp

<li
    {{ $attributes->class([
        'fi-sidebar-item',
        'fi-active' => $active,
        'fi-sidebar-item-has-active-child-items' => $activeChildItems,
        'fi-sidebar-item-has-url' => filled($url),
    ]) }}>
    <a {{ \Filament\Support\generate_href_html($url, $shouldOpenUrlInNewTab) }}
        @if ($active)
            aria-current="page"
        @endif
        x-on:click="window.matchMedia(`(max-width: 1024px)`).matches && $store.sidebar.close()"
        @if ($sidebarCollapsible && (! $subNavigation))
            x-bind:aria-label="$store.sidebar.isOpen ? null : @js($labelText)"
            x-data="filamentSidebarTooltip"
            x-tooltip="
                getTooltip({
                    content: @js(($tooltip instanceof Htmlable) ? $tooltip->toHtml() : $tooltip),
                    allowHTML: @js($tooltip instanceof Htmlable),
                })
            "
        @endif
        class="fi-sidebar-item-btn"
        @if ($paddingStart) style="padding-inline-start: {{ $paddingStart }}" @endif>
        @if (filled($icon))
            {{ \Filament\Support\generate_icon_html(
                $active && $activeIcon ? $activeIcon : $icon,
                attributes: $iconAttributes,
                size: IconSize::Large,
            ) }}
        @endif

        <span
            @if ($sidebarCollapsible && !$subNavigation)
                x-show="$store.sidebar.isOpen"
                x-transition:enter="fi-transition-enter"
                x-transition:enter-start="fi-transition-enter-start"
                x-transition:enter-end="fi-transition-enter-end"
            @endif
            class="fi-sidebar-item-label">
            {{ $label ?? $slot }}
        </span>

        @if (filled($badge))
            <span
                @if ($sidebarCollapsible && !$subNavigation)
                    x-show="$store.sidebar.isOpen"
                    x-transition:enter="fi-transition-enter"
                    x-transition:enter-start="fi-transition-enter-start"
                    x-transition:enter-end="fi-transition-enter-end"
                @endif
                class="fi-sidebar-item-badge-ctn">
                <x-filament::badge :color="$badgeColor" :tooltip="$badgeTooltip">
                    {{ $badge }}
                </x-filament::badge>
            </span>
        @endif
    </a>

    @if (($active || $activeChildItems) && $childItems)
        <ul class="fi-sidebar-sub-group-items">
            @foreach ($childItems as $childItem)
                @php
                    $isChildItemChildItemsActive = method_exists($childItem, 'hasActiveChildItems')
                        ? $childItem->hasActiveChildItems()
                        : $childItem->isChildItemsActive();
                    $isChildActive = !$isChildItemChildItemsActive && $childItem->isActive();
                    $childItemActiveIcon = $childItem->getActiveIcon();
                    $childItemBadge = $childItem->getBadge();
                    $childItemBadgeColor = method_exists($childItem, 'getBadgeColor') && count((new \ReflectionMethod($childItem, 'getBadgeColor'))->getParameters()) > 0
                        ? $childItem->getBadgeColor($childItemBadge)
                        : $childItem->getBadgeColor();
                    $childItemBadgeTooltip = method_exists($childItem, 'getBadgeTooltip') && count((new \ReflectionMethod($childItem, 'getBadgeTooltip'))->getParameters()) > 0
                        ? $childItem->getBadgeTooltip($childItemBadge)
                        : $childItem->getBadgeTooltip();
                    $childItemIcon = $childItem->getIcon();
                    $shouldChildItemOpenUrlInNewTab = $childItem->shouldOpenUrlInNewTab();
                    $childItemUrl = $childItem->getUrl();
                    $childItemExtraAttributes = method_exists($childItem, 'getExtraAttributeBag')
                        ? $childItem->getExtraAttributeBag()
                        : new ComponentAttributeBag([]);
                @endphp

                <x-filament-panels::sidebar.item
                    :active="$isChildActive"
                    :active-child-items="$isChildItemChildItemsActive"
                    :active-icon="$childItemActiveIcon"
                    :badge="$childItemBadge"
                    :badge-color="$childItemBadgeColor"
                    :badge-tooltip="$childItemBadgeTooltip"
                    :first="$loop->first"
                    grouped
                    :icon="$childItemIcon"
                    :label="$childItem->getLabel()"
                    :last="$loop->last"
                    :should-open-url-in-new-tab="$shouldChildItemOpenUrlInNewTab"
                    sub-grouped
                    :sub-navigation="$subNavigation"
                    :url="$childItemUrl"
                    :attributes="\Filament\Support\prepare_inherited_attributes($childItemExtraAttributes)">
                    {{ $childItem->getLabel() }}
                </x-filament-panels::sidebar.item>
            @endforeach
        </ul>
    @endif
</li>
