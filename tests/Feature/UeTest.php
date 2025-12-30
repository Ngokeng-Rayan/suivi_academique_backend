<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Ue;
use App\Models\Niveau;

class UeTest extends TestCase
{
    protected $token = "9|LmZvcBtlXwKBKdKyEoC4Xom3dOXCQGFj62byH6jReef77d6a";

    public function test_get_ues()
    {
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->token])
            ->getJson('/api/ues');
        $response->assertStatus(200);
    }

    public function test_create_ue()
    {
        $niveauId = Niveau::inRandomOrder()->first()->code_niveau;
        $code = 'UE-TEST-' . rand(1000, 9999);

        $data = [
            'code_ue' => $code,
            'label_ue' => 'UE de Test',
            'desc_ue' => 'Description',
            'code_niveau' => $niveauId
        ];

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->token])
            ->postJson('/api/ues', $data);

        $response->assertStatus(201);

        Ue::destroy($code);
    }

    public function test_update_ue()
    {
        $niveauId = Niveau::inRandomOrder()->first()->code_niveau;
        $code = 'UE-UPD-' . rand(1000, 9999);

        $ue = Ue::create([
            'code_ue' => $code,
            'label_ue' => 'Old Label',
            'desc_ue' => 'Old Desc',
            'code_niveau' => $niveauId
        ]);

        $updateData = ['label_ue' => 'New Label'];

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->token])
            ->putJson("/api/ues/{$code}", $updateData);

        $response->assertStatus(200);

        $ue->delete();
    }

    public function test_delete_ue()
    {
        $niveauId = Niveau::inRandomOrder()->first()->code_niveau;
        $code = 'UE-DEL-' . rand(1000, 9999);

        Ue::create([
            'code_ue' => $code,
            'label_ue' => 'To Delete',
            'desc_ue' => 'To Delete',
            'code_niveau' => $niveauId
        ]);

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->token])
            ->deleteJson("/api/ues/{$code}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('ue', ['code_ue' => $code]);
    }
}
