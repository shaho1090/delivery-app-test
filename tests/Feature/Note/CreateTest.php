<?php

namespace Tests\Feature\Note;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CreateTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_a_user_can_create_a_note()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $this->assertAuthenticated();

        $noteData = [
            'title' => 'simple title',
            'note' => 'this is simple note'
        ];

        $this->postJson(route('note.store'), $noteData)
            ->assertStatus(200)
            ->assertJsonFragment([
                'title' => $noteData['title'],
                'note' => $noteData['note']
            ]);

        $this->assertDatabaseHas('notes', [
            'author_id' => $user->id,
            'title' => $noteData['title'],
            'note' => $noteData['note']
        ]);
    }

    public function test_the_title_is_required_when_creating_a_note()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $this->assertAuthenticated();

        $noteData = [
            'title' => '',
            'note' => 'this is simple note'
        ];

        $this->postJson(route('note.store'), $noteData)
            ->assertStatus(422)
            ->assertJsonFragment([
                "message" => "The given data was invalid.",
                "errors" => [
                    "title" => [
                        0 => "The title field is required."
                    ]
                ]
            ]);

        $this->assertDatabaseMissing('notes', [
            'author_id' => $user->id,
            'title' => $noteData['title'],
            'note' => $noteData['note']
        ]);
    }

    public function test_the_note_is_required_when_creating_a_note()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $this->assertAuthenticated();

        $noteData = [
            'title' => 'title',
            'note' => ''
        ];

        $this->postJson(route('note.store'), $noteData)
            ->assertStatus(422)
            ->assertJsonFragment([
                "message" => "The given data was invalid.",
                "errors" => [
                    "note" => [
                        0 => "The note field is required."
                    ]
                ]
            ]);

        $this->assertDatabaseMissing('notes', [
            'author_id' => $user->id,
            'title' => $noteData['title'],
            'note' => $noteData['note']
        ]);
    }

    public function test_the_title_must_be_at_least_3_characters_when_creating_a_note()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $this->assertAuthenticated();

        $noteData = [
            'title' => 'AB',
            'note' => $this->faker->text
        ];

        $this->postJson(route('note.store'), $noteData)
            ->assertStatus(422)
            ->assertJsonFragment([
                "message" => "The given data was invalid.",
                "errors" => [
                    "title" => [
                        0 => "The title must be at least 3 characters."
                    ]
                ]
            ]);

        $this->assertDatabaseMissing('notes', [
            'author_id' => $user->id,
            'title' => $noteData['title'],
            'note' => $noteData['note']
        ]);
    }

    public function test_the_title_must_not_be_greater_than_255_characters_when_creating_a_note()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $this->assertAuthenticated();

        $noteData = [
            'title' => Str::random(256),
            'note' => $this->faker->text
        ];

        $this->postJson(route('note.store'), $noteData)
            ->assertStatus(422)
            ->assertJsonFragment([
                "message" => "The given data was invalid.",
                "errors" => [
                    "title" => [
                        0 => "The title must not be greater than 255 characters."
                    ]
                ]
            ]);

        $this->assertDatabaseMissing('notes', [
            'author_id' => $user->id,
            'title' => $noteData['title'],
            'note' => $noteData['note']
        ]);
    }

    public function test_the_text_of_a_note_must_be_at_least_3_characters_when_creating()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $this->assertAuthenticated();

        $noteData = [
            'title' => $this->faker->sentence,
            'note' => 'AB'
        ];

        $this->postJson(route('note.store'), $noteData)
            ->assertStatus(422)
            ->assertJsonFragment([
                "message" => "The given data was invalid.",
                "errors" => [
                    "note" => [
                        0 => "The note must be at least 3 characters."
                    ]
                ]
            ]);

        $this->assertDatabaseMissing('notes', [
            'author_id' => $user->id,
            'title' => $noteData['title'],
            'note' => $noteData['note']
        ]);
    }

    public function test_the_text_of_a_note_must_not_be_greater_than_1000_characters_when_creating()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $this->assertAuthenticated();

        $noteData = [
            'title' => $this->faker->sentence,
            'note' => Str::random(1001)
        ];

        $this->postJson(route('note.store'), $noteData)
            ->assertStatus(422)
            ->assertJsonFragment([
                "message" => "The given data was invalid.",
                "errors" => [
                    "note" => [
                        0 => "The note must not be greater than 1000 characters."
                    ]
                ]
            ]);

        $this->assertDatabaseMissing('notes', [
            'author_id' => $user->id,
            'title' => $noteData['title'],
            'note' => $noteData['note']
        ]);
    }
}
