<?php

namespace App\Filament\Admin\Widgets;

use App\Filament\Admin\Resources\Wealths\WealthResource;
use App\Filament\Admin\Resources\Tags\TagResource;

class DataOverviewWidget extends BaseNavigationWidget
{
    protected static ?int $sort = 20;

    protected ?string $heading = 'DONNÉES';

    protected int | string | array $columnSpan = 'full';

    protected function getResources(): array
    {
        return [
            WealthResource::class,
            TagResource::class,
        ];
    }
}
