<?php
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Services\DateService;
use Illuminate\Support\Facades\DB;

class AddPersonTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_add_person_with_required_fields_and_at_least_one_role()
    {
        // Arrange
        $user = factory(User::class)->create();
        $requestData = [
            'name' => 'John Doe',
            'birthdate' => '1990-01-01',
            'deathdate' => '2020-01-01',
            'bio' => 'Lorem ipsum',
            'notes' => 'Some notes',
            'links' => 'http://example.com',
            'is_avtor' => true,
            'is_translator' => false,
            'is_designer' => false,
            'is_illustrator' => false,
        ];

        // Act
        $response = $this->actingAs($user)->post('/add', $requestData);

        // Assert
        $response->assertStatus(200);
        $this->assertDatabaseHas('person', [
            'name' => 'John Doe',
            'birthdate' => DateService::formatDateToInt('1990-01-01'),
            'deathdate' => DateService::formatDateToInt('2020-01-01'),
            'bio' => 'Lorem ipsum',
            'notes' => 'Some notes',
            'links' => 'http://example.com',
            'is_avtor' => true,
            'is_translator' => false,
            'is_designer' => false,
            'is_illustrator' => false,
            'user_id' => $user->id,
            'is_public' => true,
        ]);
    }

    /** @test */
    public function it_fails_to_add_person_without_name()
    {
        // Arrange
        $user = factory(User::class)->create();
        $requestData = [
            // 'name' => 'John Doe', // Name is missing
            'birthdate' => '1990-01-01',
            'deathdate' => '2020-01-01',
            'is_avtor' => true,
        ];

        // Act
        $response = $this->actingAs($user)->post('/add', $requestData);

        // Assert
        $response->assertSessionHasErrors('name');
        $this->assertDatabaseMissing('person', ['user_id' => $user->id]);
    }

    /** @test */
    public function it_fails_to_add_person_without_any_role()
    {
        // Arrange
        $user = factory(User::class)->create();
        $requestData = [
            'name' => 'John Doe',
            'birthdate' => '1990-01-01',
            'deathdate' => '2020-01-01',
            // No role selected
        ];

        // Act
        $response = $this->actingAs($user)->post('/add', $requestData);

        // Assert
        $response->assertSessionHasErrors(['is_avtor', 'is_translator', 'is_designer', 'is_illustrator']);
        $this->assertDatabaseMissing('person', ['user_id' => $user->id]);
    }

    // Add more test cases as needed
}
