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
    }
}
