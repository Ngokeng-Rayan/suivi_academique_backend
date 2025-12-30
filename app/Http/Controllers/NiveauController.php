<?php

namespace App\Http\Controllers;

use App\Models\Niveau;
use Illuminate\Http\Request;

class NiveauController extends Controller
{
    /**
     * Liste tous les niveaux avec pagination
     *
     * @OA\Get(
     *     path="/api/niveaux",
     *     summary="Récupérer tous les niveaux",
     *     tags={"Niveaux"},
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
     *         description="Liste paginée des niveaux"
     *     )
     * )
     */
    public function index()
    {
        $perPage = request('per_page', 10);
        $niveau = Niveau::paginate($perPage);
        return response()->json($niveau, 200);
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'label_niveau' => 'required|min:3|string',
                'code_filiere' => 'required|min:3|string|exists:filiere,code_filiere',
            ]);

            // Correction : creat -> create
            Niveau::create($request->all());

            return response()->json(
                ["message" => "Niveau créée avec succès"],
                201
            );

        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function show(int $code_niveau)
    {
        try {
            $niveau = Niveau::with(['filiere', 'ues'])->findOrFail($code_niveau);
            return response()->json($niveau, 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Niveau non trouvé'
            ], 404);
        }
    }

    public function update(Request $request, int $code_niveau)
    {
        try {
            $niveau = Niveau::findOrFail($code_niveau);

            $validatedData = $request->validate([
                'label_niveau' => 'sometimes|min:3|string',
                'desc_niveau' => 'sometimes|string',
                'code_filiere' => 'sometimes|min:3|string|exists:filiere,code_filiere',
            ]);

            $niveau->update($validatedData);

            return response()->json([
                'message' => 'Niveau modifié avec succès',
                'data' => $niveau
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function destroy(int $code_niveau)
    {
        try {
            $niveau = Niveau::findOrFail($code_niveau);
            $niveau->delete();

            return response()->json(
                ["message" => "Suppression réussie"],
                200
            );

        } catch (\Throwable $th) {
            return response()->json(
                ["message" => "Niveau non trouvée"],
                404
            );
        }
    }
}
