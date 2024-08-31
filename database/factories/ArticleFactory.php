<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Article;

/**
//  * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Article>:
 */
class ArticleFactory extends Factory
{
    /**
     * Le nom du modèle correspondant à cette fabrique.
     *
     * @var string
     */
    protected $model = Article::class;

    /**
     * Définir l'état par défaut du modèle.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'libelle' => $this->faker->word(), 
            'prix' => $this->faker->randomFloat(2, 1, 1000),
            'qteStock' => $this->faker->numberBetween(0, 100), 
        ];
    }
}
