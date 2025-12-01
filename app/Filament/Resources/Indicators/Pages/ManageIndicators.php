<?php

namespace App\Filament\Resources\Indicators\Pages;

use App\Filament\Resources\Indicators\IndicatorResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageIndicators extends ManageRecords
{
    protected static string $resource = IndicatorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Nouvel Indicateur')
                ->modalWidth('2xl'),
        ];
    }
}
