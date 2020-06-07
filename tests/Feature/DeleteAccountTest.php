<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DeleteAccountTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->user = factory(User::class)->create([
            'first_name' => 'Test',
            'last_name' => 'Testerton',
            'email' => 'test@example.com',
        ]);
    }

    /** @test */
    public function userCanDeleteTheirAccount()
    {
        $this->actingAs($this->user)
            ->deleteJson('/settings/profile')
            ->assertSuccessful();

        $this->assertDatabaseMissing('users', [
            'id' => 1,
            'first_name' => 'Test',
            'last_name' => 'Testerton',
            'email' => 'test@example.com',
        ]);
    }
}
