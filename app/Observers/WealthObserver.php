<?php

namespace App\Observers;

use Models\Wealth;

class WealthObserver
{

    /**
     * Handle the Wealth "deleting" event.
     *
     * @param  \Models\Wealth  $wealth
     * @return void
     */
    public function deleting(Wealth $wealth)
    {
        $wealth->file?->delete();
    }
}
