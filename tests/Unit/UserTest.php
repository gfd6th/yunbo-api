<?php

namespace Tests\Unit;

use App\Events\OrderPaid;
use App\Group;
use App\Http\Resources\UserResource;
use App\User;
use App\UserSocial;
use Tests\TestCase;
use Setting;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{

    use RefreshDatabase;
    /** @test */
    public function user_have_a_wechat_openid()
    {
        $social = factory(UserSocial::class)->create();
        $user = $social->user;
        $this->assertEquals($user->socialFor('wechat')->provider_id, $social->provider_id);
    }

    /** @test */
    public function user_paid_plan(){
        \Event::fake();

        $user = factory(User::class)->create();

        $yearly_price = Setting::get('plans.yearly.price');
        $user->paidForPlan($yearly_price);
        $this->assertEquals($user->expire_at->toDateTimeString(), now()->addYear()->toDateTimeString());
        \Event::assertDispatched(OrderPaid::class);
    }


}
