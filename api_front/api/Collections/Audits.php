<?php

namespace Api\Collections;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Api\Resources\Audit;

class Audits extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'data' => Audit::collection($this->collection),
            'meta' => [
                'self' => 'link-value',
            ],
        ];
    }
}
