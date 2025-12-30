<?php

namespace App\Http\Controllers;

use App\Models\Enseigne;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class EnseigneController extends Controller
{
    /**
     * Liste tous les enseignements avec pagination
     *
     * @OA\Get(
     *     path="/api/enseignes",
     *     summary="Récupérer tous les enseignements",
     *     tags={"Enseignements"},
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
     *         description="Liste paginée des enseignements"
     *     )
     * )
     */
    public function index()
    {
        $perPage = request('per_page', 10);
        $enseignes = Enseigne::with(['personnel', 'ec'])->paginate($perPage);
        return response()->json($enseignes, 200);
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'code_pers' => 'required|string|exists:personnel,code_pers',
                'code_ec' => 'required|integer|exists:ec,code_ec',
                'nbh_heure' => 'sometimes|integer|min:1',
                'heure_debut' => 'sometimes|date',
                'heure_fin' => 'sometimes|date|after:heure_debut',
                'statut' => 'sometimes|in:actif,inactif,terminé',
            ]);

            // Vérifier si l'enseignement existe déjà
            $existe = Enseigne::where('code_pers', $validatedData['code_pers'])
                ->where('code_ec', $validatedData['code_ec'])
                ->exists();

            if ($existe) {
                return response()->json([
                    'message' => 'Cet enseignement existe déjà pour ce personnel et cet EC'
                ], 409);
            }

            $enseigne = Enseigne::create($validatedData);

            return response()->json([
                'message' => 'Enseignement créé avec succès',
                'data' => $enseigne
            ], 201);

        } catch (\Throwable $th) {
            if ($th instanceof ValidationException) {
                throw $th;
            }
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function show(Request $request)
    {
        try {
            // Clé composite : code_pers et code_ec
            $enseigne = Enseigne::where('code_pers', $request->code_pers)
                ->where('code_ec', $request->code_ec)
                ->with(['personnel', 'ec'])
                ->firstOrFail();

            return response()->json($enseigne, 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Enseignement non trouvé'
            ], 404);
        }
    }

    public function update(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'nbh_heure' => 'sometimes|integer|min:1',
                'heure_debut' => 'sometimes|date',
                'heure_fin' => 'sometimes|date|after:heure_debut',
                'statut' => 'sometimes|in:actif,inactif,terminé',
            ]);

            Enseigne::where('code_pers', $request->code_pers)
                ->where('code_ec', $request->code_ec)
                ->update($validatedData);

            $enseigne = Enseigne::where('code_pers', $request->code_pers)
                ->where('code_ec', $request->code_ec)
                ->firstOrFail();

            return response()->json([
                'message' => 'Enseignement modifié avec succès',
                'data' => $enseigne
            ], 200);

        } catch (\Throwable $th) {
            if ($th instanceof ValidationException) {
                throw $th;
            }
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function destroy(Request $request)
    {
        try {
            $count = Enseigne::where('code_pers', $request->code_pers)
                ->where('code_ec', $request->code_ec)
                ->delete();

            if ($count === 0) {
                return response()->json([
                    'message' => 'Enseignement non trouvé'
                ], 404);
            }

            return response()->json([
                'message' => 'Suppression réussie'
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Enseignement non trouvé'
            ], 404);
        }
    }

    public function getByPersonnel($code_pers)
    {
        try {
            $enseignes = Enseigne::where('code_pers', $code_pers)
                ->with(['personnel', 'ec'])
                ->get();

            return response()->json($enseignes, 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function getByEc($code_ec)
    {
        try {
            $enseignes = Enseigne::where('code_ec', $code_ec)
                ->with(['personnel', 'ec'])
                ->get();

            return response()->json($enseignes, 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
