<?php

declare(strict_types=1);

namespace App\Observers;

use App\Concerns\TracksHistoryTrait;
use App\Models\Shop;

final class ShopObserver
{
    use TracksHistoryTrait;

    /**
     * Handle the Shop "created" event.
     */
    public function created(Shop $shop): void
    {
        $this->trackEvent($shop, 'shop', newValue: $shop->company);
    }

    /**
     * Handle the Shop "updated" event.
     */
    public function updated(Shop $shop): void
    {
        $this->track($shop);
    }

    /**
     * Handle the Shop "deleting" event.
     *
     * Tracked before deletion so the foreign key is still valid; the FK then
     * cascades to set this entry's shop_id to null.
     */
    public function deleting(Shop $shop): void
    {
        $this->trackEvent($shop, 'shop', oldValue: $shop->company);
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
