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
        $oldUpdatedAtTimestamp = ToDoItem::find(1)->updated_at->timestamp;
        $oneMinuteLater = Carbon::now()->add(1, 'minute');
        Carbon::setTestNow($oneMinuteLater);

        $this->actingAs($this->user)
            ->patchJson('/to-do-item/' . $this->toDoItem->id, [
                'body' => 'This was an example of an item that I need to do.',
                'completed' => 'true'
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

        // Assert that the edited_at timestamp has been updated.
        $this->assertNotEquals($oldUpdatedAtTimestamp, ToDoItem::find(1)->updated_at->timestamp);
    }

    /** @test */
    public function userCanPatchBodyFieldOnly() : void
    {
        $oldUpdatedAtTimestamp = ToDoItem::find(1)->updated_at->timestamp;
        $oneMinuteLater = Carbon::now()->add(1, 'minute');
        Carbon::setTestNow($oneMinuteLater);

        $this->actingAs($this->user)
            ->patchJson('/to-do-item/' . $this->toDoItem->id, [
                'body' => 'This will be an example of an item that I need to do.',
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
            'body' => 'This will be an example of an item that I need to do.',
            'completed' => 0,
        ]);

        // Assert that the edited_at timestamp has been updated.
        $this->assertNotEquals($oldUpdatedAtTimestamp, ToDoItem::find(1)->updated_at->timestamp);
    }

    /** @test */
    public function userCanPatchCompletedFieldOnly() : void
    {
        $oldTimestamp = ToDoItem::find(1)->updated_at->timestamp;
        $oneMinuteLater = Carbon::now()->add(1, 'minute');
        Carbon::setTestNow($oneMinuteLater);

        $this->actingAs($this->user)
            ->patchJson('/to-do-item/' . $this->toDoItem->id, [
                'completed' => 'true'
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
            'body' => 'This is an example of an item that I need to do.',
            'completed' => 1,
        ]);

        // Assert that the edited_at timestamp has been updated.
        $this->assertNotEquals($oldTimestamp, ToDoItem::find(1)->updated_at->timestamp);
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
        $oldTimestamp = ToDoItem::find(1)->updated_at->timestamp;
        $oneMinuteLater = Carbon::now()->add(1, 'minute');
        Carbon::setTestNow($oneMinuteLater);

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

        // Assert that the edited_at timestamp has been updated.
        $this->assertEquals($oldTimestamp, ToDoItem::find(1)->updated_at->timestamp);
    }
}
