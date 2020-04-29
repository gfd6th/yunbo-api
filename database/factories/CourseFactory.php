<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Course;
use Faker\Generator as Faker;

$factory->define(Course::class, function (Faker $faker) {
    return [
        'title' => $faker->sentence,
        'free' => $faker->boolean,
        'level' => $faker->randomElement(['初级', '中级', '高级']),
        'intro' => $faker->sentence,
        'img' => 'http://placehold.it/200x200'

    ];
});
