<?php

namespace App\Http\Controllers;

use App\Models\Ue;
use Illuminate\Http\Request;

class UEController extends Controller
{
    /**
     * Liste tous les UEs avec pagination
     *
     * @OA\Get(
     *     path="/api/ues",
     *     summary="Récupérer tous les UEs",
     *     tags={"UEs"},
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
     *         description="Liste paginée des UEs"
     *     )
     * )
     */
    public function index()
    {
        $perPage = request('per_page', 10);
        $ue = Ue::paginate($perPage);
        return response()->json($ue, 200);
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'code_ue' => 'required|min:3|string|unique:ue,code_ue',
                'label_ue' => 'required|min:3|string',
                'code_niveau' => 'required|integer|exists:niveau,code_niveau',
            ]);

            // Correction : creat -> create
            Ue::create($request->all());

            return response()->json(
                ["message" => "Ue créée avec succès"],
                201
            );

        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function show(string $code_ue)
    {
        try {
            $ue = Ue::with(['niveau', 'ecs'])->findOrFail($code_ue);
            return response()->json($ue, 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'UE non trouvée'
            ], 404);
        }
    }

    public function update(Request $request, string $code_ue)
    {
        try {
            $ue = Ue::findOrFail($code_ue);

            $validatedData = $request->validate([
                'label_ue' => 'sometimes|min:3|string',
                'desc_ue' => 'sometimes|string',
                'code_niveau' => 'sometimes|integer|exists:niveau,code_niveau',
            ]);

            $ue->update($validatedData);

            return response()->json([
                'message' => 'UE modifiée avec succès',
                'data' => $ue
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function destroy(string $code_ue)
    {
        try {
            $ue = Ue::findOrFail($code_ue);
            $ue->delete();

            return response()->json(
                ["message" => "Suppression réussie"],
                200
            );

        } catch (\Throwable $th) {
            return response()->json(
                ["message" => "Ue non trouvée"],
                404
            );
        }
    }
}
