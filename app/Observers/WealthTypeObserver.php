<?php

namespace App\Observers;

use App\Events\UpdateObjectInRelationWithWealth;
use Models\WealthType;

class WealthTypeObserver
{

    /**
     * Handle events after all transactions are committed.
     *
     * @var bool
     */
    public $afterCommit = true;

    /**
     * Handle the WealthType "updated" event.
     *
     * @param  \Models\WealthType  $wealthType
     * @return void
     */
    public function updated(WealthType $wealthType)
    {
        if (isset($wealthType->getChanges()['label'])) {
            //Si le label change on emet l'event pour update index
            event(new UpdateObjectInRelationWithWealth($wealthType));
        }
    }
}
