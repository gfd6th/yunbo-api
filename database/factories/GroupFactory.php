<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Group;
use Faker\Generator as Faker;

$factory->define(Group::class, function (Faker $faker) {
    return [
        'name'    => $faker->word,
        'owner_id' => function () {
            return factory(App\User::class)->create(['group_id' => 1])->id;
        },
        'affiliate' => $faker->numberBetween(1,100),
        'code'    => 'rightCode',
    ];
});
