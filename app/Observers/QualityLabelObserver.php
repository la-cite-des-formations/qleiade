<?php

namespace App\Observers;

use App\Events\UpdateObjectInRelationWithWealth;
use Models\QualityLabel;

class QualityLabelObserver
{
    /**
     * Handle events after all transactions are committed.
     *
     * @var bool
     */
    public $afterCommit = true;

    /**
     * Handle the QualityLabel "updated" event.
     *
     * @param  \Models\QualityLabel  $qualityLabel
     * @return void
     */
    public function updated(QualityLabel $qualityLabel)
    {
        if (isset($qualityLabel->getChanges()['label'])) {
            //Si le label change on emet l'event pour update index
            event(new UpdateObjectInRelationWithWealth($qualityLabel));
        }
    }
}
