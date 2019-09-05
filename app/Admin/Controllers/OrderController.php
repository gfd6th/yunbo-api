<?php

namespace App\Admin\Controllers;

use App\Order;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class OrderController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '订单管理';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Order);
        $grid->filter(function ($filter) {

            $filter->disableIdFilter();
            // 设置created_at字段的范围查询
            $filter->equal('is_paid', '支付状态')->radio([
                '' => '全部',
                0  => '未支付',
                1  => '已支付',
            ]);
        });
        $grid->disableExport();
        $grid->disableCreateButton();
        $grid->column('id', __('Id'));
        $grid->column('order_id', __('订单编号'));
        $grid->column('user.name', __('用户'));
        $grid->column('group.name', '群');
        $grid->column('plan', __('会员类型'));
        $grid->column('price', __('价格'));
        $grid->column('is_paid', __('支付状态'))->using([
            0 => '',
            1 => '已支付'
        ]);
        $grid->column('created_at', __('创建时间'));
        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(Order::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('order_id', __('订单编号'));
        $show->field('user_id', __('用户'));
        $show->field('plan', __('会员类型'));
        $show->field('price', __('价格'));
        $show->field('is_paid', __('Is paid'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Order);

        $form->textarea('order_id', __('订单编号'));
        $form->number('user_id', __('用户'));
        $form->textarea('plan', __('会员类型'));
        $form->number('price', __('价格'));
        $form->switch('is_paid', __('Is paid'));

        return $form;
    }
}
