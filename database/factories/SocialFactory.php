<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\UserSocial;
use Faker\Generator as Faker;

$factory->define(UserSocial::class, function (Faker $faker) {
    return [
        'user_id'     => function () {
            return factory(\App\User::class)->create()->id;
        },
        'type'        => 'wechat',
        'provider_id' => 1234567,
    ];
});
