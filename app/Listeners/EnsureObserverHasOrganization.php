<?php

namespace App\Listeners;

use App\Events\ObserverCreated;
use App\Jobs\CreateOrganizationForObserver;

class EnsureObserverHasOrganization
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ObserverCreated $event): void
    {
        // 新しいJobクラスへ処理を委譲
        CreateOrganizationForObserver::dispatch($event->observer);
    }
}