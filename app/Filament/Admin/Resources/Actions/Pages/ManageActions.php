<?php

namespace App\Filament\Admin\Resources\Actions\Pages;

use App\Filament\Admin\Resources\Actions\ActionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Support\Str;
use Models\Action;

class ManageActions extends ManageRecords
{
    protected static string $resource = ActionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->modalWidth('2xl')
                ->modalHeading('Nouvelle Activité')
                ->mountUsing(function ($form, ManageActions $livewire) {
                    $maxOrder = Action::max('order');
                    $form->fill([
                        'order' => $maxOrder + 1,
                    ]);
                })
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
