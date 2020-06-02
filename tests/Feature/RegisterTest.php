<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        config(['app.url' => 'http://api.to-do-spa.com']);
    }

    /** @test */
    public function can_register()
    {
        $this->postJson('/register', [
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test@test.app',
            'password' => 'secret',
            'password_confirmation' => 'secret',
        ])
        ->assertSuccessful()
        ->assertJsonStructure(['id', 'first_name', 'last_name', 'email']);
    }

    /** @test */
    public function can_not_register_with_existing_email()
    {
        factory(User::class)->create(['email' => 'test@test.app']);

        $this->postJson('/register', [
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test@test.app',
            'password' => 'secret',
            'password_confirmation' => 'secret',
        ])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
    }
}
