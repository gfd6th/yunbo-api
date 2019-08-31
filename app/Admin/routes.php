<?php

use Illuminate\Routing\Router;

Admin::routes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index')->name('admin.home');
    $router->get('settings', 'SettingController@index');

    $router->resource('users', UserController::class);
    $router->resource('groups', GroupController::class);
    $router->resource('courses', CourseController::class);
    $router->resource('lessons', LessonController::class);
    $router->resource('orders', OrderController::class);
});
