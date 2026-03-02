<?php

namespace App\Filament\Admin\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

abstract class BaseNavigationWidget extends StatsOverviewWidget
{
    /**
     * @return array<class-string<\Filament\Resources\Resource>>
     */
    abstract protected function getResources(): array;

    public static function isVisible(): bool
    {
        $user = Auth::user();

        if (! $user) {
            return false;
        }

        foreach ((new static())->getResources() as $resource) {
            if ($user->can('viewAny', $resource::getModel())) {
                return true;
            }
        }

        return false;
    }

    protected function getStats(): array
    {
        $user = Auth::user();
        $stats = [];

        foreach ($this->getResources() as $resource) {
            $model = $resource::getModel();

            if ($user->can('viewAny', $model)) {
                $group = $resource::getNavigationGroup();
                $color = match ($group) {
                    'RÉFÉRENTIEL' => 'info',
                    'DONNÉES' => 'success',
                    default => 'primary',
                };

                $stat = Stat::make(
                    $resource::getNavigationLabel() ?? $resource::getPluralModelLabel(),
                    $model::count()
                )
                    ->icon($resource::getNavigationIcon())
                    ->url($resource::getUrl('index'))
                    ->color($color)
                    ->view('filament.admin.widgets.navigation-card');

                if (method_exists($resource, 'getWidgetDescription')) {
                    $stat->description($resource::getWidgetDescription());
                }

                $stats[] = $stat;
            }
        }

        return $stats;
    }

    protected function getColumns(): int | array | null
    {
        return [
            'default' => 1,
            'sm' => 2,
            'md' => 3,
            'lg' => 4,
        ];
    }
}
