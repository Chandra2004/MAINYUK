<?php

namespace Tests\Feature\Controllers;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProfileControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_loads()
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)->get('/profile');
        
        $response->assertStatus(200);
    }

    public function test_update_profile_success()
    {
        $user = User::factory()->create([
            'name' => 'Old Name',
        ]);
        
        $response = $this->actingAs($user)->put('/profile', [
            'name' => 'New Name',
            'phone' => '08123456789',
        ]);
        
        $response->assertRedirect();
        $this->assertEquals('New Name', $user->fresh()->name);
    }
}
