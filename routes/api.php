<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::get('auth/login/{provider}', 'SocialiteController@redirect');
Route::get('auth/login/{provider}/callback', 'SocialiteController@handleProviderCallback');
Route::post('auth/register/user-info', 'SocialiteController@userInfo');
Route::get('auth/register/group', 'SocialiteController@groupInfo');

Route::post('pay/{provider}/notify', 'OrderController@notify');
Route::middleware('auth:api')->post('pay/{provider}', 'OrderController@wechatPay');

Route::any('/wechat', 'SocialiteController@serve');
Route::middleware('auth:api')->get('me', 'UserController@me');
Route::middleware('auth:api')->get('more-info', 'UserController@moreInfo');
Route::resource('courses', 'CourseController');
Route::middleware('auth:api')->get('groups', 'GroupController@show');

Route::post('wxjssdk', 'WechatController@wxjssdk');
