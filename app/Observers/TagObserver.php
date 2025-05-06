<?php

namespace App\Observers;

use Models\Tag;
use App\Events\UpdateObjectInRelationWithWealth;

class TagObserver
{

    /**
     * Handle events after all transactions are committed.
     *
     * @var bool
     */
    public $afterCommit = true;

    /**
     * Handle the Tag "updated" event.
     *
     * @param  \Models\Tag  $tag
     * @return void
     */
    public function updated(Tag $tag)
    {
        if (isset($tag->getChanges()['label'])) {
            //Si le label change on emet l'event pour update index
            event(new UpdateObjectInRelationWithWealth($tag));
        }
    }
}
