<?php

namespace Database\Factories;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        // Assigner un rôle aléatoire existant ou un rôle par défaut si aucun rôle n'existe
        $role = Role::inRandomOrder()->first() ?? Role::create(['role' => 'client']);

        return [
            'nom' => $this->faker->name(),
            'prenom' => $this->faker->firstName(),
            'login' => $this->faker->unique()->safeEmail(),
            'password' => static::$password ??= Hash::make('password'),
            'role_id' => $role->id,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function client(): static
    {
        // Assigner un rôle client si disponible
        $role = Role::where('role', 'client')->first() ?? Role::create(['role' => 'client']);

        return $this->state(fn (array $attributes) => [
            'role_id' => $role->id,
        ]);
    }
}
