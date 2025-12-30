<?php

namespace Tests\Feature;

use Tests\TestCase;

class FiliereTest extends TestCase
{
    // Attention : RefreshDatabase est désactivé pour utiliser la vraie base de données
    // use Illuminate\Foundation\Testing\RefreshDatabase;

    // Token à utiliser pour tous les tests
    protected $token = "9|LmZvcBtlXwKBKdKyEoC4Xom3dOXCQGFj62byH6jReef77d6a";

    public function test_get_filiere_with_my_token()
    {
        // Requête GET sur l'API avec le token
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/filieres');

        $response->assertStatus(200);
    }

    public function test_create_filiere_with_my_token()
    {
        // Données d'une nouvelle filière
        // J'utilise un code aléatoire pour éviter les erreurs "Duplicate entry" si vous relancez le test
        $code = 'TEST-' . rand(1000, 9999);

        $data = [
            'code_filiere' => $code,
            'label_filiere' => 'Filière Test Automatique',
            'desc_filiere' => 'Créée par PHPUnit'
        ];

        // Requête POST
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/filieres', $data);

        // Vérification de la création (201 Created)
        $response->assertStatus(201);

        // Nettoyage : On supprime la filière créée
        \App\Models\Filiere::destroy($code);
    }

    public function test_update_filiere_with_my_token()
    {
        // 1. Création d'une filière temporaire
        $code = 'TEST-UPD-' . rand(1000, 9999);
        $filiere = \App\Models\Filiere::create([
            'code_filiere' => $code,
            'label_filiere' => 'Original Label',
            'desc_filiere' => 'Original Desc'
        ]);

        // 2. Données de mise à jour
        $updateData = [
            'label_filiere' => 'Label Modifié',
            'desc_filiere' => 'Desc Modifiée'
        ];

        // 3. Requête PUT
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->putJson("/api/filieres/{$code}", $updateData);

        // 4. Vérification (200 OK)
        $response->assertStatus(200);

        // 5. Nettoyage
        $filiere->delete();
    }

    public function test_delete_filiere_with_my_token()
    {
        // 1. Création d'une filière temporaire
        $code = 'TEST-DEL-' . rand(1000, 9999);
        \App\Models\Filiere::create([
            'code_filiere' => $code,
            'label_filiere' => 'To Delete',
            'desc_filiere' => 'To Delete'
        ]);

        // 2. Requête DELETE
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->deleteJson("/api/filieres/{$code}");

        // 3. Vérification (200 OK ou 204 selon votre contrôleur)
        // Votre contrôleur renvoie un JSON avec message, donc c'est probablement 200
        $response->assertStatus(200);

        // 4. Vérification qu'elle n'existe plus (Optionnel mais recommandé)
        $this->assertDatabaseMissing('filiere', ['code_filiere' => $code]);
    }
}
