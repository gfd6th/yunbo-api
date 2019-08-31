<?php

namespace Tests\Feature;

use App\Group;
use App\User;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GroupTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function only_group_owner_could_see_his_group_members()
    {
        $owner = factory(User::class)->create();

        $user = factory(User::class)->create();

        $group = factory(Group::class)->create([
            'owner_id' => $owner->id,
        ]);

        Passport::actingAs($user);
        $this->get('/api/groups')
            ->assertStatus(403);

        Passport::actingAs($owner);
        $this->withoutExceptionHandling()->get('/api/groups')
            ->assertStatus(200);


    }


}
