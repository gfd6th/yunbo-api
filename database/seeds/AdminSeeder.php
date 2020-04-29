<?php

use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \Artisan::call('admin:install');
        \DB::table('admin_menu')->insert([
            'parent_id' => 0,
            'order' => 0,
            'title' => '会员管理',
            'icon' => 'fa-user',
            'uri' => '/users'
        ]);

        \DB::table('admin_menu')->insert([
            'parent_id' => 0,
            'order' => 0,
            'title' => '群管理',
            'icon' => 'fa-users',
            'uri' => '/groups'
        ]);

        \DB::table('admin_menu')->insert([
            'parent_id' => 0,
            'order' => 0,
            'title' => '课程管理',
            'icon' => 'fa-book',
            'uri' => '/courses'
        ]);

        \DB::table('admin_menu')->insert([
            'parent_id' => 0,
            'order' => 0,
            'title' => '订单管理',
            'icon' => 'fa-money',
            'uri' => '/orders'
        ]);
        \DB::table('admin_menu')->insert([
            'parent_id' => 0,
            'order' => 0,
            'title' => '设置',
            'icon' => 'fa-gear',
            'uri' => '/settings'
        ]);


    }
}
