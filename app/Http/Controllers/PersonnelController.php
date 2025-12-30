<?php

namespace App\Http\Controllers;

use App\Models\Personnel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Services\AuditLogger;
use App\Mail\LoginCredentialsMail;

class PersonnelController extends Controller
{
    /**
     * Liste tous les personnels avec pagination
     *
     * @OA\Get(
     *     path="/api/personnels",
     *     summary="Récupérer tous les personnels",
     *     tags={"Personnels"},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         required=false,
     *         description="Numéro de la page (par défaut 1)",
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         required=false,
     *         description="Nombre d'éléments par page (par défaut 10)",
     *         @OA\Schema(type="integer", default=10)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Liste paginée des personnels"
     *     )
     * )
     */
    public function index()
    {
        $perPage = request('per_page', 10);
        $personnels = Personnel::with(['programmations', 'enseignes'])->paginate($perPage);
        return response()->json($personnels, 200);
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'code_pers' => 'required|string|unique:personnel,code_pers',
                'nom_pers' => 'required|string',
                'prenom_pers' => 'sometimes|string',
                'sexe_pers' => 'required|in:M,F',
                'phone_pers' => 'required|string',
                'login_pers' => 'required|string|unique:personnel,login_pers',
                'pwd_pers' => 'required|string|min:6',
                'type_pers' => 'required|in:ENSEIGNANT,RESPONSABLE ACADEMIQUE,RESPONSABLE DISCIPLINE',
            ]);

            // Garder le mot de passe en plaintext pour l'email
            $plaintextPassword = $validatedData['pwd_pers'];

            $validatedData['pwd_pers'] = Hash::make($validatedData['pwd_pers']);

            $personnel = Personnel::create($validatedData);

            // Envoyer un email avec les identifiants d'inscription
            try {
                Mail::to($personnel->login_pers)->send(
                    new LoginCredentialsMail($personnel, $plaintextPassword)
                );
            } catch (\Exception $e) {
                // Log l'erreur d'envoi de mail mais ne pas bloquer l'inscription
                AuditLogger::logError('SEND_EMAIL', 'Erreur lors de l\'envoi du mail d\'inscription: ' . $e->getMessage(), [
                    'personnel_id' => $personnel->code_pers,
                    'email' => $personnel->login_pers
                ]);
            }

            // Log la création
            AuditLogger::logCreate('Personnel', [
                'code_pers' => $personnel->code_pers,
                'nom_pers' => $personnel->nom_pers,
                'type_pers' => $personnel->type_pers,
            ], $personnel->code_pers);

            return response()->json([
                'message' => 'Personnel créé avec succès',
                'data' => $personnel
            ], 201);

        } catch (\Throwable $th) {
            AuditLogger::logError('CREATE_PERSONNEL', $th->getMessage());
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function show(string $code_pers)
    {
        try {
            $personnel = Personnel::with(['programmations', 'enseignes'])->findOrFail($code_pers);
            return response()->json($personnel, 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Personnel non trouvé'
            ], 404);
        }
    }

    public function update(Request $request, string $code_pers)
    {
        try {
            $personnel = Personnel::findOrFail($code_pers);
            $oldData = $personnel->toArray();

            $validatedData = $request->validate([
                'nom_pers' => 'sometimes|string',
                'prenom_pers' => 'sometimes|string',
                'sexe_pers' => 'sometimes|in:M,F',
                'phone_pers' => 'sometimes|string',
                'login_pers' => 'sometimes|string|unique:personnel,login_pers,' . $code_pers . ',code_pers',
                'pwd_pers' => 'sometimes|string|min:6',
                'type_pers' => 'sometimes|in:ENSEIGNANT,RESPONSABLE ACADEMIQUE,RESPONSABLE DISCIPLINE',
            ]);

            // Hash le mot de passe si fourni
            if (isset($validatedData['pwd_pers'])) {
                $validatedData['pwd_pers'] = Hash::make($validatedData['pwd_pers']);
            }

            $personnel->update($validatedData);

            // Log la modification
            AuditLogger::logUpdate('Personnel', $code_pers, $oldData, $personnel->toArray());

            return response()->json([
                'message' => 'Personnel modifié avec succès',
                'data' => $personnel
            ], 200);

        } catch (\Throwable $th) {
            AuditLogger::logError('UPDATE_PERSONNEL', $th->getMessage(), ['code_pers' => $code_pers]);
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function destroy(string $code_pers)
    {
        try {
            $personnel = Personnel::findOrFail($code_pers);
            $deletedData = $personnel->toArray();
            $personnel->delete();

            // Log la suppression
            AuditLogger::logDelete('Personnel', $code_pers, $deletedData);

            return response()->json([
                'message' => 'Suppression réussie'
            ], 200);

        } catch (\Throwable $th) {
            AuditLogger::logError('DELETE_PERSONNEL', $th->getMessage(), ['code_pers' => $code_pers]);
            return response()->json([
                'message' => 'Personnel non trouvé'
            ], 404);
        }
    }
}
