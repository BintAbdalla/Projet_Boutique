<?php

namespace Database\Factories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Client>
 */
class ClientFactory extends Factory
{
    /**
     * Le nom du modèle correspondant à cette factory.
     *
     * @var string
     */
    protected $model = Client::class;

    /**
     * Définir l'état par défaut du modèle.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'surname' => $this->faker->lastName(), // Génère un email unique
            'telephone' => $this->faker->phoneNumber(), // Génère un numéro de téléphone aléatoire
            'adresse' => $this->faker->address(), // Génère une adresse aléatoire
        ];
    }
}
