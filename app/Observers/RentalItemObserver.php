<?php

namespace App\Observers;

use App\Models\RentalItem;

class RentalItemObserver
{
    public function saved(RentalItem $rentalItem): void
    {
        $rentalItem->rental->updateTotalHarga();
    }

    public function deleted(RentalItem $rentalItem): void
    {
        $rentalItem->rental->updateTotalHarga();
    }
}