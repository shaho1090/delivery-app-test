<?php

namespace Tests\Feature\Note;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DeleteTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_a_user_can_delete_one_of_its_own_notes()
    {
        $user = User::factory()->hasNotes(3)->create();

        Sanctum::actingAs($user);

        $note = $user->notes()->first();

        $this->assertDatabaseHas('notes', $note->toArray());

        $this->deleteJson(route('note.delete',$note))
        ->assertStatus(200);

        $this->assertDatabaseMissing('notes', $note->toArray());
    }

    public function test_a_user_can_not_delete_note_that_belongs_to_others()
    {
        $user = User::factory()->hasNotes(3)->create();
        $anotherUser = User::factory()->hasNotes(3)->create();

        Sanctum::actingAs($user);

        $note = $anotherUser->notes()->first();

        $this->deleteJson(route('note.delete',$note))
            ->assertForbidden();

        $this->assertDatabaseHas('notes', $note->toArray());
    }
}
