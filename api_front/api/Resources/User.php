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
        // Liste des permissions attendues par React
        $features = [
            'public_home',
            'public_admin',
            'public_quality_labels_audit',
            'public_quality_labels_dashboard',
        ];

        $perms = [];
        foreach ($features as $feature) {
            // Utilisation directe de can() sur le modèle pour plus de fiabilité
            $perms[$feature] = $this->resource->can($feature);
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

    /**
     * S'assure que la réponse n'est pas mise en cache par le navigateur.
     */
    public function withResponse($request, $response)
    {
        $response->header('Cache-Control', 'no-cache, no-store, must-revalidate');
    }
}
