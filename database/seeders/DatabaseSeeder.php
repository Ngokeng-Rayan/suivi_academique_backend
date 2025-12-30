<?php

namespace Database\Seeders;

use App\Models\Filiere;
use App\Models\Niveau;
use App\Models\Ue;
use App\Models\Ec;
use App\Models\Personnel;
use App\Models\Salle;
use App\Models\Enseigne;
use App\Models\Programmation;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Créer des filières de base
        Filiere::insert([
            [
                'code_filiere' => 'INF',
                'label_filiere' => 'Informatique',
                'desc_filiere' => 'Filière spécialisée en Informatique et Développement',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code_filiere' => 'GES',
                'label_filiere' => 'Gestion',
                'desc_filiere' => 'Filière de Gestion d\'entreprise',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code_filiere' => 'COM',
                'label_filiere' => 'Commerce',
                'desc_filiere' => 'Filière de Commerce et Marketing',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Créer des niveaux
        Niveau::insert([
            [
                'code_niveau' => 'L1',
                'label_niveau' => 'Licence 1',
                'code_filiere' => 'INF',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code_niveau' => 'L2',
                'label_niveau' => 'Licence 2',
                'code_filiere' => 'INF',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code_niveau' => 'L3',
                'label_niveau' => 'Licence 3',
                'code_filiere' => 'INF',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code_niveau' => 'M1',
                'label_niveau' => 'Master 1',
                'code_filiere' => 'GES',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Optionnel : créer d'autres données
        // Ue::factory(10)->create();
        // Ec::factory(20)->create();
        // Personnel::factory(10)->create();
        // Salle::factory(10)->create();
        // Enseigne::factory(30)->create();
        // Programmation::factory(50)->create();
    }
}

