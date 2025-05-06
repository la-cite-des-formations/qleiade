<?php

namespace Api\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class audit extends JsonResource
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
        $content = json_decode($this->content, true);
        $audit = [
            'id' => $this->id,
            'name' => $content["name"],
            'audit_type' => $content["type"],
            'date' => $content["date"],
            "sample" => $content["sample"],
        ];
        return $audit;
    }
}
