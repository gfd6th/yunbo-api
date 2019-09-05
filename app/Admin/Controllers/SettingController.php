<?php

namespace App\Admin\Controllers;

use App\Admin\Forms\Setting;
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

class SettingController extends Controller
{
    public function index(Content $content)
    {

        return $content
            ->title('网站设置')
            ->body(new Setting());
    }

    public function clearCache()
    {
        \Cache::flush();
        return response()->json('ok', 200);
    }
}
