<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    
    use RefreshDatabase;
    
    public function admin_can_access_admin_route()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);
        $response = $this->get('/adminka');
        $response->assertStatus(200);
    }
    
    public function moderator_cannot_access_admin_route()
    {
        $moderator = User::factory()->create(['role' => 'moderator']);
        $this->actingAs($moderator);
        $response = $this->get('/adminka');
        $response->assertStatus(403);
    }

    public function regular_user_cannot_access_admin_route()
    {
        $regularUser = User::factory()->create();
        $this->actingAs($regularUser);
        $response = $this->get('/adminka');
        $response->assertStatus(403);
    }
}
