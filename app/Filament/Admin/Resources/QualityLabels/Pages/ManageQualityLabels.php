<?php

namespace App\Filament\Admin\Resources\QualityLabels\Pages;

use App\Filament\Admin\Resources\QualityLabels\QualityLabelResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Support\Str;

class ManageQualityLabels extends ManageRecords
{
    protected static string $resource = QualityLabelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->mutateDataUsing(function (array $data): array {
                    // Ensure name is always set from label
                    if (!empty($data['label'])) {
                        $data['name'] = Str::slug($data['label']);
                    }
                    return $data;
                }),
        ];
    }
}
