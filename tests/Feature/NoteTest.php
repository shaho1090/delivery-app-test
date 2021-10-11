<?php

namespace Tests\Feature;

use App\Models\Note;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class NoteTest extends TestCase
{
    use RefreshDatabase;

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

    public function test_a_user_can_update_a_title_of_a_note()
    {
        $user = User::factory()->hasNotes(3)->create();

        Sanctum::actingAs($user);

        $noteData =[
            'title' => 'another title for the note'
        ];

        $this->patchJson(route('note.update',$user->first()->id),$noteData)->dump();

        $this->assertSame( $user->first()->title, $noteData['title']);
    }
}
