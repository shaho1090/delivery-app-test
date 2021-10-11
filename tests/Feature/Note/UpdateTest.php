<?php

namespace Tests\Feature\Note;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_a_user_can_update_the_title_of_a_note()
    {
        $user = User::factory()->hasNotes(3)->create();

        Sanctum::actingAs($user);

        $noteData = [
            'title' => 'another title for the note'
        ];

        $note = $user->notes()->first();

        $this->patchJson(route('note.update', $note), $noteData)
            ->assertStatus(200);

        $note->refresh();

        $this->assertSame($note->title, $noteData['title']);
    }


    public function test_a_user_can_update_the_text_of_a_note()
    {
        $user = User::factory()->hasNotes(3)->create();

        Sanctum::actingAs($user);

        $noteData = [
            'note' => $this->faker->text
        ];

        $note = $user->notes()->first();

        $this->patchJson(route('note.update', $note), $noteData)
            ->assertStatus(200);

        $note->refresh();

        $this->assertSame($note->note, $noteData['note']);
    }

    public function test_a_user_can_not_update_note_of_others()
    {
        $user = User::factory()->hasNotes(3)->create();
        $anotherUser = User::factory()->hasNotes(3)->create();

        Sanctum::actingAs($anotherUser);

        $noteData = [
            'title' => $this->faker->sentence,
            'note' => $this->faker->text
        ];

        $note = $user->notes()->first();

        $this->patchJson(route('note.update', $note), $noteData)
            ->assertForbidden()
            ->assertStatus(403);

        $note->refresh();

        $this->assertNotSame($note->title, $noteData['title']);
        $this->assertNotSame($note->note, $noteData['note']);
    }

    public function test_the_title_must_be_at_least_3_characters_while_updating()
    {
        $user = User::factory()->hasNotes(3)->create();

        Sanctum::actingAs($user);

        $this->assertAuthenticated();

        $noteData = [
            'title' => 'AB',
            'note' => $this->faker->text
        ];

        $note = $user->notes()->first();

        $this->patchJson(route('note.update',$note), $noteData)
            ->assertStatus(422)
            ->assertJsonFragment([
                "message" => "The given data was invalid.",
                "errors" => [
                    "title" => [
                        0 => "The title must be at least 3 characters."
                    ]
                ]
            ]);

        $note->refresh();

        $this->assertNotSame($note->title, $noteData['title']);
        $this->assertNotSame($note->note, $noteData['note']);
    }

    public function test_the_title_must_not_be_greater_than_255_characters_while_updating()
    {
        $user = User::factory()->hasNotes(3)->create();

        Sanctum::actingAs($user);

        $this->assertAuthenticated();

        $noteData = [
            'title' => Str::random(256),
            'note' => $this->faker->text
        ];

        $note = $user->notes()->first();

        $this->patchJson(route('note.update',$note), $noteData)
            ->assertStatus(422)
            ->assertJsonFragment([
                "message" => "The given data was invalid.",
                "errors" => [
                    "title" => [
                        0 => "The title must not be greater than 255 characters."
                    ]
                ]
            ]);

        $note->refresh();

        $this->assertNotSame($note->title, $noteData['title']);
        $this->assertNotSame($note->note, $noteData['note']);
    }

    public function test_the_text_of_a_note_must_be_at_least_3_characters_while_updating()
    {
        $user = User::factory()->hasNotes(3)->create();

        Sanctum::actingAs($user);

        $this->assertAuthenticated();

        $noteData = [
            'title' => $this->faker->sentence,
            'note' => 'AB'
        ];

        $note = $user->notes()->first();

        $this->patchJson(route('note.update',$note), $noteData)
            ->assertStatus(422)
            ->assertJsonFragment([
                "message" => "The given data was invalid.",
                "errors" => [
                    "note" => [
                        0 => "The note must be at least 3 characters."
                    ]
                ]
            ]);

        $note->refresh();

        $this->assertNotSame($note->title, $noteData['title']);
        $this->assertNotSame($note->note, $noteData['note']);
    }

    public function test_the_text_of_a_note_must_not_be_greater_than_1000_characters_while_updating()
    {
        $user = User::factory()->hasNotes(3)->create();

        Sanctum::actingAs($user);

        $this->assertAuthenticated();

        $noteData = [
            'title' => $this->faker->sentence,
            'note' => Str::random(1001)
        ];

        $note = $user->notes()->first();

        $this->patchJson(route('note.update',$note), $noteData)
            ->assertStatus(422)
            ->assertJsonFragment([
                "message" => "The given data was invalid.",
                "errors" => [
                    "note" => [
                        0 => "The note must not be greater than 1000 characters."
                    ]
                ]
            ]);

        $note->refresh();

        $this->assertNotSame($note->title, $noteData['title']);
        $this->assertNotSame($note->note, $noteData['note']);
    }
}
