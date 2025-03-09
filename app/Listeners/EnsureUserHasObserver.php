<?php

namespace App\Listeners;

use App\Events\UserCreated;
use App\Jobs\CreateObserverForUser;

class EnsureUserHasObserver
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
    public function handle(UserCreated $event): void
    {
        // 新しいJobクラスへ処理を委譲
        CreateObserverForUser::dispatch($event->user);
    }
}