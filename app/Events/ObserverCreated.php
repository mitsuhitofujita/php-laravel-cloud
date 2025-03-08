<?php

namespace App\Events;

use App\Models\Observer;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ObserverCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The observer instance.
     *
     * @var \App\Models\Observer
     */
    public $observer;

    /**
     * Create a new event instance.
     */
    public function __construct(Observer $observer)
    {
        $this->observer = $observer;
    }
}