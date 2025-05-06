<?php

namespace Api\Collections;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Api\Resources\Unit as Proc;

class Unit extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return Proc::collection($this->collection);
    }
}
