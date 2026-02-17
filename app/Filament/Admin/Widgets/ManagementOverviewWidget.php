<?php

namespace App\Filament\Admin\Widgets;

use App\Filament\Admin\Resources\Users\UserResource;
use App\Filament\Admin\Resources\Roles\RoleResource;

class ManagementOverviewWidget extends BaseNavigationWidget
{
    protected static ?int $sort = 30;

    protected ?string $heading = 'GESTION';

    protected int | string | array $columnSpan = 'full';

    protected function getResources(): array
    {
        return [
            UserResource::class,
            RoleResource::class,
        ];
    }
}
