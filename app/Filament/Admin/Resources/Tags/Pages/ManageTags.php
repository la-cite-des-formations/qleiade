<?php

namespace App\Filament\Admin\Resources\Tags\Pages;

use App\Filament\Admin\Resources\Tags\TagResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Support\Str;

class ManageTags extends ManageRecords
{
    protected static string $resource = TagResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->modalHeading('Nouveau Libellé')
                ->slideOver()
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
