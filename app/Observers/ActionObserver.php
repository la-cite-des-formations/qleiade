<?php

namespace App\Observers;

use App\Events\UpdateObjectInRelationWithWealth;
use Models\Action;

class ActionObserver
{

    /**
     * Handle events after all transactions are committed.
     *
     * @var bool
     */
    public $afterCommit = true;

    /**
     * Handle the Action "updated" event.
     *
     * @param  \Models\Action  $action
     * @return void
     */
    public function updated(Action $action)
    {
        if (isset($action->getChanges()['label'])) {
            //Si le label change on emet l'event pour update index
            event(new UpdateObjectInRelationWithWealth($action));
        }
    }
}
