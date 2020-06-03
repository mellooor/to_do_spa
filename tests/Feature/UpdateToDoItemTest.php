<?php

namespace Tests\Feature;

use App\ToDoItem;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UpdateToDoItemTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
        $this->otherUser = factory(User::class)->create();
        $this->toDoItem = factory(ToDoItem::class)->create(['user_id' => $this->user->id]);
    }

    public function userCanPatchTheirToDoItem() : void
    {
        $this->actingAs($this->user)
            ->patchJson('/to-do-item/' . $this->toDoItem->id, [
                'body' => 'This was an example of an item that I need to do.',
                'completed' => 1
            ])
            ->assertSuccessful()
            ->assertJsonStructure([
                'id',
                'owner_id',
                'body',
                'created_at',
                'updated_at',
                'completed'
            ]);

        $this->assertDatabaseHas('to_do_items', [
            'id' => 1,
            'owner_id' => $this->user->id,
            'body' => 'This was an example of an item that I need to do.',
            'completed' => 1,
        ]);

        // Assert that the edited_at timestamp falls within the last minute.
        $currentTime = new Carbon();
        $timeDiffInMinutes = $currentTime->diffInMinutes(Carbon::createFromTimestamp(ToDoItem::find(1)->edited_at));
        $this->assertLessThanOrEqual(1, $timeDiffInMinutes);
    }

    public function userCannotPatchOtherUserToDoItem() : void
    {
        $this->actingAs($this->otherUser)
            ->patchJson('/to-do-item/' . $this->toDoItem->id, [
                'body' => 'This was an example of an item that I need to do.',
                'completed' => 1
            ])
            ->assertStatus(401);
    }
}
