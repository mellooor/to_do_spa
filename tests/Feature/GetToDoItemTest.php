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
        factory(ToDoItem::class, 15)->create(['user_id' => $this->user->id]); // Incomplete to do items
        factory(ToDoItem::class, 15)->create(['user_id' => $this->user->id, 'completed' => 1]); // Completed to do items
    }

    public function userCanGetTheirToDoItems() : void
    {
        // All To Do Items
        $this->actingAs($this->user)
            ->getJson('/to-do-items')
            ->assertSuccessful()
            ->assertJsonCount(30)
            ->assertJsonFragment(['next_page_url' => $this->apiURL . '?page=2']); // Check to see if pagination links are present

        // Incomplete To Do Items
        $this->actingAs($this->user)
            ->getJson('/to-do-items/incomplete')
            ->assertSuccessful()
            ->assertJsonCount(15)
            ->assertJsonFragment(['next_page_url' => $this->apiURL . '?page=2']);

        // Completed To Do Items
        $this->actingAs($this->user)
            ->getJson('/to-do-items/completed')
            ->assertSuccessful()
            ->assertJsonCount(15)
            ->assertJsonFragment(['next_page_url' => $this->apiURL . '?page=2']);
    }
}
