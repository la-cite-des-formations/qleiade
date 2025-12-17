<?php

namespace App\Filament\Admin\Resources\Indicators\Pages;

use App\Filament\Admin\Resources\Indicators\IndicatorResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Support\Str;
use Models\Criteria;
use Models\Indicator;

class ManageIndicators extends ManageRecords
{
    protected static string $resource = IndicatorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->mountUsing(function ($form, ManageIndicators $livewire) {
                    $form->fill();

                    // Get filter state from the 'structure' filter
                    $filterState = $livewire->getTableFilterState('structure');
                    $qualityLabelId = $filterState['quality_label_id'] ?? null;
                    $criteriaId = $filterState['criteria_id'] ?? null;

                    $fillData = [];

                    if ($qualityLabelId) {
                        $fillData['quality_label_id'] = $qualityLabelId;
                    }

                    if ($criteriaId) {
                        $fillData['criteria_id'] = $criteriaId;
                        
                        // Calculate next number for this criteria
                        $lastNumber = Indicator::where('criteria_id', $criteriaId)->max('number');
                        $next = $lastNumber ? intval($lastNumber) + 1 : 1;
                        $fillData['number'] = str_pad($next, 2, '0', STR_PAD_LEFT);
                    }

                    if (!empty($fillData)) {
                        $form->fill($fillData);
                    }
                })
                ->mutateDataUsing(function (array $data): array {
                    // Ensure name is always set from number and criteria_id
                    if (!empty($data['number']) && !empty($data['criteria_id'])) {
                        $criteria = Criteria::find($data['criteria_id']);
                        $data['name'] = $criteria->order . '-' . $data['number'];
                    }
                    return $data;
                }),
        ];
    }
}
