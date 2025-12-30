<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ec>
 */
class EcFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "label_ec" => $this->faker->words(3, true),
            "desc_ec" => $this->faker->paragraph(),
            "code_ue" => \App\Models\Ue::inRandomOrder()->first()->code_ue,
        ];
    }
}
