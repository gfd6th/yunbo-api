<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class OrderPaid
{
    use Dispatchable, SerializesModels;
    public $price;
    public $user;
    public $plan;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($price, $user, $plan=null)
    {
        $this->price = $price;
        $this->user = $user;
        $this->plan = $plan;
    }

    
}
