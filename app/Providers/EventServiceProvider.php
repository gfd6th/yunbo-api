<?php

namespace App\Providers;

use App\Events\OrderPaid;
use App\Listeners\AddAffiliationToGroupOwner;
use App\Listeners\SaveOrder;

use App\Listeners\SendWechatNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        OrderPaid::class => [
//            SaveOrder::class,
            SendWechatNotification::class,
            AddAffiliationToGroupOwner::class,
        ]

    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
