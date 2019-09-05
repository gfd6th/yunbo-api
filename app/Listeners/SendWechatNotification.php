<?php

namespace App\Listeners;

use App\Events\OrderPaid;
use EasyWeChat\OfficialAccount\Application;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendWechatNotification implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  OrderPaid $event
     * @return void
     */
    public function handle(OrderPaid $event)
    {
        try{

        $group = $event->user->group;
        $user = $event->user;
        $affiliate = $event->price / 100 * ($group->affiliate / 100) . '元';
        $remark = "您的 <{$group->name}> 学员 {$user->name} 付款成为了 {$event->plan}, 您收到了{$affiliate}的分成";
        $this->sendToAdmin($group, $user, $event, $affiliate);
        $this->sendTo($group, $user, $affiliate, $remark);
        } catch (\Exception $exception){
            \Log::error($exception);
        }
    }

    protected function sendToAdmin($group, $user, $event, $affiliate)
    {
        $admins = explode('|', setting('admins'));
        $remark = "<{$group->name}> 的学员 {$user->name} 成为了{$event->plan}, 群主 {$group->owner->name} 收到了 ${affiliate} 的分成";
        foreach ($admins as $admin) {

            app('wechat.official_account')->template_message->send([
                'touser'      => $admin,
                'template_id' => 'Tn4HthQo3ELGc5xQO0jrC0hipK4UmA5QEqMcmAeqWW0',
//            'url' => 'https://easywechat.org',
//            'miniprogram' => [
//                'appid' => 'xxxxxxx',
//                'pagepath' => 'pages/xxx',
//            ],
                'data'        => [
                    'keyword1' => $user->name,
                    'keyword2' => $event->price/100 . '元',
                    'keyword4' => now()->format('Y/m/d H:i:s'),
                    'first'   => ['value' => $remark, 'color' => '#48BB78'],
                ],
            ]);
        }
    }

    /**
     * @param $group
     * @param $user
     * @param $affiliate
     * @param $remark
     */
    protected function sendTo($group, $user, $affiliate, $remark): void
    {
        app('wechat.official_account')->template_message->send([
            'touser'      => $group->owner->socialFor('wechat')->provider_id,
            'template_id' => 'Tn4HthQo3ELGc5xQO0jrC0hipK4UmA5QEqMcmAeqWW0',
//            'url' => 'https://easywechat.org',
//            'miniprogram' => [
//                'appid' => 'xxxxxxx',
//                'pagepath' => 'pages/xxx',
//            ],
            'data'        => [
                'keyword1' => $user->name,
                'keyword2' => $affiliate,
                'keyword4' => now()->format('Y/m/d H:i:s'),
                'first'   => ['value' => $remark, 'color' => '#48BB78'],
            ],
        ]);
    }
}
