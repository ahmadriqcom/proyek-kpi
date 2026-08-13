<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_application_returns_a_successful_response(): void
    {
        $this->seed(PermissionSeeder::class);
        $user = User::factory()->create(['role' => 'super_admin']);
        $this->actingAs($user);

        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
