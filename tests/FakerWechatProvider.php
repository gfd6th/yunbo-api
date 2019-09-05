<?php
namespace Tests;
/**
 * Created by PhpStorm.
 * User: hexu
 * Date: 2019/9/5
 * Time: 5:17 PM
 */
use Overtrue\Socialite\User as SocialiteUser;

class FakerWechatProvider
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