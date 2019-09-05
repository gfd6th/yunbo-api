<?php

namespace Tests\Feature;

use App\Group;
use App\User;
use App\UserSocial;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Overtrue\Socialite\User as SocialiteUser;


class FakerWechatUser{
    public function get()
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
        $this->user = new FakerWechatUser;
    }
}

class UserTest extends TestCase
{
    /** @test */
    public function login_user_could_get_his_own_info()
    {

        $fakerEasyWechat = new FakerEasyWechat();
        $this->instance('wechat.official_account', $fakerEasyWechat);

        $user = factory(User::class)->create(
            ['group_id' => factory(Group::class)->create()->id]
        );
        factory(UserSocial::class)->create([
            'user_id' => $user->id
        ]);
        Passport::actingAs($user);
        $response = $this->get('api/me');
        $response->assertJson([
            "data" => [
                "name" => $user->name
            ]
        ]);
    }
}
