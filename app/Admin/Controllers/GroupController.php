<?php

namespace App\Admin\Controllers;

use App\Group;
use App\User;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class GroupController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '群管理';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid((new Group));
        $grid->model()->with('members');
        $grid->quickSearch('name');
        $grid->disableActions()->disableExport();
        $grid->disableBatchActions();
        $grid->column('id', __('Id'));
        $grid->column('name', __('群名'))->display(function ($name) {
            $url = url('admin/groups/' . $this->id . '/edit');
            return "<a href='{$url}'>{$name}</a>";
        });
        $grid->column('owner.name', __('群主'))->display(function ($name) {
            $url = url('admin/users/' . $this->owner_id);
            return "<a href='{$url}'>{$name}</a>";
        });
//        $grid->
        $grid->column('code', __('口令'))->editable();
        $grid->column('affiliate', __('提成比例(%)'))->editable();
        $grid->column('profit', __('待支付(元)'))->editable();
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
        $show = new Show(Group::findOrFail($id));

        $show->panel()
            ->tools(function ($tools) {
                $tools->disableDelete();
            });;
        $show->field('id', __('Id'));
        $show->field('name', __('群名'));
        $show->owner(__('群主'), function ($owner) {
            $owner->setResource('/admin/users');
            $owner->panel()
                ->tools(function ($tools) {
                    $tools->disableDelete();
                });;
            $owner->name();
        });
        $show->field('code', __('口令'));
        $show->field('affiliate', __('提成比例(%)'));
        $show->field('profit', __('待支付(元)'));
        $show->field('created_at', __('创建时间'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Group);
        $form->tools(function ($tools) {
            $tools->disableDelete();
        });
        $users = User::all(['id', 'name'])->pluck('name', 'id')->toArray();
        $form->text('name', __('群名'))->required();
        $form->select('owner_id', __('群主'))->setWidth(2)->required()->options($users)->required();
        $form->text('code', __('口令'))->required()->setWidth(2);
        $form->rate('affiliate', __('提成比例(%)'))->default(2)->setWidth(2);
        $form->currency('profit', __('待支付(元)'))->default(0)->setWidth(2);
        $form->saved(function ($form) {
            $form->model()->owner->update(['group_id' => null]);
        });
        return $form;
    }
}
