<?php

namespace App\Http\Controllers;

use App\Group;
use App\Http\Requests\UserInfo;
use App\User;
use App\UserSocial;
use Illuminate\Http\Request;
use Overtrue\Socialite\Providers\WeChatProvider;
use Socialite;
class SocialiteController extends Controller
{
    public function redirect($provider)
    {

        $redirect = request()->get('redirect', '/');
        return Socialite::driver('wechat')->scopes(['snsapi_userinfo'])->with(['state'=>urlencode($redirect)])->redirect();
    }

    public function userInfo(UserInfo $request)
    {

        $name = $request->name;
        $openid = $request->openid;
        $phone = $request->phone;
        $group = Group::where('code', $request->group_code)->firstOrFail('id');
        $wechatInfo = \Cache::get($openid);
        $user = UserSocial::CreateUser($name, $phone, $group->id, $wechatInfo->avatar, $wechatInfo->id);

        return $user->createToken(config('app.token_name'))->accessToken;
    }

    public function groupInfo(Request $request)
    {
        // 初始必须创建一个群, id设置为1, 这样第一个登录进来的用户才可以填写口令, 注册成功
        $group = Group::where('code', $request->code)->first();
        if (!$group){
            return response('口令错误', 201);
        }
        return $group->name;

    }


    public function handleProviderCallback(Request $request, $provider)
    {
        $redirect= $request->get('state', '/');
        try {
            $serviceUser = Socialite::driver('wechat')->user();
        } catch (\Exception $e) {
            return redirect(config('app.client_base_url') . "/auth/social-callback?error=1&redirect={$redirect}");
        }

        $localUser = $this->getExistingUser($serviceUser);

        if(!$localUser){
            \Cache::put($serviceUser->id, $serviceUser, now()->addHour());
            $avatar = urlencode($serviceUser->avatar);
            return redirect(config('app.client_base_url') . "/auth/social-callback?openid={$serviceUser->id}&redirect={$redirect}&avatar={$avatar}");
        }

        $accessToken =  $localUser->createToken(config('app.token_name'))->accessToken;

        return redirect(config('app.client_base_url') . "/auth/social-callback?&redirect={$redirect}&access_token=${accessToken}");
//        return redirect(config('app.client_base_url') . "/auth/social-callback?token{$token}=&redirect={$redirect}");
    }


    public function getExistingUser($serviceUser)
    {
            $userSocial = UserSocial::where('provider_id', $serviceUser->id)->first();
            return $userSocial ? $userSocial->user : null;
    }

    public function needsToCreateSocial(User $user, $service)
    {
        return !$user->hasSocialLinked($service);
    }




}
