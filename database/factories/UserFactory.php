<?php

namespace Database\Factories;

use App\Entities\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'username' => fake()->unique()->userName(),
            'email' => fake()->unique()->safeEmail(),
            'nick' => fake()->name(),
            'status' => User::ST_ACTIVE,
            'access_at' => now(),
            'email_level' => 0,
            'remember_token' => Str::random(10),
        ];
    }

    public function configure(): static
    {
        return $this->afterMaking(function (User $user) {
            if (! $user->password) {
                $user->password = Hash::make('password');
            }
        });
    }
}
