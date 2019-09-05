<?php

namespace App\Admin\Forms;

use Encore\Admin\Widgets\Form;
use Illuminate\Http\Request;
use Setting as SiteSetting;

class Setting extends Form
{
    /**
     * The form title.
     *
     * @var string
     */
    public $title = '设置';

    /**
     * Handle the form request.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request)
    {
        try {
            $yearly = $request->get('yearly');
            $lifetime = $request->get('lifetime');
            setting([
               'plans.yearly.price' =>  $yearly * 100,
               'plans.lifetime.price' =>  $lifetime * 100,
                'admins' => $request->admins
            ])->save();
            admin_success('修改成功');
        } catch (\Exception $e) {
            admin_success('数据处理成功失败.');

        } finally {
            return back();

        }


    }

    /**
     * Build a form here.
     */
    public function form()
    {
        $this->text('yearly', '年费会员价格(元)')->rules('required');
        $this->text('lifetime', '终身会员价格(元)')->rules('required');
        $this->text('admins', '管理员openid(已"|"分隔)')->rules('required');

//        $this->email('email')->rules('email');
//        $this->datetime('created_at');
    }

    /**
     * The data of the form.
     *
     * @return array $data
     */
    public function data()
    {
        return [
            'yearly'   => SiteSetting::get('plans.yearly.price') / 100,
            'lifetime' => SiteSetting::get('plans.lifetime.price') / 100,
            'admins' => SiteSetting::get('admins')
        ];
    }
}
