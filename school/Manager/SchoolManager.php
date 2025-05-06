<?php

namespace School\Manager;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Arr;
use School\Adapters\ConnecterInterface;
use School\Adapters\Collections\Students as StudentCollection;
use School\Adapters\Collections\Groups as GroupCollection;
use School\Adapters\Collections\Formations as FormationCollection;
use School\Adapters\Collections\Periods as PeriodCollection;
use Exception;

class SchoolManager
{
    protected array $connecters;
    protected array $config;

    /**
     * constructor
     *
     * @param array $connecters
     * @param array $config
     */
    public function __construct($connecters = [], $config = [])
    {
        $this->connecters = $connecters;
        $this->config = $config;
    }

    /**
     * set connecter
     *
     * @param string $key
     * @param ConnecterInterface $connecter
     * @return void
     */
    public function setConnecter(string $key, ConnecterInterface $connecter)
    {
        $this->connecters[$key] = $connecter;
        return true;
    }

    public function connecter($key)
    {
        return $this->connecters[$key];
    }

    /**
     * get formations
     *
     * @return Illuminate\Http\Resources\Json\ResourceCollection
     */
    public function getFormations()
    {
        $data = $this->connecters["ypareo"]->get('', 'formations', true, ["plusUtilise" => 0]);

        return new FormationCollection($data);
    }

    protected function manageDates($s, $u)
    {
        if (is_null($u)) {
            $until = Carbon::now();
        } else {
            try {
                $until = Carbon::createFromFormat('Y-m-d', $u);
            } catch (\Carbon\Exceptions\InvalidFormatException $e) {
                try {
                    $until = Carbon::createFromFormat('d-m-Y', $u);
                } catch (\Throwable $th) {
                    throw new Exception("Error invalid until date", 1);
                }
            }
        }

        if (is_null($s)) {
            //on prend la date until
            $since = $until->copy();
        } else {
            try {
                $since = Carbon::createFromFormat('Y-m-d', $s);
            } catch (\Carbon\Exceptions\InvalidFormatException $e) {
                try {
                    $since = Carbon::createFromFormat('d-m-Y', $s);
                } catch (\Carbon\Exceptions\InvalidFormatException $e) {
                    throw new Exception("Error invalid since date", 1);
                }
            }
        }

        return ["since" => $since, "until" => $until];
    }

    /**
     * get periods
     *
     * @param $since
     * @param $until
     * @return array $filtered
     */
    public function getPeriods($since, $until, $internal = true)
    {
        $dates = $this->manageDates($since, $until);
        $s = $dates['since'];
        $u = $dates['until'];

        $p = $this->connecters["ypareo"]->get('', 'periodes');

        $filtered = $p->filter(function ($value) use ($s, $u) {

            $periodeDeb = Carbon::createFromFormat('d/m/Y', $value['dateDeb']);
            $periodeFin = Carbon::createFromFormat('d/m/Y', $value['dateFin']);

            $encours = $u->greaterThanOrEqualTo($periodeDeb) && $u->lessThanOrEqualTo($periodeFin);
            $ante = $s->greaterThanOrEqualTo($periodeDeb) && $s->lessThanOrEqualTo($periodeFin);

            $A = $periodeDeb->greaterThanOrEqualTo($s) && $periodeDeb->lessThanOrEqualTo($u);
            $B = $periodeFin->greaterThanOrEqualTo($s) && $periodeFin->lessThanOrEqualTo($u);
            $include =  $A && $B;

            //fait ressortir toutes les périodes concernées par l'interval de dates
            //ex: since = 01-01-2020 et until = 02-03-2023
            //resultat = periode 2019-2020, 2020-2021, 2021-2022, 2022-2023

            return $include || $encours || $ante;
        })->values();

        if ($internal) {
            $resp = $filtered;
        } else {
            $resp = new PeriodCollection(Arr::sort($filtered, "codePeriode"));
        }

        return  $resp;
    }

    /**
     * get groups
     *
     * @param Collection $periods
     * @param array $formations
     * @return Illuminate\Http\Resources\Json\ResourceCollection
     */
    public function getGroups($periods, array $formations = null, $internal = false)
    {

        $periodeIds = $periods->map(function ($value, $key) {
            return $value['codePeriode'];
        })->toArray();

        //si des formations FI, les groupes FI
        $data = $this->connecters["ypareo"]->get('', 'groupes', true, [], ['codesPeriode' => implode(",", $periodeIds)], [], 'FI');

        //si des formations FC, les groupes FC
        // $data = app(Ypareo::class)->get('groupes', true, [], ['codesPeriode' => implode(",", $periodeIds)], [], 'FC');

        //on a le codeFormation en variable donc on peut filtrer dessus après requêtes
        if (count($formations) > 0) {
            //je suis sur que ça marche donc si besoin de controler le freach
            // $gros = $data->filter(function ($value, $groupKey) use ($formations, $data) {
            //     $find = false;
            //     foreach ($formations as $key => $json) {
            //         $formation = json_decode($json);
            //         try {
            //             $thinkValue = $value['codeFormation'];
            //         } catch (\Throwable $th) {
            //             throw 'not filtered';
            //         }
            //         if ($thinkValue == $formation->id) {
            //             $find = true;
            //             // $data[$groupKey]['codeFormation'] = $formation->title;
            //         }
            //     }

            //     return $find;
            // });
            $groups = collect([]);
            foreach ($data as $key => $group) {
                foreach ($formations as $key => $json) {
                    $formation = json_decode($json);
                    try {
                        $thinkValue = $group['codeFormation'];
                    } catch (\Throwable $th) {
                        throw 'not filtered';
                    }
                    if ($thinkValue == $formation->id) {
                        $group['codeFormation'] = $formation->title;
                        $groups->push($group);
                        break;
                    }
                }
            }
        } else {
            $groups = $data;
        }

        $res = new GroupCollection($groups);
        if (!$internal) {
            $response = $res;
        } else {
            $res->withoutWrapping();
            $response = json_decode($res->toJson())->data;
        }

        return $response;
    }

    /**
     * get students
     *
     * @param array $formations
     * @param array $groups
     * @param  $since
     * @param  $until
     * @return Illuminate\Http\Resources\Json\ResourceCollection $students
     */
    public function getStudents($formations, $groups, $since, $until)
    {
        $periods = $this->getPeriods($since, $until, true);

        //si ni groupes ni formations
        if (count($formations) <= 0 && count($groups) <= 0) {
            $groups = $this->getGroups($periods, [], true);
        }

        // si des formations mais pas de groupes
        if (count($formations) > 0 && count($groups) <= 0) {
            $groups = $this->getGroups($periods, $formations, true);
        }

        $data = collect([]);
        //si des groups sont renseignés
        // les appreants sont-ils forcément dans un groupe ?
        // dd("getgroups", $groups);
        if (count($groups) > 0) {
            //apprenants à partir des groupes
            foreach ($groups as $group) {
                if (gettype($group) === "string") {
                    $g = json_decode($group);
                } else {
                    $g = $group;
                }
                $uri = 'groupes/' . $g->id . "/apprenants";
                $d = $this->connecters["ypareo"]->get($uri);
                $data = $data->concat($d);
            }
        }

        return new StudentCollection($data);
    }

    /**
     * getWealths in school app
     *
     * @param string $query
     * @return \Illuminate\Support\Collection $wealths
     */
    public function getWealths(string $query, string $app = "ypareo")
    {
        $wealths = ["toto"];
        $w = $this->connecters["ypareo"]->get();

        return $wealths;
    }
}
