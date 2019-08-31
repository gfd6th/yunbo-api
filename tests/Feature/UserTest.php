<?php

namespace Tests\Feature;

use App\Group;
use App\User;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    /** @test */
    public function login_user_could_get_his_own_info()
    {
        $user = factory(User::class)->create(
            ['group_id' => factory(Group::class)->create()->id]
        );
        Passport::actingAs($user);
        $response = $this->get('api/me');
        $response->assertJson([
            "data" => [
                "name" => $user->name
            ]
        ]);
    }
}
