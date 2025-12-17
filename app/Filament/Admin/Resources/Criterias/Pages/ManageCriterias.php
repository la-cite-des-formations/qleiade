<?php

namespace App\Filament\Admin\Resources\Criterias\Pages;

use App\Filament\Admin\Resources\Criterias\CriteriaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

use Illuminate\Support\Str;

class ManageCriterias extends ManageRecords
{
    protected static string $resource = CriteriaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->modalHeading('Nouveau Critère')
                ->mountUsing(function ($form, ManageCriterias $livewire) {
                    $form->fill();

                    $filterState = $livewire->getTableFilterState('quality_label_id');
                    $qualityLabelId = is_array($filterState) ? ($filterState['value'] ?? null) : $filterState;

                    if ($qualityLabelId) {
                        $model = CriteriaResource::getModel();
                        $maxOrder = $model::where('quality_label_id', $qualityLabelId)->max('order');
                        $newOrder = $maxOrder + 1;

                        $form->fill([
                            'quality_label_id' => $qualityLabelId,
                            'order' => $newOrder,
                            'label' => "Critère $newOrder",
                        ]);
                    }
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
