<?php

namespace Api\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Api\Collections\Unit as UnitCollection;
use Api\Collections\Wealths as WealthCollection;

class Action extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $proc = [
            'id' => $this->id,
            'name' => $this->name,
            'label' => $this->label,
            'order' => $this->order,
            'unit' => new UnitCollection($this->unit),
            'wealths' => new WealthCollection($this->wealths),
            'type' => 'Action'
        ];
        return $proc;
    }
}
