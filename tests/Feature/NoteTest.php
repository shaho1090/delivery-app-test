<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class NoteTest extends TestCase
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
            ])->assertJsonFragment([
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email
            ]);

        $this->assertDatabaseHas('notes', [
            'author_id' => $user->id,
            'title' => $noteData['title'],
            'note' => $noteData['note']
        ]);

    }

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

    public function test_a_user_can_see_all_its_own_notes_with_pagination()
    {
        $user = User::factory()->hasNotes(2)->create();
        $anotherUser = User::factory()->hasNotes(2)->create();

        Sanctum::actingAs($user);

        $this->getJson(route('note.index'))->dump()
            ->assertJsonFragment([
                'title' => $user->notes()->first()->title,
                'note' => $user->notes()->first()->note,
            ])->assertJsonFragment([
                'title' => $user->notes()->get()->last()->title,
                'note' => $user->notes()->get()->last()->note,
            ])->assertJsonMissing([
                'title' => $anotherUser->notes()->first()->title,
                'note' => $anotherUser->notes()->first()->note,
            ])->assertJsonMissing([
                'title' => $anotherUser->notes()->get()->last()->title,
                'note' => $anotherUser->notes()->get()->last()->note,
            ]);
    }
}
