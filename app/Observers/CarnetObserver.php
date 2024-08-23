<?php

namespace App\Observers;

use App\Models\Carnet;

class CarnetObserver
{
    /**
     * Handle the Carnet "created" event.
     */
    public function created(Carnet $carnet): void
    {
        $carnet->installments()->createMany($carnet->spreadIntoInstallments());
    }
}
