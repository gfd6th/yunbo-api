<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Lesson;
use Faker\Generator as Faker;

$factory->define(Lesson::class, function (Faker $faker) {
    return [
        'title'     => $faker->sentence,
        'intro'     => $faker->sentence,
        'video'     => $faker->randomElement([
            'http://pan.ghexu.com/down/%E6%AD%A6%E6%B1%89%E9%9D%9E%E5%85%B8/Sp.005%20%E5%85%B3%E4%BA%8E%E6%96%B0%E5%86%A0%E8%82%BA%E7%82%8E%E7%9A%84%E4%B8%80%E5%88%87-0ySYM4kRJVY.mp4',
            'https://www.kj.com/sites/default/files/video/530262769.mp4',
            'http://pan.ghexu.com/down/refactoring ui/lesson1.mp4',
            'http://pan.ghexu.com/down/refactoring ui/lesson2.mp4',
            'http://pan.ghexu.com/down/refactoring ui/lesson3.mp4',
        ]),
        'course_id' => function () {
            return factory(\App\Course::class)->create()->id;
        },
        'free'      => $faker->boolean,
    ];
});

