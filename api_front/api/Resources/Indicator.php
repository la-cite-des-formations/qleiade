<?php

namespace Api\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Indicator extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
        'id' => $this->id,
        'name' => $this->name,
        'label' => $this->label,
        'number' => $this->number,
        'description' => $this->description,
        'conformity_level_expected' => $this->conformity_level_expected,
        'order' => $this->order,
        'type' => 'Indicator',

        'pivot' => $this->pivot ? [
            'is_essential' => (bool) $this->pivot->is_essential,
        ] : null,

        'criteria' => new Criteria($this->whenLoaded('criteria')),
        'qualityLabel' => $this->qualityLabel->label ?? null,
        ];
    }
}
