<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WechatController extends Controller
{
    public function wxjssdk(Request $request)
    {
        $url = $request->url;
        $config = $request->config;
        $debug = $request->get('debug', false);
//        dump(app('wechat.official_account')->access_token->getToken());
        return  app('wechat.official_account')->jssdk->setUrl($url)->buildConfig($config, $debug);
    }
}
