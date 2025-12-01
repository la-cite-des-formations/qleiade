<?php

namespace App\Filament\Resources\QualityLabels\Pages;

use App\Filament\Resources\QualityLabels\QualityLabelResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageQualityLabels extends ManageRecords
{
    protected static string $resource = QualityLabelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Nouveau Label Qualité')
                ->modalWidth('2xl'),
        ];
    }
}
