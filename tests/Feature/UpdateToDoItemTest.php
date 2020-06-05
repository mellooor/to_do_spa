<?php

namespace Tests\Feature;

use App\ToDoItem;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class UpdateToDoItemTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
        $this->otherUser = factory(User::class)->create();
        $this->toDoItem = factory(ToDoItem::class)->create(['owner_id' => $this->user->id]);
    }

    /** @test */
    public function userCanPatchTheirToDoItem() : void
    {
        $this->actingAs($this->user)
            ->patchJson('/to-do-item/' . $this->toDoItem->id, [
                'body' => 'This was an example of an item that I need to do.',
                'completed' => 1
            ])
            ->assertSuccessful()
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'owner',
                    'body',
                    'created_at',
                    'updated_at',
                    'completed'
                ]
            ]);

        $this->assertDatabaseHas('to_do_items', [
            'id' => 1,
            'owner_id' => $this->user->id,
            'body' => 'This was an example of an item that I need to do.',
            'completed' => 1,
        ]);

        // Assert that the edited_at timestamp falls within the last minute.
        $currentTime = new Carbon();
        $timeDiffInMinutes = $currentTime->diffInMinutes(ToDoItem::find(1)->edited_at->format("Y-m-d H:i:s"));
        $this->assertLessThanOrEqual(1, $timeDiffInMinutes);
    }

    /** @test */
    public function userCannotPatchOtherUserToDoItem() : void
    {
        $this->actingAs($this->otherUser)
            ->patchJson('/to-do-item/' . $this->toDoItem->id, [
                'body' => 'This was an example of an item that I need to do.',
                'completed' => 1
            ])
            ->assertStatus(401);
    }

    /** @test */
    public function userCannotPatchToBlankToDoItem() {
        $this->actingAs($this->user)
            ->patchJson('/to-do-item/' . $this->toDoItem->id, [
                'body' => '',
            ])
            ->assertStatus(422);

        $this->assertDatabaseHas('to_do_items', [
            'id' => 1,
            'owner_id' => $this->user->id,
            'body' => 'This is an example of an item that I need to do.',
            'completed' => 0,
        ]);

        // Assert that the edited_at timestamp doesn't fall within the last minute.
        $currentTime = new Carbon();
        $timeDiffInMinutes = $currentTime->diffInMinutes(ToDoItem::find(1)->edited_at->format("Y-m-d H:i:s"));
        $this->assertGreaterThan(1, $timeDiffInMinutes);
    }

    /** @test */
    public function incorrectQueryParameterReturnsError() {
        $this->actingAs($this->user)
            ->patchJson('/to-do-item/abcdefg')
            ->assertJson([
                'error' => 'Incorrect parameters supplied.'
            ])
            ->assertStatus(400);
    }
}
