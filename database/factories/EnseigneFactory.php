<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Enseigne>
 */
class EnseigneFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "code_pers" => \App\Models\Personnel::inRandomOrder()->first()->code_pers,
            "code_ec" => \App\Models\Ec::inRandomOrder()->first()->code_ec,
            "nbh_heure" => $this->faker->numberBetween(10, 100),
            "heure_debut" => $this->faker->date(),
            "heure_fin" => $this->faker->date(),
            "statut" => $this->faker->randomElement(['actif', 'inactif', 'terminé']),
        ];
    }
}
