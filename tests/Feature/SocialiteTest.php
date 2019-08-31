<?php

namespace Tests\Feature;

use App\Group;
use App\User;
use App\UserSocial;
use Closure;
use Laravel\Passport\ClientRepository;
use Tests\TestCase;
use Overtrue\Socialite\User as SocialiteUser;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Socialite;
use Cache;

class FakeWechatProvider
{
    private $code;

    public function __construct($code)
    {
        $this->code = $code;
    }

    public function user()
    {
        if ($this->code !== 'token') {
            throw new \Exception('error token');
        }
        $user = new SocialiteUser([
            'id'       => 'openid',
            'name'     => 'name',
            'nickname' => 'nickname',
            'avatar'   => 'avatar',
            'email'    => null,
            'original' => [],
            'provider' => 'WeChat',
        ]);

        return $user;
    }
}

class SocialiteTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_not_agree_authorize()
    {
        $code = 'disagree';
        $redirect = '/path/to/client/callback';
        $wechatProvider = new FakeWechatProvider($code);
        Socialite::shouldReceive('driver')->andReturn($wechatProvider);
        $this->get("/api/auth/login/wechat/callback?code={$code}&state={$redirect}")
            ->assertRedirect(config('app.client_base_url') . "/auth/social-callback?error=1&redirect={$redirect}");
    }

    /** @test */
    public function user_not_exist()
    {
        $code = 'token';
        $redirect = '/path/to/client/callback';
        $wechatProvider = new FakeWechatProvider($code);
        Socialite::shouldReceive('driver')->andReturn($wechatProvider);
        $this->get("/api/auth/login/wechat/callback?code={$code}&state={$redirect}")
            ->assertRedirect(config('app.client_base_url') . "/auth/social-callback?openid=openid&redirect={$redirect}&avatar=avatar");
        $this->assertEquals('openid', Cache::get('openid')['id']);
    }



    /** @test */
    public function user_submit_user_form()
    {
        $this->createPersonClient();

        factory(Group::class)->create();

        $this->json("POST", "/api/auth/register/user-info", [
            'name'       => 'hexu',
            'phone'      => '18612345678',
            'openid'     => 'expiredOpenId',
            'group_code' => 'wrongCode',
        ])->assertJsonValidationErrors(['group_code', 'openid'])
            ->assertJsonFragment([
                'errors' => [
                    'group_code' => ['口令错误'],
                    'openid'     => ['本次会话已过期'],
                ],
            ]);


        $fakeWechatInfo = new SocialiteUser([
            'id'       => 'openid',
            'name'     => 'name',
            'nickname' => 'nickname',
            'avatar'   => 'avatar',
            'email'    => null,
            'original' => [],
            'provider' => 'WeChat',
        ]);

        Cache::put($fakeWechatInfo->id, $fakeWechatInfo);
        $this->withoutExceptionHandling()->json("POST", "/api/auth/register/user-info", [
            'name'       => 'hexu',
            'phone'      => '18612345678',
            'openid'     => 'openid',
            'group_code' => 'rightCode',
        ]);

        $user = User::where('name', 'hexu')->first();
        $this->assertEquals($user->name, 'hexu');
        $this->assertCount(1, UserSocial::where('user_id', $user->id)->get());
    }

    /** @test */
    public function user_exists()
    {
        $this->createPersonClient();
        $fakeWechatUser = new SocialiteUser([
            'id'       => 'openid',
            'name'     => 'name',
            'nickname' => 'nickname',
            'avatar'   => 'avatar',
            'email'    => null,
            'original' => [],
            'provider' => 'WeChat',
        ]);
        $user = UserSocial::createUser(
            'hexu', '18612345678', '1', $fakeWechatUser->avatar, $fakeWechatUser->id
        );
        $code = 'token';
        $redirect = '/path/to/client/callback';
        $wechatProvider = new FakeWechatProvider($code);
        Socialite::shouldReceive('driver')->andReturn($wechatProvider);
        $response = $this->get("/api/auth/login/wechat/callback?code={$code}&state={$redirect}")
            ->getContent();

        $this->assertContains('access_token', $response);
    }

    public function createPersonClient()
    {
        $clientRepository = new ClientRepository();
        $clientRepository->createPersonalAccessClient(
            null, config('app.token_name'), 'http://localhost'
        );
    }

    /** @test */
    public function check_group_code()
    {
        $group = factory(Group::class)->create();

        $this->withoutExceptionHandling()->get("/api/auth/register/group?code=wrongCode")
            ->assertSee('口令错误');

        $this->withoutExceptionHandling()->get("/api/auth/register/group?code=rightCode")
            ->assertSee($group->name);
    }
}
