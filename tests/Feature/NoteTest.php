<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
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

       $this->postJson(route('note.store'),$noteData)->dump();

   }
}
