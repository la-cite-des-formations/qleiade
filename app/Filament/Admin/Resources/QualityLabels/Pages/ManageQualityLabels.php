<?php

namespace App\Filament\Admin\Resources\QualityLabels\Pages;

use App\Filament\Admin\Resources\QualityLabels\QualityLabelResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageQualityLabels extends ManageRecords
{
    protected static string $resource = QualityLabelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
