<?php

namespace App\Http\Controllers;

use App\Models\Ec;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Services\AuditLogger;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\EcExport;

class EcController extends Controller
{
    /**
     * Liste tous les ECs avec pagination
     *
     * @OA\Get(
     *     path="/api/ecs",
     *     summary="Récupérer tous les ECs",
     *     tags={"ECs"},
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
     *         description="Liste paginée des ECs"
     *     )
     * )
     */
    public function index()
    {
        $perPage = request('per_page', 10);
        $ecs = Ec::with(['ue', 'programmations', 'enseignes'])->paginate($perPage);
        return response()->json($ecs, 200);
    }

    /**
     * Créer un nouvel EC avec support de cours optionnel
     *
     * @OA\Post(
     *     path="/api/ecs",
     *     summary="Créer un nouvel EC",
     *     tags={"ECs"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"label_ec", "code_ue"},
     *                 @OA\Property(property="label_ec", type="string", example="EC Programmation"),
     *                 @OA\Property(property="desc_ec", type="string", example="Description de l'EC"),
     *                 @OA\Property(property="code_ue", type="string", example="UE001"),
     *                 @OA\Property(property="support_cours", type="string", format="binary", description="Fichier PDF, DOC, DOCX, etc.")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=201, description="EC créé avec succès")
     * )
     */
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'label_ec' => 'required|min:3|string',
                'desc_ec' => 'sometimes|string',
                'code_ue' => 'required|string|exists:ue,code_ue',
                'support_cours' => 'sometimes|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt,zip|max:10240'
            ]);

            // Traiter l'upload du fichier s'il existe
            if ($request->hasFile('support_cours')) {
                $file = $request->file('support_cours');
                $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();

                // Stocker le fichier dans storage/app/public/supports-cours
                $file->storeAs('supports-cours', $filename, 'public');

                $validatedData['support_cours'] = $filename;
            }

            $ec = Ec::create($validatedData);

            return response()->json([
                'message' => 'EC créé avec succès',
                'data' => $ec
            ], 201);

        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $code_ec)
    {
        try {
            $ec = Ec::with(['ue', 'programmations', 'enseignes'])->findOrFail($code_ec);
            return response()->json($ec, 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'EC non trouvé'
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $code_ec)
    {
        try {
            $ec = Ec::findOrFail($code_ec);

            $validatedData = $request->validate([
                'label_ec' => 'sometimes|min:3|string',
                'desc_ec' => 'sometimes|string',
                'code_ue' => 'sometimes|string|exists:ue,code_ue',
                'support_cours' => 'sometimes|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt,zip|max:10240'
            ]);

            // Traiter la modification du fichier
            if ($request->hasFile('support_cours')) {
                // Supprimer l'ancien fichier s'il existe
                if ($ec->support_cours && Storage::disk('public')->exists('supports-cours/' . $ec->support_cours)) {
                    Storage::disk('public')->delete('supports-cours/' . $ec->support_cours);
                }

                // Stocker le nouveau fichier
                $file = $request->file('support_cours');
                $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
                $file->storeAs('supports-cours', $filename, 'public');

                $validatedData['support_cours'] = $filename;
            }

            $ec->update($validatedData);

            return response()->json([
                'message' => 'EC modifié avec succès',
                'data' => $ec
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $code_ec)
    {
        try {
            $ec = Ec::findOrFail($code_ec);

            // Supprimer le fichier associé s'il existe
            if ($ec->support_cours && Storage::disk('public')->exists('supports-cours/' . $ec->support_cours)) {
                Storage::disk('public')->delete('supports-cours/' . $ec->support_cours);
            }

            $ec->delete();

            return response()->json([
                'message' => 'Suppression réussie'
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'EC non trouvé'
            ], 404);
        }
    }

    /**
     * Exporter tous les EC en Excel
     */
    public function exportExcel()
    {
        try {
            AuditLogger::log('EXPORT_ECS_EXCEL', [
                'format' => 'xlsx',
                'total_records' => Ec::count(),
            ]);

            return Excel::download(new EcExport(), 'ecs_' . date('Y-m-d_H-i-s') . '.xlsx');
        } catch (\Throwable $th) {
            AuditLogger::logError('EXPORT_ECS_EXCEL', $th->getMessage());
            return response()->json([
                'message' => 'Erreur lors de l\'export Excel: ' . $th->getMessage()
            ], 500);
        }
    }

    /**
     * Exporter tous les EC en JSON pour conversion PDF côté frontend
     */
    public function exportPdf()
    {
        try {
            $ecs = Ec::with('ue')->get()->map(function ($ec) {
                return [
                    'code_ec' => $ec->code_ec,
                    'label_ec' => $ec->label_ec,
                    'desc_ec' => $ec->desc_ec,
                    'label_ue' => $ec->ue->label_ue ?? 'N/A',
                    'created_at' => $ec->created_at,
                    'updated_at' => $ec->updated_at,
                ];
            });

            AuditLogger::log('EXPORT_ECS_PDF', [
                'format' => 'pdf',
                'total_records' => $ecs->count(),
            ]);

            return response()->json([
                'success' => true,
                'data' => $ecs,
                'filename' => 'ecs_' . date('Y-m-d_H-i-s')
            ], 200);
        } catch (\Throwable $th) {
            AuditLogger::logError('EXPORT_ECS_PDF', $th->getMessage());
            return response()->json([
                'message' => 'Erreur lors de la préparation du PDF: ' . $th->getMessage()
            ], 500);
        }
    }
}
