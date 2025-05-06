<?php

namespace Api\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;

use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Http\Traits\Meilisearchable;

use Models\Wealth;
use Models\Action;
use Models\Unit;
use Models\Indicator;
use Models\QualityLabel;

use Api\Collections\Actions as ActionsCollection;
use Api\Collections\Wealths as WealthCollection;

class GraphController extends Controller
{
    use Meilisearchable;

    #region not in use
    public function filteredWealths($filters)
    {
        $f = $this->makeFilters("wealths", $filters);
        try {
            $wealths = Wealth::search("", function ($meilisearch, $query, $options) use ($f) {
                $options['limit'] = 2000;
                $options['filter'] = $f;
                return $meilisearch->search($query, $options);
            })
                ->query(function ($builder) {
                    $builder->with('indicators.criteria.qualityLabel', 'wealthType', 'actions.stage', 'files', 'unit', 'tags');
                })
                ->get();
            $collection = new WealthCollection($wealths);
            return json_decode($collection->toJson(), true);
        } catch (\MeiliSearch\Exceptions\ApiException $e) {
            throw new ApiException('MeiliSearch connection error');
        }
    }

    function makeGraphElements($items)
    {
        $nodes = [];
        $edges = [];

        if ($items) {
            foreach ($items as $index => $parent) {
                $parentNode = $this->createNode($parent, $parent['type']);
                $nodes[] = $parentNode;

                //une seul action par preuve $parent['actions'])
                if ($parent['actions']) {
                    foreach ($parent['actions'] as $action) {
                        $actId = $action['type'] . '-' . $action['id'];
                        $actNode = $this->findNodeById($actId, $nodes);

                        if (!$actNode) {
                            $newActNode = $this->createNode($action, "Action");
                            $nodes[] = $newActNode;
                            $edges[] = $this->createLink($newActNode, $parentNode);
                        } else {
                            $edges[] = $this->createLink($actNode, $parentNode);
                        }
                    }
                }

                if ($parent['indicators']) {
                    foreach ($parent['indicators'] as $indicator) {
                        $indId = $indicator['type'] . '-' . $indicator['id'];
                        $indicatorNode = $this->findNodeById($indId, $nodes);

                        if (!$indicatorNode) {
                            $newIndNode = $this->createNode($indicator, 'Indicator');
                            $nodes[] = $newIndNode;
                            $edges[] = $this->createLink($parentNode, $newIndNode);
                        } else {
                            $edges[] = $this->createLink($parentNode, $indicatorNode);
                        }
                    }
                }
            }
        }

        return ['nodes' => $nodes, 'edges' => $edges];
    }

    #endregion not in use

    #region manage data
    function getActions($unitId, $filters)
    {
        $actionQuery = Action::with(['unit', 'wealths.indicators.qualityLabel'])->whereHas('unit', function ($query) use ($unitId) {
            $query->where('unit_id', $unitId);
        });

        if (isset($filters['action']) && $filters['action'] != "") {
            $actionQuery->where("id", "=", $filters['action']);
        }
        $actions = $actionQuery->get();
        // Retourne les actions avec un unit ayant pour ID $unitId
        $collection = new ActionsCollection($actions);
        $data = json_decode($collection->toJson(), true);
        return $data;
    }

    function getGraphFilters($what, $who)
    {
        switch ($what) {
            case 'unit':
                $act  = Action::with(['unit'])->whereHas('unit', function ($query) use ($who) {
                    $query->where('unit_id', '=', $who);
                })->orderBy('order')
                    ->get();
                $collection = new ActionsCollection($act);
                $filters = json_decode($collection->toJson(), true);
                break;

            default:
                break;
        }
        return $filters;
    }

    #endregion manage data

    function getGraph($view, $what, Request $request)
    {
        $data = [];
        $mapper = [];
        $graphFilters = [];

        $filters = $request->all();
        switch ($view) {
            case 'board':
                //le dashboard label
                if ($what === "labels") {
                    $data = QualityLabel::with(
                        [
                            "audits",
                            'indicators.wealths',
                            "indicators.wealths.wealthType",
                            'indicators.wealths.unit'
                        ]
                    )
                        ->get();

                    $mapper = [
                        "item" => ["id" => "id", "name" => "name", "key" => "quality_label"], //node principale labels, unit, indicators...
                        // "audits", //les no
                        // "indicators" => [
                        //     "wealths" => [
                        //         "unit"
                        //     ]
                        // ]
                        'indicators'
                    ];
                }

                //le dashboard unit le seul vraiment en utilisation
                if ($what === "unit") {
                    $procName = $filters['unit'];
                    $byUnitId = Unit::where("name", "=", $procName)->first()->id;
                    $data = $this->getActions($byUnitId, $filters);
                    $mapper = [
                        "item" => ["id" => "id", "name" => "name", "label" => "label", "key" => "Action", "order" => "order"], //node principale labels, unit, indicators...
                        "wealths" => ['indicators']
                    ];

                    $graphFilters = $this->getGraphFilters($what, $byUnitId);
                }

                // le dashboard qualité
                if ($what === "indicators") {
                    //un tableau d'indicateurs
                    //un tableau de preuves
                    //un tableau d'actions
                    //un tableau de unit
                    //un tableau de links ?


                    $data =  Indicator::with([
                        "wealths",
                        // "wealths.wealthType",
                        // "actions.stage",
                        // "wealths.tags",
                        "wealths.unit",
                        "wealths.actions"
                    ])
                        ->limit(1)
                        ->get();

                    $mapper = [
                        "item" => ["id" => "id", "name" => "name", "key" => "Indicator"], //node principale labels, unit, indicators...
                        "wealths" => ['unit', "actions"]
                    ];
                }
                break;
            case 'parcour':
                break;
            default:
                abort(418, "parameter not matched");
                break;
        }

        $n_e = $this->parseGraphData($data, $mapper);

        $output = $this->addPortsToNodes($n_e['nodes'], $n_e['edges']);
        //mettre en cache car la requête est trop longue
        return [
            "nodesWithEdges" => $output,
            "filters" => $graphFilters
        ];
    }

    #region make graph
    function parseGraphData($list, $mapper)
    {
        $nodes = [];
        $links = [];

        function mapRecursivly($mapper, $item, &$nodes, &$links, $parent = null, $context)
        {
            foreach ($mapper as $key => $step) {
                if (is_numeric($key)) {
                    $key = $step;
                }

                if ($key != "item") {
                    $valueToMap = $item[$key];
                    if (is_array($valueToMap)) {
                        // Traitement des collections (wealths, audits, etc.)
                        foreach ($valueToMap as $childItem) {
                            $node = $context->createNode($childItem);
                            $context->pushUniqueIn($nodes, $node);

                            if (!is_null($parent)) {
                                $link = $context->createLink($parent, $node);
                                $context->pushUniqueIn($links, $link);
                            }

                            if (is_array($step)) {
                                $childParent = $node;
                                mapRecursivly($step, $childItem, $nodes, $links, $childParent, $context);
                            }
                        }
                    } else {
                        // Création d'un nœud pour les éléments non-collection
                        $node = $context->createNode($valueToMap);
                        $context->pushUniqueIn($nodes, $node);
                        if (!is_null($parent)) {
                            $link = $context->createLink($parent, $node);
                            $context->pushUniqueIn($links, $link);
                        }
                        if (is_array($step)) {
                            $childParent = $node;
                            mapRecursivly($step, $valueToMap, $nodes, $links, $childParent, $context);
                        }
                    }
                }
            }
        }

        foreach ($list as $item) {
            $p = [
                "id" => $item[$mapper['item']["id"]],
                "name" => $item[$mapper['item']["name"]],
                "label" => $item[$mapper['item']["label"]],
                "type" => $mapper['item']['key'],
            ];
            if ($item['order']) {
                $p['order'] = $item['order'];
            }

            $parent = $this->createNode($p);
            $this->pushUniqueIn($nodes, $parent);

            mapRecursivly($mapper, $item, $nodes, $links, $parent, $this);
        }

        return ["nodes" => $nodes, "edges" => $links];
    }

    function addPortsToNodes($nodes, $edges)
    {
        $layoutedNodes = [];
        $layoutedEdges = array_values($edges);

        foreach ($nodes as $node) {
            $target = 0;
            $source = 0;
            $ports = [];

            foreach ($layoutedEdges as &$edge) {
                if ($edge['target'] === $node['id']) {
                    $target += 1;
                    $portId = $node['id'] . '-left-port-' . $target;
                    $ports[] = [
                        'id' => $portId,
                        'type' => 'target',
                        'position' => 'Left',
                        'isConnectable' => true
                    ];
                    $edge['targetHandle'] = $portId;
                }

                if ($edge['source'] === $node['id']) {
                    $source += 1;
                    $portId = $node['id'] . '-right-port-' . $source;
                    $ports[] = [
                        'id' => $portId,
                        'type' => 'source',
                        'position' => 'Right',
                        'isConnectable' => true
                    ];
                    $edge['sourceHandle'] = $portId;
                }
            }
            $node['data']['ports'] = $ports;
            $node['data']['relations'] = count($ports);
            $layoutedNodes[] = $node;
        }

        return [
            'nodes' => $layoutedNodes,
            'edges' => $layoutedEdges
        ];
    }

    #endregion make grpah




    #region manage nodes and links
    function createNode($item)
    {
        $node = [
            "id" => $item['type'] . "-" . $item['id'],
            "name" => $item['name'],
            'data' => ['item' => $item], //si c'est un objet il faut le mapper
            "label" => $item['label'],
            "type" => $item['type'],
            'connectable' => true
        ];

        if (isset($item['order'])) {
            $node['order'] = $item['order'];
        }

        return $node;
    }

    function createLink($sourceNode, $targetNode)
    {
        return [
            'id' => $sourceNode['id'] . '-' . $targetNode['id'],
            'source' => $sourceNode['id'],
            'target' => $targetNode['id'],
        ];
    }

    function findNodeById($id, $nodes)
    {
        foreach ($nodes as $node) {
            if ($node['id'] === $id) {
                return $node;
            }
        }
        return null;
    }

    public function pushUniqueIn(&$array, $obj)
    {
        $finded = Arr::first($array, function ($value, $key) use ($obj, $array) {
            if (array_key_exists("id", $obj)) {
                return $value['id'] === $obj['id'];
            } else {
                if (array_key_exists("source", $obj) && array_key_exists("target", $obj)) {
                    return $value['source'] === $obj['source'] && $value['target'] === $obj['target'];
                }
            }
            return true;
        });

        if ($finded === null) {
            $array[$obj['id']] = $obj;
        }

        return true;
    }
    #endregion manage ports and links
}
