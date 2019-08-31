<?php

namespace Tests\Unit;

use App\Events\OrderPaid;
use App\Group;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderTest extends TestCase
{
    use RefreshDatabase;
    /** @test */
    public function event_order_paid(){
        $groupOwner = factory(User::class)->create();
        $group = factory(Group::class)->create([
            'owner_id' => $groupOwner->id,
            'affiliate' => 10
        ]);
        $user = factory(User::class)->create([
            'group_id' => $group->id
        ]);

        event(new OrderPaid(200000, $user));

        $this->assertEquals($group->fresh()->profit, 20000);
    }
}
