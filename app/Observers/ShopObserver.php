<?php

declare(strict_types=1);

namespace App\Observers;

use App\Concerns\TracksHistoryTrait;
use App\Models\Shop;

final class ShopObserver
{
    use TracksHistoryTrait;

    /**
     * Handle the Shop "updated" event.
     */
    public function updated(Shop $shop): void
    {
        $this->track($shop);
    }

    /**
     * Handle the Shop "deleted" event.
     */
    public function deleted(Shop $shop): void
    {
        // ...
    }

    /**
     * Handle the Shop "restored" event.
     */
    public function restored(Shop $shop): void
    {
        // ...
    }

    /**
     * Handle the Shop "forceDeleted" event.
     */
    public function forceDeleted(Shop $shop): void
    {
        // ...
    }
}
