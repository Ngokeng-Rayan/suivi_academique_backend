<?php

namespace App\Http\Controllers;

use App\Models\Filiere;
use Illuminate\Http\Request;
use App\Services\AuditLogger;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\FiliereExport;

/**
 * @OA\Info(
 *     title="API Suivi Académique",
 *     version="1.0.0"
 * )
 *
 * @OA\Tag(
 *     name="Filières",
 *     description="Endpoints liés aux filières"
 * )
 */
class FiliereController extends Controller
{
    /**
     * Liste toutes les filières avec pagination
     *
     * @OA\Get(
     *     path="/api/filieres",
     *     summary="Récupérer toutes les filières",
     *     tags={"Filières"},
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
     *         description="Liste paginée des filières"
     *     )
     * )
     */
    public function index()
    {
        $perPage = request('per_page', 10);
        $filieres = Filiere::with('niveaux')->paginate($perPage);
        return response()->json($filieres, 200);
    }

    /**
     * Créer une nouvelle filière
     *
     * @OA\Post(
     *     path="/api/filieres",
     *     summary="Créer une filière",
     *     tags={"Filières"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"code_filiere", "label_filiere"},
     *             @OA\Property(property="code_filiere", type="string", example="INF01"),
     *             @OA\Property(property="label_filiere", type="string", example="Informatique")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Filière créée"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erreur serveur"
     *     )
     * )
     */
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'code_filiere' => 'required|min:3|string|unique:filiere,code_filiere',
                'label_filiere' => 'required|min:3|string',
            ]);

            // Correction : creat -> create
            $filiere = Filiere::create($request->all());

            // Log la création
            AuditLogger::logCreate('Filiere', [
                'code_filiere' => $filiere->code_filiere,
                'label_filiere' => $filiere->label_filiere,
            ]);

            return response()->json(
                ["message" => "Filière créée avec succès"],
                201
            );

        } catch (\Throwable $th) {
            AuditLogger::logError('CREATE_FILIERE', $th->getMessage());
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Afficher une filière spécifique
     *
     * @OA\Get(
     *     path="/api/filieres/{code_filiere}",
     *     summary="Récupérer une filière",
     *     tags={"Filières"},
     *     @OA\Parameter(
     *         name="code_filiere",
     *         in="path",
     *         required=true,
     *         description="Code de la filière",
     *         @OA\Schema(type="string"),
     *         example="INF01"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Filière trouvée"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Filière non trouvée"
     *     )
     * )
     */
    public function show($code_filiere)
    {
        try {
            $filiere = Filiere::with('niveaux')->findOrFail($code_filiere);
            return response()->json($filiere, 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Filière non trouvée'
            ], 404);
        }
    }

    /**
     * Mettre à jour une filière
     *
     * @OA\Put(
     *     path="/api/filieres/{code_filiere}",
     *     summary="Modifier une filière",
     *     tags={"Filières"},
     *     @OA\Parameter(
     *         name="code_filiere",
     *         in="path",
     *         required=true,
     *         description="Code de la filière",
     *         @OA\Schema(type="string"),
     *         example="INF01"
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="label_filiere", type="string", example="Informatique et Réseaux"),
     *             @OA\Property(property="desc_filiere", type="string", example="Description mise à jour")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Filière modifiée"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Filière non trouvée"
     *     )
     * )
     */
    public function update(Request $request, $code_filiere)
    {
        try {
            $filiere = Filiere::findOrFail($code_filiere);
            $oldData = $filiere->toArray();

            $validatedData = $request->validate([
                'label_filiere' => 'sometimes|min:3|string',
                'desc_filiere' => 'sometimes|string',
            ]);

            $filiere->update($validatedData);

            // Log la modification
            AuditLogger::logUpdate('Filiere', $code_filiere, $oldData, $filiere->toArray());

            return response()->json([
                'message' => 'Filière modifiée avec succès',
                'data' => $filiere
            ], 200);

        } catch (\Throwable $th) {
            AuditLogger::logError('UPDATE_FILIERE', $th->getMessage(), ['code_filiere' => $code_filiere]);
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Supprimer une filière
     *
     * @OA\Delete(
     *     path="/api/filieres/{code_filiere}",
     *     summary="Supprimer une filière",
     *     tags={"Filières"},
     *     @OA\Parameter(
     *         name="code_filiere",
     *         in="path",
     *         required=true,
     *         description="Code de la filière",
     *         @OA\Schema(type="string"),
     *         example="INF01"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Filière supprimée"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Filière non trouvée"
     *     )
     * )
     */
    public function destroy($code_filiere)
    {
        try {
            $filiere = Filiere::findOrFail($code_filiere);
            $deletedData = $filiere->toArray();
            $filiere->delete();

            // Log la suppression
            AuditLogger::logDelete('Filiere', $code_filiere, $deletedData);

            return response()->json(
                ["message" => "Suppression réussie"],
                200
            );

        } catch (\Throwable $th) {
            AuditLogger::logError('DELETE_FILIERE', $th->getMessage(), ['code_filiere' => $code_filiere]);
            return response()->json(
                ["message" => "Filière non trouvée"],
                404
            );
        }
    }

    /**
     * Exporter toutes les filières en Excel
     */
    public function exportExcel()
    {
        try {
            AuditLogger::log('EXPORT_FILIERES_EXCEL', [
                'format' => 'xlsx',
                'total_records' => Filiere::count(),
            ]);

            return Excel::download(new FiliereExport(), 'filieres_' . date('Y-m-d_H-i-s') . '.xlsx');
        } catch (\Throwable $th) {
            AuditLogger::logError('EXPORT_FILIERES_EXCEL', $th->getMessage());
            return response()->json([
                'message' => 'Erreur lors de l\'export Excel: ' . $th->getMessage()
            ], 500);
        }
    }

    /**
     * Exporter toutes les filières en JSON pour conversion PDF côté frontend
     */
    public function exportPdf()
    {
        try {
            $filieres = Filiere::all(['code_filiere', 'label_filiere', 'desc_filiere', 'created_at', 'updated_at']);

            AuditLogger::log('EXPORT_FILIERES_PDF', [
                'format' => 'pdf',
                'total_records' => $filieres->count(),
            ]);

            return response()->json([
                'success' => true,
                'data' => $filieres,
                'filename' => 'filieres_' . date('Y-m-d_H-i-s')
            ], 200);
        } catch (\Throwable $th) {
            AuditLogger::logError('EXPORT_FILIERES_PDF', $th->getMessage());
            return response()->json([
                'message' => 'Erreur lors de la préparation du PDF: ' . $th->getMessage()
            ], 500);
        }
    }
}
