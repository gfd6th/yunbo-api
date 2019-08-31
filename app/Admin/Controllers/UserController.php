<?php

namespace App\Admin\Controllers;

use App\Group;
use App\User;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Carbon\Carbon;

class UserController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '用户管理';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new User);
        $grid->disableExport();
        $grid->disableCreateButton();
        $groups = Group::all(['id', 'name'])->pluck('name', 'id')->toArray();
        $grid = $this->rendGrid($grid);
        $grid->quickSearch('name', 'phone');
        $grid->disableActions();
        $grid->disableBatchActions();
        $this->rendFilters($grid, $groups);

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
        $show = new Show(User::findOrFail($id));
        $show->panel()
            ->tools(function ($tools) {
                $tools->disableDelete();
            });
        $show->field('id', __('编号'));
        $show->field('name', __('姓名'));
        $show->field('phone', __('手机'));
        $show->field('avatar', __('头像'))->image('', 100, 100);
        $show->group(__('群'), function ($group) {
            $group->setResource('/admin/groups');
            $group->panel()
                ->tools(function ($tools) {
                    $tools->disableDelete();
                });;
            $group->name();
        });
        $show->ownGroup('群主', function ($groups) {
            $groups->setResource('/admin/groups');
            $groups->disableActions();
            $groups->disableBatchActions();
            $groups->disableExport();
            $groups->disableCreateButton();
            $groups->name('群名');
            $groups->profit('待支付');
        });
        $show->field('lifetime', __('终身会员'))->using([
            0 => '否',
            1 => '是',
        ]);
        $show->field('forbidden', __('冻结'))->using([
            0 => '否',
            1 => '是',
        ]);
        $show->field('expire_at', __('会员到期'));
        $show->field('created_at', __('加入时间'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new User);
        $form->disableCreatingCheck();
        $form->disableEditingCheck();

        $form->tools(function ($tools) {
            $tools->disableDelete();
        });
        $groups = Group::all(['id', 'name'])->pluck('name', 'id')->toArray();

        $form->text('name', __('姓名'))->required();
        $form->mobile('phone', __('手机'))->required()->options(['mask' => '999 9999 9999']);;
        $form->image('avatar', __('头像'));
        $form->select('group_id', __('群'))->options($groups);
        $form->switch('lifetime', __('终身会员'));
        $form->switch('forbidden', __('冻结'));
        $form->datetime('expire_at', __('会员到期'))->default(null);

        return $form;
    }

    /**
     * @param $grid
     * @return array
     */
    protected function rendGrid($grid)
    {

        $grid->column('id', __('编号'))->width(70)->sortable();
        $grid->column('avatar', __('头像'))->image('', 50, 50);
        $grid->column('name', __('姓名'))->display(function ($name) {
            $route = route('users.edit', $this->id);
            return "<a href='{$route}'>{$name}</a>";
        })->filter('like');

        $grid->column('phone', __('手机'))->filter('like');
        $grid->column('group.name', __('群'))->display(function ($name) {
            $route = route('groups.show', $this->group ? $this->group : 1);
            return "<a href='{$route}'>{$name}</a>";
        });
        $grid->column('ownGroup', __('群主'))->display(function ($group) {
            $count = count($group);
            return $count > 0 ? '<i class="fa fa-group text-primary" aria-hidden="true"></i>' . "({$count})" : '';
        });
        $grid->column('lifetime', __('终身会员'))->display(function ($lifetime) {
            return $lifetime ? '<i class="fa fa-certificate text-success" aria-hidden="true"></i>' : '';
        });
        $grid->column('forbidden', __('冻结'))->display(function ($lifetime) {
            return $lifetime ? '<i class="fa fa-ban text-danger" aria-hidden="true"></i>' : '';
        });
        $grid->column('expire_at', __('会员到期'))->sortable();
        $grid->column('created_at', __('加入时间'))->sortable();
        return $grid;
    }

    /**
     * @param $grid
     * @param $groups
     */
    protected function rendFilters($grid, $groups): void
    {
        $grid->filter(function ($filter) use ($groups) {

            $filter->disableIdFilter();
            // 设置created_at字段的范围查询
            $filter->like('name', '姓名');
            $filter->like('phone', '手机');
            $filter->in('group_id', '群')->multipleSelect($groups);
            $filter->where(function ($query) {
                if (in_array(1, $this->input)) {
                    $query->has('ownGroup');
                }
                if (in_array(2, $this->input)) {
                    $query->where('forbidden', 1);
                }
                if (in_array(3, $this->input)) {
                    $query->where('lifetime', 1);
                }

            }, '')->checkbox([
                1 => '群主',
                2 => '冻结',
                3 => '终身会员',
            ]);
            $filter->where(function ($query) {
                switch ($this->input) {
                    case 0:
                        $query->where('expire_at', '>', now());
                        break;
                    case 1:
                        $query->where('expire_at', '<', now());
                        break;
                    case 2:
                        $query->whereNotNull('expire_at');
                }
            }, '付费')->radio([
                2 => '全部',
                0 => '年费',
                1 => '过期',
            ]);
            /**
             * @param $filter
             */
            function renderDatetimeFilter($filter, $type, $label): void
            {
                $filter->where(function ($q) use ($type) {
                    $now = now();
                    if ($type === 'expire_at') {
                        $now->addYear();
                    }
                    switch ($this->input) {
                        case 6;
                            $q->where($type, '>', $now->subDay()->startOfDay());
                            break;
                        case 0:
                            $q->where($type, '>', $now->today());
                            break;
                        case 1:
                            $q->where($type, '>', $now->startOfWeek());
                            break;
                        case 2:
                            $q->where($type, '>', $now->startOfMonth());
                            break;

                        case 3:
                            $q->where($type, '>', $now->subMonth(3));
                            break;

                        case 4:
                            $q->where($type, '>', $now->subMonth(6));
                            break;

                        case 5:
                            $q->where($type, '>', $now->subYear());
                            break;

                    }
                }, $label)->radio([
                    '6' => '昨天',
                    '0' => '今天',
                    '1' => '本周',
                    '2' => '本月',
                    '3' => '三月内',
                    '4' => '半年内',
                    '5' => '一年内',
                ]);
//                $filter->where(function($q){
//                    dd($this->input);
//                }, ' ')->between($type, ' ')->datetime();
            }

            renderDatetimeFilter($filter, 'created_at', '加入时间');
            renderDatetimeFilter($filter, 'expire_at', '付费时间');

        });
    }
}
