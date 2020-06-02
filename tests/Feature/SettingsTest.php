<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;

class SettingsTest extends TestCase
{
    /** @var \App\User */
    protected $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
        config(['app.url' => 'http://api.to-do-spa.com']);
    }

    /** @test */
    public function update_profile_info()
    {
        $this->actingAs($this->user)
            ->patchJson('/settings/profile', [
                'first_name' => 'Test',
                'last_name' => 'User',
                'email' => 'test@test.app',
            ])
            ->assertSuccessful()
            ->assertJsonStructure(['id', 'first_name', 'last_name', 'email']);

        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test@test.app',
        ]);
    }

    /** @test */
    public function update_password()
    {
        $this->actingAs($this->user)
            ->patchJson('/settings/password', [
                'password' => 'updated',
                'password_confirmation' => 'updated',
            ])
            ->assertSuccessful();

        $this->assertTrue(Hash::check('updated', $this->user->password));
    }
}
