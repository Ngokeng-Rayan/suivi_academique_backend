<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Salle;

class SalleTest extends TestCase
{
    protected $token = "9|LmZvcBtlXwKBKdKyEoC4Xom3dOXCQGFj62byH6jReef77d6a";

    public function test_get_salles()
    {
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->token])
            ->getJson('/api/salles');
        $response->assertStatus(200);
    }

    public function test_create_salle()
    {
        $code = 'SAL-TEST-' . rand(100, 999);

        $data = [
            'num_sale' => $code,
            'contenance' => 50,
            'statut' => 'disponible' // Enum
        ];

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->token])
            ->postJson('/api/salles', $data);

        $response->assertStatus(201);

        Salle::destroy($code);
    }

    public function test_update_salle()
    {
        $code = 'SAL-UPD-' . rand(100, 999);
        $salle = Salle::create([
            'num_sale' => $code,
            'contenance' => 30,
            'statut' => 'maintenance'
        ]);

        $updateData = ['contenance' => 100];

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->token])
            ->putJson("/api/salles/{$code}", $updateData);

        $response->assertStatus(200);

        $salle->delete();
    }

    public function test_delete_salle()
    {
        $code = 'SAL-DEL-' . rand(100, 999);
        Salle::create([
            'num_sale' => $code,
            'contenance' => 10,
            'statut' => 'occupée'
        ]);

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->token])
            ->deleteJson("/api/salles/{$code}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('salle', ['num_sale' => $code]);
    }
}
