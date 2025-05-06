<?php

namespace App\Observers;

use Models\Unit;
use App\Events\UpdateObjectInRelationWithWealth;

class UnitObserver
{
    /**
     * Handle events after all transactions are committed.
     *
     * @var bool
     */
    public $afterCommit = true;

    /**
     * Handle the Unit "updated" event.
     *
     * @param  \Models\Unit  $unit
     * @return void
     */
    public function updated(Unit $unit)
    {
        if (isset($unit->getChanges()['label'])) {
            //Si le label change on emet l'event pour update index
            event(new UpdateObjectInRelationWithWealth($unit));
        }
    }
}
