<?php

namespace App\Http\Controllers;

use App\Order;
use App\UserSocial;
use EasyWeChat\Factory;
use Hamcrest\Core\Set;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Setting;

class OrderController extends Controller
{

    private $payment;

    public function __construct()
    {
        // notify_url 可以单次动态设定
        $config = config('wechat.payment.default');
        $this->payment = Factory::payment($config);

    }


    protected function tradeNo()
    {
        $now = now();
        return $now->timestamp . $now->micro;
    }

    public function wechatPay(Request $request)
    {
//        dump($this->payment);
        $plan = Setting::get("plans.{$request->plan}");
        $trade_no = $this->tradeNo();
        $response = $this->payment->order->unify([
            'body'         => "云泊硬笔{$plan['name']}会员",
            'out_trade_no' => $trade_no,
            'total_fee'    => $plan['price'],
//        'spbill_create_ip' => '123.12.12.123', // 可选，如不传该参数，SDK 将会自动获取相应 IP 地址
//        'notify_url' => 'https://pay.weixin.qq.com/wxpay/pay.action', // 支付结果通知网址，如果不设置则会使用配置里的默认地址
            'notify_url'         => 'http://yunbo.api.vifashion.cn/api/pay/wechat/notify',                           // 默认支付结果通知地址

            'trade_type'   => 'JSAPI', // 请对应换成你的支付方式对应的值类型
            'openid'       => $request->user()->socialFor('wechat')->provider_id,
        ]);

        if ($response['return_code'] === 'SUCCESS' && $response['result_code'] === 'SUCCESS') {
            $jssdk = $this->payment->jssdk;
            Order::create([
                'user_id' => auth()->id(),
                'plan' => $plan['name'],
                'price' => $plan['price'],
                'order_id' => $trade_no
            ]);
            return $jssdk->bridgeConfig($response['prepay_id']);
        }

        return 'error';
    }

    public function notify($provider)
    {
        $response = $this->payment->handlePaidNotify(function ($message, $fail) use($provider) {
            // 你的逻辑
            if ($message['return_code'] === 'SUCCESS') { // return_code 表示通信状态，不代表支付状态
                // 用户是否支付成功
                if ($message['result_code'] === 'SUCCESS' && $order = Order::where('order_id',$message['out_trade_no'] )->where('is_paid', false)->firstOrFail()) {
                    $order->user->paidForPlan($message['total_fee']);
                    $order->is_paid = true;
                    $order->save();
                }
            return true;
            }
            // 或者错误消息
            $fail('Order not exists.');
        });

        return $response; // Laravel 里请使用：return $response;
    }


}
