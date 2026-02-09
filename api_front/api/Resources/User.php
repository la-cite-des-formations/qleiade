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
        // Récupérer les permissions via Spatie (permissions directes et via rôles)
        $spatiePermissions = $this->getAllPermissions()->pluck('name')->toArray();
        $perms = [];
        foreach ($spatiePermissions as $perm) {
            if (Str::startsWith($perm, 'public')) {
                $perms[$perm] = true;
            }
        }

        // Garder la compatibilité avec d'éventuelles permissions legacy si nécessaire
        if (is_array($this->permissions)) {
            foreach ($this->permissions as $key => $value) {
                if (Str::startsWith($key, "public")) {
                    $perms[$key] = $value;
                }
            }
        }

        $pr = new UnitCollection($this->units);
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
