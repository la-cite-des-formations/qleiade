<?php

namespace App\Filament\Admin\Resources\Stages\Pages;

use App\Filament\Admin\Resources\Stages\StageResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Support\Str;

class ManageStages extends ManageRecords
{
    protected static string $resource = StageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->slideOver()
                ->modalHeading('Nouvelle Étape')
                ->mutateDataUsing(function (array $data): array {
                    if (!empty($data['label'])) {
                        $data['name'] = Str::slug($data['label']);
                    }
                    return $data;
                }),
        ];
    }
}
