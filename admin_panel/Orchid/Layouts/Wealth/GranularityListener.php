<?php

namespace Admin\Orchid\Layouts\Wealth;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;

use Orchid\Screen\Layouts\Listener;
use Orchid\Screen\Fields\Select;
use Orchid\Support\Facades\Layout;

use School\Manager\SchoolManager;

class GranularityListener extends Listener
{
    /**
     * List of field names for which values will be listened.
     *
     * @var string[]
     */
    protected $targets = ['wealth.granularity.type', 'wealth.validity_date', 'wealth.id'];

    /**
     * What screen method should be called
     * as a source for an asynchronous request.
     *
     * The name of the method must
     * begin with the prefix "async"
     *
     * @var string
     */
    protected $asyncMethod = 'asyncGranularity';

    private function getGranularities($type)
    {
        $options = [];
        switch ($type) {
            case 'formation':
                $formations = Cache::remember('formations', now()->addHour(1), function () {
                    return json_decode(app(SchoolManager::class)->getFormations()->toJson())->data;
                });
                $options = $formations;
                break;
            case 'group':
                //sur quelle période recherche-t-on les groupes ?
                $periods = app(SchoolManager::class)->getPeriods(null, null, true);
                $options = json_decode(app(SchoolManager::class)->getGroups($periods, [])->toJson())->data;
                break;
            case 'student':
                break;
            default:
                break;
        }
        return $options;
    }

    /**
     * @return Layout[]
     */
    protected function layouts(): iterable
    {
        $title = "";
        $options = [];
        if (isset($this->query['granularity'])) {
            $title = $this->query['granularity']['type'];
            $o = $this->getGranularities($title);
            $options = Arr::pluck($o, 'label', 'id');
        }

        return [
            Layout::rows(
                [
                    Select::make('wealth.granularity.id')
                        ->options($options)
                        ->title(__($title))
                ]
            )
                ->canSee(count($options) > 0)
        ];
    }
}
