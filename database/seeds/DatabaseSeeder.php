<?php

use App\Course;
use App\Group;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        \Artisan::call('passport:install');

         $this->call(AdminSeeder::class);

         // 初始必须创建一个群, id设置为1, 这样第一个登录进来的用户才可以填写口令, 注册成功
        factory(Group::class, 1)->create()->each(function($group){
            factory(\App\User::class, 10)->create([
                'group_id' => $group->id
            ]);
        });

        factory(Course::class, 3)->create()->each(function(Course $course){
           factory(\App\Lesson::class, 10)->create([
               'course_id' => $course->id
           ]);

           $course->lessons[0]->update([
               'free' => true
           ]);
        });
        \Artisan::call('cache:clear');

    }
}
