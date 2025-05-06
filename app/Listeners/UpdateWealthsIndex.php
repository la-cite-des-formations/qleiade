<?php

namespace App\Listeners;

use App\Events\UpdateObjectInRelationWithWealth;

class UpdateWealthsIndex
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\UpdateObjectInRelationWithWealth  $event
     * @return void
     */
    public function handle(UpdateObjectInRelationWithWealth $event)
    {
        $objUpdated = $event->obj;

        //l'objet est-il en relation avec Wealth
        // = method Wealths exists
        if (method_exists($objUpdated, "wealths")){
            //wealths concernés
            if(count($objUpdated->wealths)>0){
                //si oui mise à jour de l'index
                $objUpdated->wealths()->searchable();
            }
        }
    }
}
