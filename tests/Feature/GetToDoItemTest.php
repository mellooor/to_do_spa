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
            ->assertSuccessful();

        $this->assertEquals(30, $response->getData()->meta->total); // Check to see that the correct number of to do items have been retrieved.
        $this->assertNotNull($response->getData()->links->next); // Check to see if pagination links are present

        // Incomplete To Do Items
        $response = $this->actingAs($this->user)
            ->getJson('/to-do-items/incomplete')
            ->assertSuccessful();

        $this->assertEquals(15, $response->getData()->meta->total); // Check to see that the correct number of to do items have been retrieved.
        $this->assertNotNull($response->getData()->links->next); // Check to see if pagination links are present

        // Completed To Do Items
        $response = $this->actingAs($this->user)
            ->getJson('/to-do-items/completed')
            ->assertSuccessful();

        $this->assertEquals(15, $response->getData()->meta->total); // Check to see that the correct number of to do items have been retrieved.
        $this->assertNotNull($response->getData()->links->next); // Check to see if pagination links are present
    }

    /** @test */
    public function unregisteredUserCannotRetrieveToDoItems() {
        $this->getJson('/to-do-items')
            ->assertJson([
                'message' => 'Unauthenticated.'
            ])
            ->assertStatus(401);
    }

    /** @test */
    public function incorrectQueryParameterReturnsError() {
        $this->actingAs($this->user)
            ->getJson('/to-do-items/abcdefg')
            ->assertJson([
                'error' => 'Incorrect parameters supplied.'
            ])
            ->assertStatus(400);
    }
}
