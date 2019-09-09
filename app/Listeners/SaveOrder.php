<?php

namespace App\Listeners;

use App\Events\OrderPaid;
use App\Order;
use Setting;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SaveOrder
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  OrderPaid  $event
     * @return void
     */
    public function handle(OrderPaid $event)
    {
        $order = new Order();
        $order->price = $event->price / 100;
        if ($event->price === Setting::get('plans.lifetime.price')) {
            $order->plan = '终身会员';
        } else {
            $order->plan = '年费会员';
        }

        $event->user->orders()->save($order);
    }
}
