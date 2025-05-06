<?php

namespace School\Adapters\Collections;

use Illuminate\Http\Resources\Json\ResourceCollection;
use School\Traits\Mapper;

class Groups extends ResourceCollection
{
    use Mapper;

    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'data' => $this->getData("Ypareo", "Group", $this->collection),
            'meta' => [
                'self' => 'link-value',
            ],
        ];
    }
}
