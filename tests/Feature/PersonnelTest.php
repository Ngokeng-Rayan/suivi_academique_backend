<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Personnel;
use Illuminate\Support\Facades\Hash; // Ajout pour Hash

class PersonnelTest extends TestCase
{
    protected $token = "9|LmZvcBtlXwKBKdKyEoC4Xom3dOXCQGFj62byH6jReef77d6a";

    public function test_get_personnels()
    {
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->token])
            ->getJson('/api/personnels');
        $response->assertStatus(200);
    }

    public function test_create_personnel()
    {
        $code = 'PERS-TEST-' . rand(1000, 9999);
        $login = 'user' . rand(1000, 9999);

        $data = [
            'code_pers' => $code,
            'nom_pers' => 'NomTest',
            'prenom_pers' => 'PrenomTest',
            'sexe_pers' => 'M',
            'phone_pers' => '00000000',
            'login_pers' => $login,
            'pwd_pers' => 'password123',
            'type_pers' => 'ENSEIGNANT' // Enum strict
        ];

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->token])
            ->postJson('/api/personnels', $data);

        $response->assertStatus(201);

        Personnel::destroy($code);
    }

    public function test_update_personnel()
    {
        $code = 'PERS-UPD-' . rand(1000, 9999);
        $personnel = Personnel::create([
            'code_pers' => $code,
            'nom_pers' => 'Old Nom',
            'sexe_pers' => 'F',
            'phone_pers' => '111',
            'login_pers' => 'login' . rand(),
            'pwd_pers' => Hash::make('pass'),
            'type_pers' => 'RESPONSABLE ACADEMIQUE'
        ]);

        $updateData = ['nom_pers' => 'New Nom'];

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->token])
            ->putJson("/api/personnels/{$code}", $updateData);

        $response->assertStatus(200);

        $personnel->delete();
    }

    public function test_delete_personnel()
    {
        $code = 'PERS-DEL-' . rand(1000, 9999);
        Personnel::create([
            'code_pers' => $code,
            'nom_pers' => 'To Delete',
            'sexe_pers' => 'M',
            'phone_pers' => '222',
            'login_pers' => 'del' . rand(),
            'pwd_pers' => Hash::make('pass'),
            'type_pers' => 'ENSEIGNANT'
        ]);

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->token])
            ->deleteJson("/api/personnels/{$code}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('personnel', ['code_pers' => $code]);
    }
}
