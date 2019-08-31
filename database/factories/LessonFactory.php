<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Lesson;
use Faker\Generator as Faker;

$factory->define(Lesson::class, function (Faker $faker) {
    return [
        'title' => $faker->sentence,
        'intro' => $faker->sentence,
        'video' => $faker->randomElement([
            'https://clips.vorwaerts-gmbh.de/big_buck_bunny.mp4',
            'https://www.kj.com/sites/default/files/video/530262769.mp4'
        ]),
        'course_id' => function(){
            return factory(\App\Course::class)->create()->id;
        },
        'free' => $faker->boolean,
    ];
});
