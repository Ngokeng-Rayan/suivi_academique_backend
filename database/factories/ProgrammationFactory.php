<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Programmation>
 */
class ProgrammationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "code_ec" => \App\Models\Ec::inRandomOrder()->first()->code_ec,
            "num_salle" => \App\Models\Salle::inRandomOrder()->first()->num_sale,
            "code_pers" => \App\Models\Personnel::inRandomOrder()->first()->code_pers,
            "date" => $this->faker->date(),
            "heure_debut" => $this->faker->time('H:i'),
            "heure_fin" => $this->faker->time('H:i'),
            "nbre_heure" => $this->faker->numberBetween(1, 4),
            "status" => $this->faker->randomElement(['planifié', 'en cours', 'terminé', 'annulé']),
        ];
    }
}
