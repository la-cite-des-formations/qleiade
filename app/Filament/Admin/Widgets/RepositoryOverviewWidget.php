<?php

namespace App\Filament\Admin\Widgets;

use App\Filament\Admin\Resources\QualityLabels\QualityLabelResource;
use App\Filament\Admin\Resources\Actions\ActionResource;

class RepositoryOverviewWidget extends BaseNavigationWidget
{
    protected static ?int $sort = 10;

    protected ?string $heading = 'RÉFÉRENTIEL';

    protected int | string | array $columnSpan = 'full';

    protected function getResources(): array
    {
        return [
            QualityLabelResource::class,
            ActionResource::class,
        ];
    }
}
