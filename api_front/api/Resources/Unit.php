<?php

namespace Api\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Unit extends JsonResource
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
            'linkLabel' => $this->name,
            'type' => 'Unit'
        ];
        return $proc;
    }
}
