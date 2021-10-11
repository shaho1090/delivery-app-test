<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * @var array
     */
    private $registerData;

    public function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub

        $this->registerData = [
            'name' => $this->faker->name,
            'email' => 'simple.email@gmail.com',
            'password' => '12345678',
            'password_confirmation' => '12345678'
        ];
    }

    public function test_a_user_can_register()
    {
        $this->postJson(route('register-user'), $this->registerData)
            ->assertJsonFragment([
                'name' => $this->registerData['name'],
                'email' => $this->registerData['email']
            ]);

        $this->assertAuthenticated();
    }

    public function test_user_can_login_and_get_token()
    {
        User::factory()->create([
            'password' => Hash::make($this->registerData['password']),
            'email' => $this->registerData['email']
        ]);

        $this->postJson(route('login'), [
            'email' => $this->registerData['email'],
            'password' => $this->registerData['password']
        ]);

        $this->assertAuthenticated();
    }
}
