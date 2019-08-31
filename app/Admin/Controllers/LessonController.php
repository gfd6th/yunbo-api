<?php

namespace App\Admin\Controllers;

use App\Course;
use App\Lesson;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class LessonController extends AdminController
{
    /**
     * 课时名 for current resource.
     *
     * @var string
     */
    protected $title = '所有课时';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Lesson);
        $grid->disableExport();
        $grid->quickSearch('title', 'intro');
        $grid->column('id', __('Id'));
        $grid->column('title', __('课时名'))->display(function($title){
            $url = url('admin/lessons/' . $this->id . '/edit');
            return "<a href='{$url}'>{$title}</a>" ;
        });
        $grid->column('intro', __('课时介绍'))->editable();
        $grid->column('video', __('视频链接'))->link();
        $grid->column('course', __('课程'))->display(function($course){
            $url = url('admin/courses/' . $course['id']);
            $title = $course['title'];
                        return "<a href='{$url}'>{$title}</a>" ;
        });;
        $grid->column('free', __('免费'))->icon([
            1=> 'unlock',
        ], '')->width(50);
        $grid->column('created_at', __('创建时间'))->width(80);

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
        $show = new Show(Lesson::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('title', __('课时名'));
        $show->field('intro', __('课时介绍'));
        $show->field('video', __('视频链接'))->link();
        $show->field('free', __('免费'))->using([
            0 => '否',
            1 => '是'
        ]);
        $show->field('course_id', __('课程'))->unescape()->as(function($course_id){
            $course = $this->course;
            $url = url('admin/courses/' . $course['id']);
            return "<a href='{$url}'>{$course['title']}</a>";
        });
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
        $form = new Form(new Lesson);
        $courses = Course::all()->pluck('title', 'id')->toArray();
        $form->text('title', __('课时名'));
        $form->text('intro', __('课时介绍'));
        $form->text('video', __('视频链接'));
        $form->switch('free', __('免费'));
        $form->select('course_id', __('课程'))->options($courses)->default(request()->get('course_id'));

        return $form;
    }
}
