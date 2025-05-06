<?php

namespace Api\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Api\Collections\Unit as UnitCollection;


class User extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $perms = Arr::where($this->permissions, function ($value, $key) {
            return Str::startsWith($key, "public");
        });

        $pr = new UnitCollection($this->unit);
        $procs = json_decode($pr->toJson());

        $user = [
            "id" => $this->id,
            "email" => $this->email,
            "email_verified_at" => $this->email_verified_at,
            "name" => $this->name,
            "permissions" => $perms,
            "unit" => $procs,
        ];
        return $user;
    }
}
