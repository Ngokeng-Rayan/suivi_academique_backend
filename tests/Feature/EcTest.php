<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Ec;
use App\Models\Ue;

class EcTest extends TestCase
{
    protected $token = "9|LmZvcBtlXwKBKdKyEoC4Xom3dOXCQGFj62byH6jReef77d6a";

    public function test_get_ecs()
    {
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->token])
            ->getJson('/api/ecs');
        $response->assertStatus(200);
    }

    public function test_create_ec()
    {
        $ueCode = Ue::inRandomOrder()->first()->code_ue;

        $data = [
            'label_ec' => 'EC Test',
            'desc_ec' => 'Description EC',
            'code_ue' => $ueCode
        ];

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->token])
            ->postJson('/api/ecs', $data);

        $response->assertStatus(201);

        // Nettoyage via ID récupéré
        $id = $response->json('code_ec');
        if ($id)
            Ec::destroy($id);
    }

    public function test_update_ec()
    {
        $ueCode = Ue::inRandomOrder()->first()->code_ue;
        $ec = Ec::create([
            'label_ec' => 'Old EC',
            'desc_ec' => 'Old Desc',
            'code_ue' => $ueCode
        ]);

        $updateData = ['label_ec' => 'New EC Label'];

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->token])
            ->putJson("/api/ecs/{$ec->code_ec}", $updateData);

        $response->assertStatus(200);

        $ec->delete();
    }

    public function test_delete_ec()
    {
        $ueCode = Ue::inRandomOrder()->first()->code_ue;
        $ec = Ec::create([
            'label_ec' => 'To Delete',
            'desc_ec' => 'To Delete',
            'code_ue' => $ueCode
        ]);

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->token])
            ->deleteJson("/api/ecs/{$ec->code_ec}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('ec', ['code_ec' => $ec->code_ec]);
    }
}
