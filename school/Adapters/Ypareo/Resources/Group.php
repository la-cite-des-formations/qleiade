<?php

namespace School\Adapters\Ypareo\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Group extends JsonResource
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
        //     {"codeGroupe": 846452,
        //     "nomGroupe": "MC TECH ASCENSEUR 2023",
        //     "abregeGroupe": "MC ASC",
        //     "etenduGroupe": "MC TECH ASCENSEUR",
        //     "codePeriode": 15,
        //     "codePersonnel": 506099,
        //     "codeSite": 28,
        //     "matieres": [
        //         {
        //             "codeMatiere": 30028,
        //             "nomMatiere": "PRATIQUE PROFESSIONNELLE",
        //             "abregeMatiere": "TP",
        //             "codeTypeMatiere": 16,
        //             "abregeTypeMatiere": "FFP",
        //             "nomTypeMatiere": "EP"
        //         },
        //         {
        //             "codeMatiere": 109566,
        //             "nomMatiere": "SUIVI du parcours",
        //             "abregeMatiere": "SUIVI",
        //             "codeTypeMatiere": 15,
        //             "abregeTypeMatiere": "FFP",
        //             "nomTypeMatiere": "EG"
        //         },
        //         {
        //             "codeMatiere": 29927,
        //             "nomMatiere": "COMMUNICATION PRO",
        //             "abregeMatiere": "COMMUNICATIO",
        //             "codeTypeMatiere": 16,
        //             "abregeTypeMatiere": "FFP",
        //             "nomTypeMatiere": "EP"
        //         },
        //         {
        //             "codeMatiere": 331339,
        //             "nomMatiere": "Electrotechnique",
        //             "abregeMatiere": "Elec",
        //             "codeTypeMatiere": 16,
        //             "abregeTypeMatiere": "FFP",
        //             "nomTypeMatiere": "EP"
        //         },
        //         {
        //             "codeMatiere": 1155012,
        //             "nomMatiere": "Solutions constructives",
        //             "abregeMatiere": "Sol const",
        //             "codeTypeMatiere": 16,
        //             "abregeTypeMatiere": "FFP",
        //             "nomTypeMatiere": "EP"
        //         },
        //         {
        //             "codeMatiere": 331342,
        //             "nomMatiere": "Hygiène et prévention des risques",
        //             "abregeMatiere": "HPR",
        //             "codeTypeMatiere": 16,
        //             "abregeTypeMatiere": "FFP",
        //             "nomTypeMatiere": "EP"
        //         },
        //         {
        //             "codeMatiere": 586522,
        //             "nomMatiere": "Introduction module",
        //             "abregeMatiere": "Intro",
        //             "codeTypeMatiere": 27,
        //             "abregeTypeMatiere": null,
        //             "nomTypeMatiere": "Module Pro"
        //         },
        //         {
        //             "codeMatiere": 1155072,
        //             "nomMatiere": "Habilitation électrique",
        //             "abregeMatiere": "HBT",
        //             "codeTypeMatiere": 16,
        //             "abregeTypeMatiere": "FFP",
        //             "nomTypeMatiere": "EP"
        //         },
        //         {
        //             "codeMatiere": 29953,
        //             "nomMatiere": "SST",
        //             "abregeMatiere": "SST",
        //             "codeTypeMatiere": 15,
        //             "abregeTypeMatiere": "FFP",
        //             "nomTypeMatiere": "EG"
        //         },
        //         {
        //             "codeMatiere": 563680,
        //             "nomMatiere": "Etude de cas / Evaluation",
        //             "abregeMatiere": null,
        //             "codeTypeMatiere": 27,
        //             "abregeTypeMatiere": null,
        //             "nomTypeMatiere": "Module Pro"
        //         },
        //         {
        //             "codeMatiere": 29858,
        //             "nomMatiere": "CULTURE PROFESSIONNELLE",
        //             "abregeMatiere": "TECHNO",
        //             "codeTypeMatiere": 16,
        //             "abregeTypeMatiere": "FFP",
        //             "nomTypeMatiere": "EP"
        //         }
        //     ],
        //     "codePersonnelGestionnaire": 1123658,
        //     "dateDebut": "29/08/2022",
        //     "dateFin": "27/08/2023",
        //     "codeFormation": 301919,
        //     "nomCategorieAction": null,
        //     "nomModeEvaluation": null,
        //     "nomModaliteEnseignement": null,
        //     "nomModaliteEntreeSortie": null,
        //     "capaciteMini": 0,
        //     "capaciteMax": 20,
        //     "nbFrequentationActives": 6,
        //     "nbReservation": null,
        //     "estSalleEnExterieur": 0,
        //     "prixDeVente": null,
        //     "uniteDeFacturation": null,
        //     "nomUniteDeFacturation": null,
        //     "ensembleFinance": null,
        //     "nomEnsembleFinance": null,
        //     "typeSousTraite": 190,
        //     "codeSteFacturation": null,
        //     "codeSiteFormation": 28,
        //     "nomenclTypeConvention": null,
        //     "isFormationLongue": 1,
        //     "isModulaire": 1,
        //     "isPlanComplexe": 1,
        //     "isBp": 0,
        //     "nomenclSpecialiteFormation": 250,
        //     "codeTypeCours": null,
        //     "coefProf": null,
        //     "codeCompte": 328,
        //     "codeCompteAnalytique": 353,
        //     "codeCompteService": null,
        //     "dateCreation": "25/06/2021",
        //     "dateModification": "19/01/2023",
        //     "numeroAnnee": 1,
        //     "libelleEtatFacture": "Facturé",
        //     "observationGenerale": null
        // }

        $group = [
            'id' => $this['codeGroupe'],
            'title' => $this['nomGroupe'],
            'label' => $this['nomGroupe'],
            'periode' => $this['codePeriode'],
            'formation' => $this['codeFormation'],
            'type' => "group"
        ];
        return $group;
    }
}
