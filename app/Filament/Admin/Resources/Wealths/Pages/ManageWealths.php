<?php

namespace App\Filament\Admin\Resources\Wealths\Pages;

use App\Filament\Admin\Resources\Wealths\WealthResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageWealths extends ManageRecords
{
    protected static string $resource = WealthResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->modalHeading('Nouvelle Preuve')
                ->slideOver()
                ->using(fn (array $data, string $model) => WealthResource::saveRelationships(new $model, $data)),
        ];
    }
}
