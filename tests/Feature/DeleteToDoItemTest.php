<?php

namespace Tests\Feature;

use App\ToDoItem;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DeleteToDoItemTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
        $this->otherUser = factory(User::class)->create();
        $this->toDoItem = factory(ToDoItem::class)->create(['user_id' => $this->user->id]);
    }

    public function userCanDeleteTheirToDoItem() : void
    {
        $this->actingAs($this->user)
            ->deleteJson('/to-do-item/' . $this->toDoItem->id)
            ->assertSuccessful();

        $this->assertDatabaseMissing('users', [
            'id' => 1,
            'owner_id' => $this->user->id,
            'body' => 'This is an example of an item that I need to do.',
            'email' => 'test@test.app',
        ]);
    }

    public function userCannotDeleteOtherUserToDoItem() : void
    {
        $this->actingAs($this->otherUser)
            ->deleteJson('/to-do-item/' . $this->toDoItem->id)
            ->assertStatus(401);

        $this->assertDatabaseHas('users', [
            'id' => 1,
            'owner_id' => $this->user->id,
            'body' => 'This is an example of an item that I need to do.',
            'email' => 'test@test.app',
        ]);
    }
}