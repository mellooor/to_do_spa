<?php

namespace Tests\Feature;

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
                'id',
                'owner_id',
                'body',
                'created_at',
                'updated_at',
                'completed'
            ]);

        $this->assertDatabaseHas('users', [
            'id' => 1,
            'owner_id' => $this->user->id,
            'body' => 'This is an example of an item that I need to do.',
            'email' => 'test@test.app',
        ]);

        // Assert that the created_at timestamp falls within the last minute.
        $currentTime = new Carbon();
        $timeDiffInMinutes = $currentTime->diffInMinutes(Carbon::createFromTimestamp(ToDoItem::find(1)->created_at));
        $this->assertLessThanOrEqual(1, $timeDiffInMinutes);
    }

    /** @test */
    public function unregisteredUserCannotCreateToDoItem() {
        $this->postJson('/to-do-item', [
                'body' => 'This is an example of an item that I need to do.',
            ])
            ->assertStatus(401);
    }
}
