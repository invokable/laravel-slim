<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $user = User::factory()->create();

        $token = $user->createToken('test')->plainTextToken;

        $response = $this->actingAs($user)->withToken($token)->get('api/user');

        $response->assertStatus(200)
            ->assertJson([
                'name' => $user->name,
            ]);

        $this->assertStringStartsWith('1|', $token);
    }
}
