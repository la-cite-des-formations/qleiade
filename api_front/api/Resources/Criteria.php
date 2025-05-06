<?php

namespace Api\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Criteria extends JsonResource
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
        // title:"Administrer",
        // description:"Administrer les preuves",
        // path:"/admin",
        // linkLabel : "Administration",
        // type : "menu"
        $qualityLabel = [
            'id' => $this->id,
            'title' => $this->label,
            'type' => "qualityLabel",
            'description' => $this->description,
            'linkLabel' => $this->label,
            'items' => $this->criterias
        ];
        return $qualityLabel;
    }
}
