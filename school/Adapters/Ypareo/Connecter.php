<?php

namespace School\Adapters\Ypareo;


use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
//pour utilise requete api
use Illuminate\Support\Facades\Http;
//mise en cache
use Illuminate\Support\Facades\Cache;

use School\Adapters\ConnecterInterface;
use Api\Collections\Groups as GroupCollection;
use School\Adapters\Ypareo\Mapper;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Carbon\Carbon;
use COM;
use DateTimeZone;
use DateTime;

class Connecter implements ConnecterInterface
{


    protected $client;
    protected $base_uri;
    protected $token;
    protected $mapper;

    public function __construct($config = [])
    {
        $this->base_uri = config('ypareo.token');
        $this->client = new Client([
            'base_uri' => config('ypareo.base_uri'),
        ]);
        $this->token = config('ypareo.token');
    }

    /**
     * Fonction qui appel l'API Ypareo
     *
     * @return array;
     */
    protected function call($uri, $params = [])
    {
        //je vais avoir des filtres et des paramètres

        try {
            $response = $this->client->request('get', $uri, [
                'headers' => [
                    "X-auth-Token" => $this->token,
                    "content-type" => "application/json",
                ],
                'form_params' => $params
            ]);
        } catch (ClientException $e) {
            throw $e;
        }

        $data = $response->getBody();

        return $data;
    }

    public function formatResponse($response, $filter = null)
    {
        $data = json_decode($response->getContents(), true);

        if ($filter) {
            $data = Arr::where($data, function ($value) use ($filter) {
                $find = 0;
                foreach ($filter as $filterKey => $filterValue) {
                    try {
                        $thinkValue = $value[$filterKey];
                    } catch (\Throwable $th) {
                        throw 'not filtered';
                    }
                    if ($thinkValue == $filterValue) {
                        $find += 1;
                    }
                }

                return $find === count($filter);
            });
        }

        $collec = collect($data);

        return $collec;
    }

    public function makeUri($tab, $formationType, $params)
    {
        $uri = '';
        if ($tab === "apprenants") {
            if ($formationType === 'FI') {
                // $uri = "/formation-longue/apprenants?codesPeriode=" . $code_periode;
                $uri .= "/formation-longue";
            } elseif ($formationType === 'FC') {
                //$uri = "/formation-courte/".$dateDeb."/".$dateFin."/apprenants"
            }
        }

        if (!is_null($formationType)) {
            if ($formationType === 'FI') {

                // $uri = "/formation-longue/apprenants?codesPeriode=" . $code_periode;
                $uri .= "formation-longue/";
            } elseif ($formationType === 'FC') {
                // dump('formation continu');
                //$uri = "/formation-courte/".$dateDeb."/".$dateFin."/apprenants"
                //$uri = "/formation-courte/".$dateDeb."/".$dateFin."/groupes"
            }
        }

        $uri .= $tab;

        //add params
        if (count($params) > 0) {
            $uri .= "?";

            //?param1=value1&param2=value2...
            $i = 0;
            foreach ($params as $key => $value) {
                # code...

                $uri .= $key . "=" . $value;
                if ($i < count($params) - 1) {
                    $uri .= "&";
                }
                $i++;
            }
        }
        return $uri;
    }

    /**
     * get
     *
     * @param [type] $tab
     * @param boolean $filterResponse
     * @param array $where
     * @param array $params
     * @param array $filters
     * @param string $formationType
     * @return \Illuminate\Support\Collection $data
     */
    public function get($uri = "", $tab = null, $filterResponse = false, $where = null, $params = [], $filters = [], $formationType = null)
    {
        //gestion des paramètres
        if ($uri === "" && is_null($tab)) {
            abort(500, 'ynternal error');
        }
        if ($uri === "") {
            $uri = $this->makeUri($tab, $formationType, $params);
        }

        $response = $this->call($uri, $filters);

        if ($filterResponse) {
            // dump('filter : ', $response, $where);
            $data = $this->formatResponse($response, $where);
        } else {
            $data = $this->formatResponse($response);
        }

        return $data;
    }

    /**
     * searchStudent
     *
     * @param [type] $name
     * @param [type] $firstName
     * @param [type] $ineCode
     * @param [type] $studentCode
     * @param [type] $badgeCode
     * @return void
     */
    public function searchStudent($name = null, $firstName = null, $ineCode = null, $studentCode = null, $badgeCode = null)
    {

        //When you concatenate only NULL values, you got an empty string
        if ($name . $firstName . $ineCode . $studentCode . $badgeCode === "") {
            abort(500, 'Ynternal error');
        }

        $params = [
            'nomApprenant' => $name,
            'prenomApprenant' => $firstName,
            'ine' => $ineCode,
            'codesApprenant' => $studentCode, //à vérifier si c'est un tableau de int ou string
            'numeroBadge' => $badgeCode
        ];

        $uri = "/rechercher/apprenants";

        $student = $this->call($uri, $params);

        return $student;
    }

    // ****APPRENANTS
    public function apprenants($formationType = 'FI', $code_periode = null)
    {
        //gestion des paramètres
        //si c'est des filtres alors query params
        //si c'est des params alors dans l'URI

        if ($formationType === 'FI') {
            // $uri = "/formation-longue/apprenants?codesPeriode=" . $code_periode;
            $uri = "/formation-longue/apprenants";
            $params = [
                "codesPeriode" => $code_periode
            ];
        } elseif ($formationType === 'FC') {
            //GET /r/v1/formation-courte/@dateDeb/@dateFin/apprenants
        }
        $api_data_apprenants = $this->call($uri, $params);

        return $api_data_apprenants;
    }

    // ****FREQUENTES
    public function ApiFrequentes($code_periode = null)
    {

        $url = "https://citeformations.ymag.cloud/index.php/r/v1/apprenants/frequentes?codesPeriode=" . $code_periode;
        $api_data_frequentes = $this->call($url);

        return $api_data_frequentes;
    }

    // ****CONTRATS
    public function Apicontrats($code_periode = null)
    {

        $url = "https://citeformations.ymag.cloud/index.php/r/v1/contrats?codesPeriode=" . $code_periode;
        $api_data_contrats = $this->call($url);

        return $api_data_contrats;
    }
}
