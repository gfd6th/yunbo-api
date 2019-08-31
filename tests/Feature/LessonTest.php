<?php

namespace Tests\Feature;

use App\Lesson;
use App\User;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LessonTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function normal_user_could_not_get_premium_lesson()
    {
        $user = factory(User::class)->create();

        $lesson = factory(Lesson::class)->create([
            'free'  => false,
            'video' => 'aaaa',
        ]);

        Passport::actingAs($user);

        $this->get('api/courses/' . $lesson->course->id)->assertJson([
            'data' => [
                'lessons' => [
                    [
                        'video' => '',
                    ],
                ],
            ],
        ]);

        $premiumUser =  $user = factory(User::class)->create([
            'expire_at' =>now()->addYear()
        ]);

        Passport::actingAs($premiumUser);

        $this->get('api/courses/' . $lesson->course->id)->assertJson([
            'data' => [
                'lessons' => [
                    [
                        'video' => 'aaaa',
                    ],
                ],
            ],
        ]);

    }


}
