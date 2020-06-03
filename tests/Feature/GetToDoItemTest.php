<?php

namespace Tests\Feature;

use App\ToDoItem;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class GetToDoItemTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
        factory(ToDoItem::class, 15)->create(['owner_id' => $this->user->id]); // Incomplete to do items
        factory(ToDoItem::class, 15)->create(['owner_id' => $this->user->id, 'completed' => 1]); // Completed to do items
    }

    /** @test */
    public function userCanGetTheirToDoItems() : void
    {
        // All To Do Items
        $response = $this->actingAs($this->user)
            ->getJson('/to-do-items')
            ->assertSuccessful()
            ->assertJsonCount(30);

        $this->assertNotNull($response->getData()->links->next); // Check to see if pagination links are present

        // Incomplete To Do Items
        $response = $this->actingAs($this->user)
            ->getJson('/to-do-items/incomplete')
            ->assertSuccessful()
            ->assertJsonCount(15);

        $this->assertNotNull($response->getData()->links->next); // Check to see if pagination links are present

        // Completed To Do Items
        $response = $this->actingAs($this->user)
            ->getJson('/to-do-items/completed')
            ->assertSuccessful()
            ->assertJsonCount(15);

        $this->assertNotNull($response->getData()->links->next); // Check to see if pagination links are present
    }
}
