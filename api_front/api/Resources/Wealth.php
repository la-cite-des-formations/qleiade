<?php

namespace Api\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Api\Collections\Indicators as IndicatorsCollection;

class Wealth extends JsonResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    // public static $wrap = 'data';

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $wealth = [
            'id' => $this->id,
            'name' => $this->name,
            'label' => $this->name,
            'type' => "Wealth",
            'description' => $this->description,
            'content' => $this->description,
            "indicators" => new IndicatorsCollection($this->indicators),
            // "actions" => new ActionsCollection($this->actions),
            "unit" => $this->unit->id,
            "granularity" => $this->granularity,
            "wealth_type" => $this->wealthType,
            "attachment" => $this->attachment,
            "files" => $this->files,
        ];
        return $wealth;
    }
}
