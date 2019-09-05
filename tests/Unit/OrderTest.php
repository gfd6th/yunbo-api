<?php

namespace Tests\Unit;

use App\Events\OrderPaid;
use App\Group;
use App\User;
use App\UserSocial;
use Overtrue\Socialite\User as SocialiteUser;

use EasyWeChat\OfficialAccount\Application;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FakerTemplate{
    public function send()
    {
        return  new SocialiteUser([
            'id'       => 'openid',
            'name'     => 'name',
            'nickname' => 'nickname',
            'avatar'   => 'avatar',
            'email'    => null,
            'original' => [],
            'provider' => 'WeChat',
        ]);
    }
}

class FakerEasyWechat{
    public $template_message;

    public function __construct()
    {
        $this->template_message = new FakerTemplate();
    }
}

class OrderTest extends TestCase
{
    use RefreshDatabase;
    /** @test */
    public function event_order_paid(){
//        dd((new FakerEasyWechat())->template_message->send());
        $fakerEasyWechat = new FakerEasyWechat();
        $this->instance('wechat.official_account', $fakerEasyWechat);
        $groupOwner = factory(User::class)->create();
        factory(UserSocial::class)->create([
            'user_id' => $groupOwner->id
        ]);
        $group = factory(Group::class)->create([
            'owner_id' => $groupOwner->id,
            'affiliate' => 10
        ]);
        $user = factory(User::class)->create([
            'group_id' => $group->id
        ]);
        factory(UserSocial::class)->create([
            'user_id' => $user->id
        ]);

        event(new OrderPaid(200000, $user));

        $this->assertEquals($group->fresh()->profit, 200);
    }
}
