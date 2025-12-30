<?php

namespace App\Http\Controllers;

use App\Models\Salle;
use Illuminate\Http\Request;

class SalleController extends Controller
{
    /**
     * Liste tous les salles avec pagination
     *
     * @OA\Get(
     *     path="/api/salles",
     *     summary="Récupérer tous les salles",
     *     tags={"Salles"},
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
     *         description="Liste paginée des salles"
     *     )
     * )
     */
    public function index()
    {
        $perPage = request('per_page', 10);
        $salles = Salle::with('programmations')->paginate($perPage);
        return response()->json($salles, 200);
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'num_sale' => 'required|string|unique:salle,num_sale',
                'contenance' => 'required|integer|min:1',
                'statut' => 'required|in:disponible,occupée,maintenance',
            ]);

            $salle = Salle::create($validatedData);

            return response()->json([
                'message' => 'Salle créée avec succès',
                'data' => $salle
            ], 201);

        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function show(string $num_sale)
    {
        try {
            $salle = Salle::with('programmations')->findOrFail($num_sale);
            return response()->json($salle, 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Salle non trouvée'
            ], 404);
        }
    }

    public function update(Request $request, string $num_sale)
    {
        try {
            $salle = Salle::findOrFail($num_sale);

            $validatedData = $request->validate([
                'contenance' => 'sometimes|integer|min:1',
                'statut' => 'sometimes|in:disponible,occupée,maintenance',
            ]);

            $salle->update($validatedData);

            return response()->json([
                'message' => 'Salle modifiée avec succès',
                'data' => $salle
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function destroy(string $num_sale)
    {
        try {
            $salle = Salle::findOrFail($num_sale);
            $salle->delete();

            return response()->json([
                'message' => 'Suppression réussie'
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Salle non trouvée'
            ], 404);
        }
    }
}
