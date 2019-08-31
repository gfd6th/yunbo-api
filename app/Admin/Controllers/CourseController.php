<?php

namespace App\Admin\Controllers;

use App\Course;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class CourseController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '课程管理';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Course);
        $grid->disableExport();
        $grid->column('id', __('Id'));
        $grid->column('img', __('封面'))->image('', 100, 100);
        $grid->column('lessons', "课时(免费/总计)")->display(function ($lessons) {
            $free = collect($lessons)->filter(function ($lesson) {
                return $lesson['free'];
            })->count();
            $total = count($lessons);
            return "{$free} / {$total}";
        })->width(100);
        $grid->column('title', __('课程名'))->width(250)->display(function($title){
            $url = url("admin/courses/{$this->id}/edit");
            return "<a href='{$url}'>$title</a>";
        });
        $grid->column('intro', __('课程介绍'))->editable()->width(250);
        $grid->column('free', __('免费'))->icon([
           1=>'unlock'
        ], '');
        $grid->column('active', __('可见'))->icon([
           0 => 'eye-slash'
        ], '');
        $grid->column('level', __('难度'))->label([
            '初级' => 'success',
            '中级' => 'warning',
            '高级' => 'danger'
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
        $show = new Show($course = Course::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('title', __('课程名'));
        $show->field('img', __('封面'))->image(200,200);
        $show->field('intro', __('课程介绍'));
        $show->field('free', __('免费'))->using([
            0 => '否',
            1 => '是'
        ]);
        $show->field('active', __('开放'))->using([
            0 => '否',
            1 => '是'
        ]);
        $show->field('level', __('难度'));
        $show->field('created_at', __('创建时间'));


        $show->lessons('课程', function ($lessons) {
            $lessons->model()->orderBy('order', 'asc');

            $lessons->setResource('/admin/lessons');
            $lessons->sortable();


            $lessons->free('免费')->icon([
                1 => 'unlock'
            ], '');
            $lessons->title('课时名')->display(function($title){
                $url = url("admin/lessons/{$this->id}");
                return "<a href='{$url}'>$title</a>";
            });
            $lessons->column('created_at', '创建时间');
            $lessons->disableExport();
        });

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Course);

        $form->text('title', __('课程名'));
        $form->image('img', __('封面'));
        $form->textarea('intro', __('课程介绍'));
        $form->switch('free', __('免费'));
        $form->switch('active', __('开放'))->default(1);
        $form->select('level', __('难度'))->options([
            '初级' => '初级',
            '中级' => "中级",
            '高级' => '高级'
        ]);

        return $form;
    }
}
