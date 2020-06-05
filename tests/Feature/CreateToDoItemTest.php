<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\ToDoItem;
use Illuminate\Support\Carbon;

class CreateToDoItemTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
    }

    /** @test */
    public function registeredUserCanCreateToDoItem()
    {
        $this->actingAs($this->user)
            ->postJson('/to-do-item', [
                'body' => 'This is an example of an item that I need to do.',
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
            'completed' => 0,
        ]);

        // Assert that the created_at timestamp falls within the last minute.
        $currentTime = new Carbon();
        $timeDiffInMinutes = $currentTime->diffInMinutes(ToDoItem::find(1)->created_at->format('Y-m-d H:i:s'));
        $this->assertLessThanOrEqual(1, $timeDiffInMinutes);
    }

    /** @test */
    public function registeredUserCannotCreateBlankToDoItem() {
        $this->actingAs($this->user)
            ->postJson('/to-do-item', [
                'body' => '',
            ])
            ->assertStatus(422);

        $this->assertDatabaseMissing('to_do_items', [
            'id' => 2,
            'owner_id' => $this->user->id,
            'body' => '',
            'email' => 'test@test.app',
        ]);
    }

    /** @test */
    public function unregisteredUserCannotCreateToDoItem() {
        $this->postJson('/to-do-item', [
            'body' => 'This is an example of an item that I need to do.',
        ])
            ->assertJson([
                'message' => 'Unauthenticated.'
            ])
            ->assertStatus(401);
    }
}
