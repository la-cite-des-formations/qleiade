<?php

namespace App\Filament\Admin\Resources\WealthTypes\Pages;

use App\Filament\Admin\Resources\WealthTypes\WealthTypeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Support\Str;

class ManageWealthTypes extends ManageRecords
{
    protected static string $resource = WealthTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->slideOver()
                ->modalHeading('Nouveau Type de preuve')
                ->mutateDataUsing(function (array $data): array {
                    if (!empty($data['label'])) {
                        $data['name'] = Str::slug($data['label']);
                    }
                    return $data;
                }),
        ];
    }
}
