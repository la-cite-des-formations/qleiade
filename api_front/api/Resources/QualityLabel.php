<?php

namespace Api\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class QualityLabel extends JsonResource
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

        // Load images with attachment relationship
        $this->load('attachment');
        $url = "";
        if ($this->attachment()->first()) {
            $url = $this->attachment()->first()->url();
            // dd($url);
        }

        $qualityLabel = [
            'id' => $this->id,
            'title' => $this->label,
            'type' => "QualityLabel",
            'description' => $this->description,
            'linkLabel' => $this->label,
            'size' => count($this->indicators),
            'image' => $url,
        ];
        return $qualityLabel;
    }
}
