<?php

namespace App\Listeners;

use App\Events\ObserverCreated;
use App\Jobs\CreateOrganizationForObserver;
use Illuminate\Contracts\Queue\ShouldQueueAfterCommit;

class EnsureObserverHasOrganization implements ShouldQueueAfterCommit
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
