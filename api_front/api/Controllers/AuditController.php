<?php

namespace Api\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Models\Wealth;
use School\Manager\SchoolManager;
use App\Exceptions\ApiException;
use Illuminate\Support\Facades\Log;
use Api\Collections\Wealths as WealthCollection;
use Models\Audit;
use App\Http\Requests\AuditRequest;
use Models\QualityLabel;

class AuditController extends Controller
{
    protected $schoolManager;
    public function __construct()
    {
        $this->schoolManager = app(SchoolManager::class);
    }

    /**
     * Undocumented function
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = $request->get("params");
        $request->request->set("internal", true);
        $extern = $query["extern"];
        $formations = $query["formations"];
        $groups = $query["groups"];
        $students = $query["students"];

        $periods = $this->schoolManager->getPeriods($query["since"], $query["until"], false);

        //si que des formations = audit par formation
        $auditFormation = count($formations) > 0 && count($groups) <= 0 && count($students) <= 0;
        //si formations + group -apprenants = audit groupes
        $auditGroups = count($groups) > 0 && count($formations) >= 0 && count($students) <= 0;
        //si formation + groupes + apprenants ou formations + apprenants ou groupes + apprenants = audit par apprenants
        $auditStudent = count($students) > 0 && count($formations) >= 0 && count($groups) >= 0;
        //C'est quoi comme audit
        $audit = $auditFormation ? "formation" : ($auditGroups ? "group" : ($auditStudent ? "student" : "global"));

        $json = [
            "audit_type" => $query['type'],
            "extern" => $extern,
            "periods" => $periods,
            "formations" => $formations,
            "groups" => $groups,
            "students" => $students,
        ];
        //retourne une liste de preuve ordonnée
        return response()->json($json, 200);
    }


    /**
     * endpoint for /audit/result
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function result(Request $request)
    {

        // "602409", //"TITRE PRO CUISINIER"
        // "3154", //CAP PEINTRE EN CARROSSERIE
        // "217168" //MODULE LA COMMERCIALISATION
        $query = $request->get("params");
        $qualityLabel = $query["quality_label"];
        $filters = $query;

        try {
            $wealths = $this->getData($qualityLabel, $filters);
            $json = [
                "wealths" => $wealths,
                "wrapper" => $this->getWrapper($qualityLabel, 'indicator'),
            ];
            return response()->json($json, 200);
        } catch (ApiException $e) {
            // If an exception is thrown, log the error and return an error response
            Log::error($e->getMessage());
            return response()->json(['error' => 'An error occurred while retrieving data.'], 500);
        }
    }

    protected function makeFilters($index, $filters)
    {
        $config = config('scout.meilisearch.filtrable_attributes');
        $availableFilters = $config[$index];
        $filter = "";
        $and = " AND ";

        if ($filters) {
            foreach ($filters as $key => $value) {
                if (in_array($key, $availableFilters) && !is_null($value)) {
                    $filter .= $key . "=" . $value . $and;
                } else {
                }
            }
            $filter = rtrim($filter, $and);
        }
        return $filter;
    }

    protected function getData($qualityLabel, $filters)
    {

        // $filters = [
        //     "wealth_type" => "lien",
        //     "granularity_type" => 'global',
        //     "archived" => 'true',
        //     "indicators.quality_label.name" => "Qualiopi",
        //     "tags.label" => "Handicap"
        // ];

        $f = $this->makeFilters("wealths", $filters);
        //TODO-BACK #71 getData pour autre chose que qualiopy ??????
        try {
            $wealths = Wealth::search($qualityLabel, function ($meilisearch, $query, $options) use ($f) {
                $options['limit'] = 2000;
                // $options['q'] = "qualiopi";
                $options['filter'] = $f;
                return $meilisearch->search($query, $options);
            })
                ->query(function ($builder) {
                    $builder->with('indicators.criteria.qualityLabel', 'wealthType', 'actions.stage', 'files', 'unit', 'tags');
                })
                ->get();
            $collection = new WealthCollection($wealths);
            return json_decode($collection->toJson());
        } catch (\MeiliSearch\Exceptions\ApiException $e) {
            throw new ApiException('MeiliSearch connection error');
        }
    }

    protected function getWrapper($qualityLabel, $type)
    {
        //TODO-BACK #71 Wrapper pour autre chose que qualiopy ??????
        //FIXME ne fonctionne que pour les indicateurs à revoir pour le reste des where
        $modelClass = 'Models\\' . ucfirst($type);
        if (!class_exists($modelClass)) {
            return false;
        }
        // a reprendre je n'ai qualityLabel et criteria que sur indicateur, criteria
        return $modelClass::with(['qualityLabel', 'criteria'])
            ->whereHas('qualityLabel', function ($query) use ($qualityLabel) {
                $query->where('label', '=', $qualityLabel);
            })
            ->get();
    }

    public function validateAudit(AuditRequest $request)
    {
        $values = $request->all();
        $content = [
            "name" => $values['name'],
            "date" => $values['date'],
            "auditor" => $values['auditor'],
            "audit_type" => $values['audit_type']["label"],
            "comment" => $values['comment'],
            "sample" => $values['sample'],
            "validation" => $values['validation'],
            "wealths" => $values['wealths'],
            "quality_label" => $values['quality_label']
        ];

        $qualityLabel = $values['quality_label'];

        $q = QualityLabel::where('label', "=", $qualityLabel)->get();

        if (isset($values['audit_id']) && $values['audit_id'] != "") {
            $audit = Audit::findOrFail($values['audit_id']);
        } else {
            $audit = new Audit();
        }

        $audit->fill(["content" => json_encode($content)]);
        $audit->qualityLabel()->associate($q[0]);
        $audit->save();

        return response()->json(["audit_id" => $audit->id], 200);
    }
}
