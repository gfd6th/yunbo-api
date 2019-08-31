<?php

namespace App\Admin\Controllers;

use App\Course;
use App\Group;
use App\Http\Controllers\Controller;
use App\Lesson;
use App\User;
use Carbon\Carbon;
use Encore\Admin\Controllers\Dashboard;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Encore\Admin\Widgets\Box;

class HomeController extends Controller
{
    public function index(Content $content)
    {

        $users = User::all();
        $groups = Group::with('members')->get();
//
        $data = $groups->pluck('paid_stat', 'name')->transform(function ($i, $key) {
            $j = [];
            $j['name'] = $key;
            $j['普通用户'] = 0;
            $j['年费用户'] = 0;
            $j['终身用户'] = 0;

            return array_merge($j, $i->toArray());
        })->values()->toJson();

//        dd($data);

        $courses = Course::all();
        $lessons = Lesson::all();
        return $content
            ->title('概况')
//            ->row(view('admin::home'))
            ->row("<h2 class=\"page-header\">课程统计</h2>")
            ->row(function (Row $row) use ($courses, $lessons) {
                $row->column(4, $this->renderCourseBox($courses, $lessons));
                $row->column(4, $this->renderCoursesIncrementMonthlyBox($courses));
                $row->column(4, $this->renderLessonsIncrementyBox($lessons));
            })
            ->row("<h2 class=\"page-header\">会员统计</h2>")
            ->row(function (Row $row) use ($users) {
                $row->column(4, $this->renderUserBox($users));
                $row->column(4, $this->renderUserIncrementMonthlyBox($users));
                $row->column(4, $this->renderUserIncrementYesterdayBox($users));
            })
            ->row("<h2 class=\"page-header\">群组统计</h2>")
            ->row(function (Row $row) use ($groups) {
//                $groups = Group::all();
                $row->column(4, $this->renderGroupBox($groups));
            })
            ->row("<h2 class=\"page-header\">用户分布</h2>")
            ->row(view('admin::home')->with(compact('data')));
    }

    protected function renderUserIncrementYesterdayBox($users)
    {
        $usersIncrements = $users->filter(function ($user) {
            return $user->created_at > now()->today();
        });

        $usersIncrementsPreminum = $usersIncrements->filter(function ($user) {
            return $user->isPremium();
        });

        return view('admin::partials.count-box', [
            'color' => 'red',
            'desc'  => '今日新增 付费用户/普通用户',
            'count' => "{$usersIncrementsPreminum->count()} / {$usersIncrements->count()}",
            'icon'  => 'fa-user-plus',
        ]);
    }

    private function renderUserIncrementMonthlyBox($users)
    {
        $usersIncrements = $users->filter(function ($user) {
            return $user->created_at > now()->startOfMonth();
        });

        $usersIncrementsPreminum = $usersIncrements->filter(function ($user) {
            return $user->isPremium();
        });

        return view('admin::partials.count-box', [
            'color' => 'blue',
            'desc'  => '本月新增 付费用户/普通用户',
            'count' => "{$usersIncrementsPreminum->count()} / {$usersIncrements->count()}",
            'icon'  => 'fa-user-plus',
        ]);
    }

    private function renderUserBox($users)
    {

        $usersLifetime = $users->filter(function ($user) {
            return $user->lifetime;
        })->count();

        $usersYearly = $users->filter(function ($user) {
            return $user->expire_at > now()->startOfYear()->addYear();
        })->count();

        return view('admin::partials.count-box', [
            'color' => 'green',
            'desc'  => '全部 终身用户/年费用户/普通用户',
            'count' => "{$usersLifetime} / {$usersYearly} / {$users->count()}",
            'icon'  => 'fa-user',
        ]);
    }

    private function renderCourseBox($courses, $lessons)
    {
        return view('admin::partials.count-box', [
            'color' => 'green',
            'desc'  => '全部 课时/课程',
            'count' => "{$lessons->count()} / {$courses->count()}",
            'icon'  => 'fa-book',
        ]);
    }

    private function renderCoursesIncrementMonthlyBox($courses)
    {

        $yearly = $courses->filter(function ($course) {
            return $course->created_at > now()->startOfYear();
        });

        $monthly = $courses->filter(function ($course) {
            return $course->created_at > now()->startOfMonth();
        });

        return view('admin::partials.count-box', [
            'color' => 'blue',
            'desc'  => '本月新增课程 / 本年新增课程',
            'count' => "{$monthly->count()} / {$yearly->count()}",
            'icon'  => 'fa-plus',
        ]);
    }

    private function renderLessonsIncrementyBox($lessons)
    {
        $yearly = $lessons->filter(function ($lesson) {
            return $lesson->created_at > now()->startOfYear();
        });

        $monthly = $lessons->filter(function ($lesson) {
            return $lesson->created_at > now()->startOfMonth();
        });

        $daily = $lessons->filter(function ($lesson) {
            return $lesson->created_at > today();
        });

        return view('admin::partials.count-box', [
            'color' => 'red',
            'desc'  => '本日新增课时 / 本月新增课时 / 本年新增课时',
            'count' => "{$daily->count()} / {$monthly->count()} / {$yearly->count()}",
            'icon'  => 'fa-plus',
        ]);
    }

    private function renderGroupBox($groups)
    {

        $daily = $groups->filter(function ($group) {
            return new Carbon($group['created_at']) > today();
        });

        $monthly = $groups->filter(function ($group) {
            return new Carbon($group['created_at']) > now()->startOfMonth();
        });


        return view('admin::partials.count-box', [
            'color' => 'green',
            'desc'  => '本日新增群组 / 本月新增群组 / 全部群组',
            'count' => "{$daily->count()} / {$monthly->count()} / {$groups->count()}",
            'icon'  => 'fa-group',
        ]);
    }
}
