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
        $this->toDoItem = factory(ToDoItem::class)->create(['owner_id' => $this->user->id]);
    }

    /** @test */
    public function userCanDeleteTheirToDoItem() : void
    {
        $this->actingAs($this->user)
            ->deleteJson('/to-do-item/' . $this->toDoItem->id)
            ->assertSuccessful();

        $this->assertDatabaseMissing('to_do_items', [
            'id' => 1,
            'owner_id' => $this->user->id,
            'body' => 'This is an example of an item that I need to do.',
            'completed' => 0,
        ]);
    }

    /** @test */
    public function userCannotDeleteOtherUserToDoItem() : void
    {
        $this->actingAs($this->otherUser)
            ->deleteJson('/to-do-item/' . $this->toDoItem->id)
            ->assertStatus(401);

        $this->assertDatabaseHas('to_do_items', [
            'id' => 1,
            'owner_id' => $this->user->id,
            'body' => 'This is an example of an item that I need to do.',
            'completed' => 0,
        ]);
    }

    /** @test */
    public function incorrectQueryParameterReturnsError() {
        $this->actingAs($this->user)
            ->deleteJson('/to-do-item/abcdefg')
            ->assertJson([
                'error' => 'Incorrect parameters supplied.'
            ])
            ->assertStatus(400);
    }
}
