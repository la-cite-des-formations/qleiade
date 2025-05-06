<?php

namespace School\Adapters\Ypareo\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Formation extends JsonResource
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
        //     "codeFormation": 3108
        // +"nomFormation": "DONNEE INCONNUE"
        //     +"estFormationLongue": 1
        //     +"estFormationCourte"

        // "codeFormation" => 3115
        // "nomFormation" => "MC ACCUEIL RECEPTION"
        // "codeEn" => "01033413"
        // "etenduFormation" => "MC ACCUEIL RECEPTION"
        // "abregeFormation" => "01033413"
        // "dureeMois" => 12
        // "dureeJours" => null
        // "dureeHeures" => null
        // "codeSecteurActivite" => 2
        // "nomSecteurActivite" => "HOTELLERIE RESTAURATION"
        // "codePersonnel" => null
        // "prixVente" => null
        // "uniteFacturation" => null
        // "nomUniteFacturation" => null
        // "ensembleFinance" => null
        // "nomEnsembleFinance" => null
        // "modeEvaluation" => null
        // "natureAction" => null
        // "description" => null
        // "plusUtilise" => 0
        // "estFormationLongue" => 1
        // "estFormationCourte" => 1
        // "planDeFormation" => array:13 [
        //   0 => array:6 [ …6]
        //   1 => array:6 [ …6]
        //   2 => array:6 [ …6]
        //   3 => array:6 [ …6]
        //   4 => array:6 [ …6]
        //   5 => array:6 [ …6]
        //   6 => array:6 [ …6]
        //   7 => array:6 [ …6]
        //   8 => array:6 [ …6]
        //   9 => array:6 [ …6]
        //   10 => array:6 [ …6]
        //   11 => array:6 [ …6]
        //   12 => array:6 [ …6]
        // ]
        // "diplome" => array:7 [
        //   "codeDiplome" => 2
        //   "nomDiplome" => "MC NIVEAU 4"
        //   "abregeDiplome" => "4C"
        //   "nomenclDiplome" => "010"
        //   "nomenclNiveau" => 4
        //   "nomenclCerfa2012" => null
        //   "nomenclCerfa2020" => null
        // ]
        // "niveau" => null
        // "npec" => array:3 [
        //   "npecMax" => 8943
        //   "npecMoyen" => 7531.04
        //   "npecMin" => 6759
        // ]
        // "numeroCompteGeneral" => "70610000"
        // "numeroCompteAnalytique" => "NAP01HR4CACRE"
        // "numeroCompteService" => null
        // "certification" => array:3 [
        //   "typeCertification" => "RNCP"
        //   "nomenclature" => "6926"
        //   "nom" => "Accueil Réception"
        // ]

        $formation = [
            'id' => $this['codeFormation'],
            'title' => $this['nomFormation'],
            'label' => $this['nomFormation'],
            'code' => $this['abregeFormation'],
            'FL' => $this['estFormationLongue'],
            "FC" => $this['estFormationCourte'],
            "type" => "formation"
        ];
        return $formation;
    }
}
