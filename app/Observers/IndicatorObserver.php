<?php

namespace App\Observers;

use Models\Indicator;
use App\Events\UpdateObjectInRelationWithWealth;

class IndicatorObserver
{

    /**
     * Handle events after all transactions are committed.
     *
     * @var bool
     */
    public $afterCommit = true;

    /**
     * Handle the Indicator "updated" event.
     *
     * @param  \Models\Indicator  $indicator
     * @return void
     */
    public function updated(Indicator $indicator)
    {
        if (isset($indicator->getChanges()['label'])) {
            //Si le label change on emet l'event pour update index
            event(new UpdateObjectInRelationWithWealth($indicator));
        }
    }
}
