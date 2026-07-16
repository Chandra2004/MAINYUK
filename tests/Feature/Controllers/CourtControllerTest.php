<?php

namespace Tests\Feature\Controllers;

use Tests\TestCase;
use App\Models\User;
use App\Models\Court;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CourtControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_loads_for_authenticated_users()
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)->get('/dashboard');
        
        $response->assertStatus(200);
    }

    public function test_dashboard_redirects_for_guests()
    {
        $response = $this->get('/dashboard');
        
        $response->assertRedirect('/login');
    }

    public function test_court_detail_loads_for_public()
    {
        $court = Court::create([
            'name' => 'Test Court',
            'sport_type' => 'futsal',
            'location' => 'Test Location',
            'city' => 'Surabaya',
            'address' => 'Test Address',
            'price_per_hour' => 100000,
            'is_active' => true,
            'slug' => 'test-court'
        ]);

        $response = $this->get('/courts/' . $court->id);
        
        $response->assertStatus(200);
    }
}
