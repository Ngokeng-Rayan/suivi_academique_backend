<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Programmation;
use App\Models\Personnel;
use App\Models\Ec;
use App\Models\Salle;

class ProgrammationTest extends TestCase
{
    protected $token = "9|LmZvcBtlXwKBKdKyEoC4Xom3dOXCQGFj62byH6jReef77d6a";

    public function test_get_programmations()
    {
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->token])
            ->getJson('/api/programmations');
        $response->assertStatus(200);
    }

    public function test_create_programmation()
    {
        $persId = Personnel::inRandomOrder()->first()->code_pers;
        $ecId = Ec::inRandomOrder()->first()->code_ec;
        $salleId = Salle::inRandomOrder()->first()->num_sale;

        // Éviter les doublons de clés primaires
        Programmation::where([
            'code_pers' => $persId,
            'code_ec' => $ecId,
            'num_salle' => $salleId
        ])->delete();

        $data = [
            'code_pers' => $persId,
            'code_ec' => $ecId,
            'num_salle' => $salleId,
            'date' => '2025-12-01',
            'heure_debut' => '08:00',
            'heure_fin' => '10:00',
            'nbre_heure' => 2,
            'status' => 'planifié'
        ];

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->token])
            ->postJson('/api/programmations', $data);

        $response->assertStatus(201); // Ou 200

        // Nettoyage
        Programmation::where([
            'code_pers' => $persId,
            'code_ec' => $ecId,
            'num_salle' => $salleId
        ])->delete();
    }

    public function test_update_programmation()
    {
        $persId = Personnel::inRandomOrder()->first()->code_pers;
        $ecId = Ec::inRandomOrder()->first()->code_ec;
        $salleId = Salle::inRandomOrder()->first()->num_sale;

        // Créer donnée initiale
        Programmation::firstOrCreate(
            ['code_pers' => $persId, 'code_ec' => $ecId, 'num_salle' => $salleId],
            ['date' => '2025-10-10', 'heure_debut' => '10:00', 'heure_fin' => '12:00', 'nbre_heure' => 2, 'status' => 'init']
        );

        $updateData = [
            'code_pers' => $persId,
            'code_ec' => $ecId,
            'num_salle' => $salleId,
            'status' => 'terminé',
            'nbre_heure' => 4
        ];

        // PUT vers /api/programmations/update
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->token])
            ->putJson("/api/programmations/update", $updateData);

        if ($response->status() !== 200) {
            dump($response->json());
        }
        $response->assertStatus(200);

        // Nettoyage
        Programmation::where(['code_pers' => $persId, 'code_ec' => $ecId, 'num_salle' => $salleId])->delete();
    }

    public function test_delete_programmation()
    {
        $persId = Personnel::inRandomOrder()->first()->code_pers;
        $ecId = Ec::inRandomOrder()->first()->code_ec;
        $salleId = Salle::inRandomOrder()->first()->num_sale;

        Programmation::firstOrCreate(
            ['code_pers' => $persId, 'code_ec' => $ecId, 'num_salle' => $salleId],
            ['date' => '2025-10-10', 'heure_debut' => '14:00', 'heure_fin' => '16:00', 'nbre_heure' => 2, 'status' => 'to_del']
        );

        // Paramètres Delete via Query String
        $url = "/api/programmations/delete?code_pers={$persId}&code_ec={$ecId}&num_salle={$salleId}";

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->token])
            ->deleteJson($url);

        if ($response->status() !== 200) {
            dump($response->json());
        }
        $response->assertStatus(200);

        $this->assertDatabaseMissing('programmation', [
            'code_pers' => $persId,
            'code_ec' => $ecId,
            'num_salle' => $salleId
        ]);
    }
}
