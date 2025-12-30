<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Niveau;
use App\Models\Filiere;

class NiveauTest extends TestCase
{
    // Token d'accès
    protected $token = "9|LmZvcBtlXwKBKdKyEoC4Xom3dOXCQGFj62byH6jReef77d6a";

    public function test_get_niveaux()
    {
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->token])
            ->getJson('/api/niveaux');
        $response->assertStatus(200);
    }

    public function test_create_niveau()
    {
        // Création d'un test ID unique
        $filiereCode = Filiere::inRandomOrder()->first()->code_filiere;

        $data = [
            'label_niveau' => 'Niveau Test ' . rand(100, 999),
            'desc_niveau' => 'Description test',
            'code_filiere' => $filiereCode
        ];

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->token])
            ->postJson('/api/niveaux', $data);

        $response->assertStatus(201);

        // Récupérer l'ID créé pour le nettoyage (si besoin, mais la base n'est pas refresh)
        $id = $response->json('code_niveau'); // Supposons que l'API renvoie l'objet créé
        if ($id) {
            Niveau::destroy($id);
        }
    }

    // Pour l'update et delete, on doit créer une ressource temporaire d'abord pour ne pas casser les vraies données
    public function test_update_niveau()
    {
        $filiereCode = Filiere::inRandomOrder()->first()->code_filiere;
        $niveau = Niveau::create([
            'label_niveau' => 'Old Label',
            'desc_niveau' => 'Old Desc',
            'code_filiere' => $filiereCode
        ]);

        $updateData = ['label_niveau' => 'New Label'];

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->token])
            ->putJson("/api/niveaux/{$niveau->code_niveau}", $updateData);

        $response->assertStatus(200);

        $niveau->delete();
    }

    public function test_delete_niveau()
    {
        $filiereCode = Filiere::inRandomOrder()->first()->code_filiere;
        $niveau = Niveau::create([
            'label_niveau' => 'To Delete',
            'desc_niveau' => 'To Delete',
            'code_filiere' => $filiereCode
        ]);

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->token])
            ->deleteJson("/api/niveaux/{$niveau->code_niveau}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('niveau', ['code_niveau' => $niveau->code_niveau]);
    }
}
