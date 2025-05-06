<?php

namespace School\Adapters\Ypareo\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Period extends JsonResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = null;

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // ["1": {
        //     "codePeriode": 16,
        //     "dateDeb": "28/08/2023",
        //     "dateFin": "25/08/2024",
        //     "nomPeriode": "2023-2024"
        // },]
        $period = [
            'id' => $this['codePeriode'],
            'name' => $this['nomPeriode'],
            'title' => $this['nomPeriode'],
            'start_date' => $this['dateDeb'],
            "end_date" => $this['dateFin'],
            "type" => "period"
        ];
        return $period;
    }
}
