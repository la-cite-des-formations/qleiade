<?php

namespace School\Adapters\Ypareo\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Student extends JsonResource
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
        //nom, prenom, date de naissance, formation, groupe sur une période donnée ou une date
        // cf date inscription
        $student = [
            'id' => $this['codeApprenant'],
            'ine' => $this['codeIne'],
            'lastName' => $this['nomApprenant'],
            'birthName' => $this['nomJeuneFille'],
            'firstName' => $this['prenomApprenant'],
            'firstName2' => $this['prenom2Apprenant'],
            'birthDate' => $this['dateNaissance'],
            'birthPlace' => $this['lieuNaissance'],
            "sex" => $this['sexe'],
            "groupCode" => $this['informationsCourantes']['codeGroupe'],
            "fullName" => $this['nomApprenant'] . " " . $this['prenomApprenant'],
            "type" => "student"
        ];
        return $student;
    }
}

// "0": {
//     "codeApprenant": 772181,
//     "codeIne": "133219320KC",
//     "codeCivilite": 1,
//     "nomApprenant": "ABDALLAH DJAHA",
//     "nomJeuneFille": null,
//     "prenomApprenant": "Abdel Hakim",
//     "prenom2Apprenant": null,
//     "autresPrenoms": null,
//     "sexe": "M",
//     "dateNaissance": "23/03/2002",
//     "lieuNaissance": "MAMOUDZOU",
//     "deptNaissance": "976",
//     "codeCommuneNaissance": 2530,
//     "codePaysNaissance": 512764,
//     "adresse": {
//         "codeAdresse": 88033,
//         "codeCommune": 528,
//         "codePays": 1,
//         "adr1": "21 RUE FLANDRES DUNKERQUE",
//         "adr2": null,
//         "adr3": null,
//         "adr4": null,
//         "codeRessource": 772181,
//         "cp": "45600",
//         "email": "djaha.abdel45600@gmail.com",
//         "fax": null,
//         "province": null,
//         "tel1": "0218493365",
//         "tel2": "0766527782",
//         "typeAdresse": 70,
//         "typeRessource": 7500,
//         "ville": "SULLY SUR LOIRE",
//         "zoneLibre": null,
//         "commune": {
//             "codeCommune": 528,
//             "codeBassin": 348,
//             "codeCanton": 410,
//             "nomenclDepartement": "45",
//             "codePostal": "45600",
//             "nomenclInsee": "45315",
//             "nomCommune": "SULLY SUR LOIRE",
//             "listAdresse": null
//         },
//         "pays": {
//             "codePays": 1,
//             "formatCp": "99999",
//             "formatSiret": "99999999999999",
//             "formatTel": "##.##.##.##.##",
//             "indicatifTel": null,
//             "isProvince": 0,
//             "isSiretObligatoire": 1,
//             "libelleSiret": "Siret",
//             "nomenclPays": 1,
//             "nomenclPaysInsee": "",
//             "nomPays": "FRANCE",
//             "numOrdre": 1
//         }
//     },
//     "adresse2": null,
//     "adresse3": null,
//     "codeEtabOrigine": 138,
//     "estReconnuHandicape": 0,
//     "dateCreation": "09/04/2021",
//     "dateModification": "10/02/2023",
//     "numeroBadge": "F2EA4BC6",
//     "contactAdresse": {
//         "identifiant": 772181,
//         "titre": "Apprenant",
//         "codeCivilite": 1,
//         "nom": "ABDALLAH DJAHA",
//         "prenom": "Abdel Hakim"
//     },
//     "contactAdresse2": null,
//     "contactAdresse3": null,
//     "repLegal": null,
//     "informationsCourantes": {
//         "codeGroupe": 846591,
//         "codeQualite": 99001,
//         "codeInscription": 1101875,
//         "codeCalendrier": 1414
//     },
//     "inscriptions": [
//         {
//             "codeInscription": 1101875,
//             "codeApprenant": 772181,
//             "isInscriptionEnCours": 1,
//             "isInscriptionFc": 0,
//             "isIndividuel": 0,
//             "codePeriode": 15,
//             "codeSite": 28,
//             "site": {
//                 "codeSite": 28,
//                 "nomFormeJuridique": "ASSOCIATION",
//                 "nomSite": "LA CITE DES FORMATIONS",
//                 "abregeSite": "CFA",
//                 "etenduSite": null,
//                 "regroupementSite": "",
//                 "adresse": {
//                     "codeAdresse": 2,
//                     "codeCommune": 2258,
//                     "codePays": 1,
//                     "adr1": "8 Allée Roger Lecotté",
//                     "adr2": null,
//                     "adr3": null,
//                     "adr4": null,
//                     "codeRessource": 28,
//                     "cp": "37100",
//                     "email": "contact@citeformations.com",
//                     "fax": "",
//                     "province": "",
//                     "tel1": "0247885100",
//                     "tel2": null,
//                     "typeAdresse": 70,
//                     "typeRessource": 114500,
//                     "ville": "TOURS",
//                     "zoneLibre": null,
//                     "commune": {
//                         "codeCommune": 2258,
//                         "codeBassin": 215,
//                         "codeCanton": 1442,
//                         "nomenclDepartement": "37",
//                         "codePostal": "37100",
//                         "nomenclInsee": "37261",
//                         "nomCommune": "TOURS",
//                         "listAdresse": null
//                     },
//                     "pays": {
//                         "codePays": 1,
//                         "formatCp": "99999",
//                         "formatSiret": "99999999999999",
//                         "formatTel": "##.##.##.##.##",
//                         "indicatifTel": null,
//                         "isProvince": 0,
//                         "isSiretObligatoire": 1,
//                         "libelleSiret": "Siret",
//                         "nomenclPays": 1,
//                         "nomenclPaysInsee": "",
//                         "nomPays": "FRANCE",
//                         "numOrdre": 1
//                     }
//                 },
//                 "rne": "0370825W",
//                 "naf": null,
//                 "siret": "32570588700033",
//                 "numeroExistence": "24370147037",
//                 "civiliteDirigeant": "Mme",
//                 "nomDirigeant": "BODIN",
//                 "prenomDirigeant": "Marie-Josèphe",
//                 "plusUtilise": 0,
//                 "listeRib": [
//                     {
//                         "codeRib": 13183,
//                         "codeBanque": "18707",
//                         "codeGuichet": "00610",
//                         "numeroCompte": "00119340065",
//                         "cleRib": "65",
//                         "ibanPays": "FR76",
//                         "bic": "CCBPFRPPVER",
//                         "titulaire": "AFP CFA VILLE DE TOURS",
//                         "numEmetteur": null,
//                         "codeRemettant": null,
//                         "sacdPatronym": null,
//                         "sacdUai": null,
//                         "teneurCodique": null,
//                         "teneurIban": null,
//                         "teneurNom": null,
//                         "ibanCpt": "18707006100011934006565",
//                         "dateSignatureMandatPrelev": null,
//                         "idCreancierPrelev": "",
//                         "rumPrelev": null,
//                         "typeSequencePrelev": null,
//                         "nbTypeSequencePrelev": 0,
//                         "isAutorisePrelevement": 0,
//                         "isSacd": 0
//                     }
//                 ]
//             },
//             "codeStatut": 2,
//             "statut": {
//                 "codeStatut": 2,
//                 "nomStatut": "Apprenti",
//                 "abregeStatut": "N01",
//                 "isFacturableNpec": 1,
//                 "isFacturableContrat": 0,
//                 "isFacturableFormation": 0,
//                 "plusUtilise": 0
//             },
//             "codeSituation": 14,
//             "situation": {
//                 "codeSituation": 14,
//                 "nomSituation": "APPRENTI TH 2EME ANNEE",
//                 "abregeSituation": null,
//                 "codeStatut": 2,
//                 "nomStatut": "Apprenti",
//                 "codeDiplomePrepare": 8,
//                 "nomDiplomePrepare": "TITRE HOMOLOGUE NIVEAU 4",
//                 "codeAnnee": 3,
//                 "nomAnnee": "2ème année"
//             },
//             "codeAnnee": 3,
//             "annee": {
//                 "codeAnnee": 3,
//                 "nomAnnee": "2ème année",
//                 "abregeAnnee": "2A",
//                 "numeroAnnee": 2
//             },
//             "codeFormation": 3125,
//             "formation": {
//                 "codeFormation": 3125,
//                 "nomFormation": "TP TECHNICIEN DE TRAVAUX SUR ASCENSEUR",
//                 "codeEn": "46X25001",
//                 "etenduFormation": "TP TECHNICIEN DE TRAVAUX SUR ASCENSEUR",
//                 "abregeFormation": "46X25001",
//                 "dureeMois": 24,
//                 "dureeJours": null,
//                 "dureeHeures": 1430,
//                 "codeSecteurActivite": 8,
//                 "nomSecteurActivite": "ASCENSEURS",
//                 "codePersonnel": null,
//                 "prixVente": null,
//                 "uniteFacturation": null,
//                 "nomUniteFacturation": null,
//                 "ensembleFinance": null,
//                 "nomEnsembleFinance": null,
//                 "modeEvaluation": null,
//                 "natureAction": null,
//                 "description": null,
//                 "plusUtilise": 0,
//                 "estFormationLongue": 1,
//                 "estFormationCourte": 1,
//                 "planDeFormation": null,
//                 "diplome": {
//                     "codeDiplome": 8,
//                     "nomDiplome": "TITRE HOMOLOGUE NIVEAU 4",
//                     "abregeDiplome": "4B",
//                     "nomenclDiplome": "46Q",
//                     "nomenclNiveau": 4,
//                     "nomenclCerfa2012": null,
//                     "nomenclCerfa2020": null
//                 },
//                 "niveau": 4,
//                 "npec": {
//                     "npecMax": 11000,
//                     "npecMoyen": 10399,
//                     "npecMin": 9798
//                 },
//                 "numeroCompteGeneral": "70610000",
//                 "numeroCompteAnalytique": "NAP01AS4BREMO",
//                 "numeroCompteService": null,
//                 "certification": {
//                     "typeCertification": "RNCP",
//                     "nomenclature": "35292",
//                     "nom": "Technicien de travaux sur ascenseurs"
//                 }
//             },
//             "dateDepart": null
//         }
//     ],
//     "informationsFinancieres": null,
//     "codeNationalite": 0,
//     "codeOrigineScolaire": 3064,
//     "codeDiplomeObtenu": 10
// },
