<?php

namespace School\Adapters\Collections;

use Illuminate\Http\Resources\Json\ResourceCollection;
use School\Traits\Mapper;

class Periods extends ResourceCollection
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
        $data = $this->getData("Ypareo", "Period", $this->collection);
        $res = $data;
        if (!$request->input('internal')) {
            $res = [
                'data' => $data,
                'meta' => [
                    'self' => 'link-value',
                ],
            ];
        }
        return $res;
    }
}
