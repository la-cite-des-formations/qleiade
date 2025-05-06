<?php

namespace Api\Collections;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Api\Resources\QualityLabel;

class QualityLabels extends ResourceCollection
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
            'data' => QualityLabel::collection($this->collection),
            'meta' => [
                'self' => 'link-value',
            ],
        ];
    }
}
