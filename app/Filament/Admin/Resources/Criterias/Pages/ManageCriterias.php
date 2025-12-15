<?php

namespace App\Filament\Admin\Resources\Criterias\Pages;

use App\Filament\Admin\Resources\Criterias\CriteriaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageCriterias extends ManageRecords
{
    protected static string $resource = CriteriaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->modalHeading('Nouveau Critère'),
        ];
    }
}
