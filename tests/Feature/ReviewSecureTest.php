<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class ReviewTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_prevents_sql_injection_when_adding_review()
    {
        // Arrange
        $user = factory(User::class)->create();
        $requestData = [
            'w_id' => '1',
            'text' => "Injected text'; DROP TABLE review;",
            'date' => '2024-05-20',
        ];

        // Act
        $response = $this->actingAs($user)->post('/add-review', $requestData);

        // Assert
        $response->assertStatus(200);
        $this->assertDatabaseMissing('review', ['text' => "Injected text'; DROP TABLE review;"]);
    }
}
