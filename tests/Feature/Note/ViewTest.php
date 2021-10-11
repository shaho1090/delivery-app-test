<?php

namespace Tests\Feature\Note;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ViewTest extends TestCase
{
    public function test_a_user_can_see_all_its_own_notes_with_pagination()
    {
        $user = User::factory()->hasNotes(2)->create();
        $anotherUser = User::factory()->hasNotes(2)->create();

        Sanctum::actingAs($user);

        $this->getJson(route('note.index'))
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

    public function test_a_user_can_see_a_single_its_own_note()
    {
        $user = User::factory()->hasNotes(2)->create();

        Sanctum::actingAs($user);

        $this->getJson(route('note.show', $user->notes()->first()))
            ->assertJsonFragment([
                'title' => $user->notes()->first()->title,
                'note' => $user->notes()->first()->note,
            ])->assertJsonMissing([
                'title' => $user->notes()->get()->last()->title,
                'note' => $user->notes()->get()->last()->note,
            ]);
    }

    public function test_a_user_can_not_see_a_single_note_of_other_users()
    {
        $user = User::factory()->hasNotes(2)->create();
        $anotherUser = User::factory()->hasNotes(2)->create();

        Sanctum::actingAs($user);

        $this->getJson(route('note.show', $anotherUser->notes()->first()))
            ->assertForbidden()
            ->assertJsonMissing([
                'title' => $user->notes()->get()->last()->title,
                'note' => $user->notes()->get()->last()->note,
            ]);
    }
}
