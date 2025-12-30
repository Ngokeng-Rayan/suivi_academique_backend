<?php

namespace App\Http\Controllers;

use App\Models\Programmation;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Services\AuditLogger;

class ProgrammationController extends Controller
{
    /**
     * Liste tous les programmations avec pagination
     *
     * @OA\Get(
     *     path="/api/programmations",
     *     summary="Récupérer tous les programmations",
     *     tags={"Programmations"},
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
     *         description="Liste paginée des programmations"
     *     )
     * )
     */
    public function index()
    {
        $perPage = request('per_page', 10);
        $programmations = Programmation::with(['ec', 'salle', 'personnel'])->paginate($perPage);
        return response()->json($programmations, 200);
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'code_ec' => 'required|integer|exists:ec,code_ec',
                'num_salle' => 'required|string|exists:salle,num_sale',
                'code_pers' => 'required|string|exists:personnel,code_pers',
                'date' => 'required|date',
                'heure_debut' => 'required|date_format:H:i',
                'heure_fin' => 'required|date_format:H:i|after:heure_debut',
                'nbre_heure' => 'required|integer|min:1',
                'status' => 'required|in:planifié,en cours,terminé,annulé',
            ]);

            // Vérifier qu'il n'y a pas de conflit de programmation
            $conflit = Programmation::where('num_salle', $validatedData['num_salle'])
                ->where('date', $validatedData['date'])
                ->where(function ($query) use ($validatedData) {
                    $query->whereBetween('heure_debut', [$validatedData['heure_debut'], $validatedData['heure_fin']])
                        ->orWhereBetween('heure_fin', [$validatedData['heure_debut'], $validatedData['heure_fin']]);
                })
                ->exists();

            if ($conflit) {
                AuditLogger::log('PROGRAMMATION_CONFLICT', [
                    'num_salle' => $validatedData['num_salle'],
                    'date' => $validatedData['date'],
                ], 'warning');
                return response()->json([
                    'message' => 'Conflit de programmation : la salle est déjà réservée pour cette période'
                ], 409);
            }

            $programmation = Programmation::create($validatedData);

            // Log la création
            AuditLogger::logCreate('Programmation', [
                'code_ec' => $programmation->code_ec,
                'num_salle' => $programmation->num_salle,
                'date' => $programmation->date,
            ]);

            return response()->json([
                'message' => 'Programmation créée avec succès',
                'data' => $programmation
            ], 201);

        } catch (\Throwable $th) {
            if ($th instanceof ValidationException) {
                throw $th;
            }
            AuditLogger::logError('CREATE_PROGRAMMATION', $th->getMessage());
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function show(Request $request)
    {
        try {
            // Comme la clé primaire est composite, on doit chercher par les 3 clés
            $programmation = Programmation::where('code_ec', $request->code_ec)
                ->where('num_salle', $request->num_salle)
                ->where('code_pers', $request->code_pers)
                ->with(['ec', 'salle', 'personnel'])
                ->firstOrFail();

            return response()->json($programmation, 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Programmation non trouvée'
            ], 404);
        }
    }

    public function update(Request $request)
    {
        try {
            $oldProgrammation = Programmation::where('code_ec', $request->code_ec)
                ->where('num_salle', $request->num_salle)
                ->where('code_pers', $request->code_pers)
                ->first();

            $validatedData = $request->validate([
                'date' => 'sometimes|date',
                'heure_debut' => 'sometimes|date_format:H:i',
                'heure_fin' => 'sometimes|date_format:H:i|after:heure_debut',
                'nbre_heure' => 'sometimes|integer|min:1',
                'status' => 'sometimes|in:planifié,en cours,terminé,annulé',
            ]);

            Programmation::where('code_ec', $request->code_ec)
                ->where('num_salle', $request->num_salle)
                ->where('code_pers', $request->code_pers)
                ->update($validatedData);

            $programmation = Programmation::where('code_ec', $request->code_ec)
                ->where('num_salle', $request->num_salle)
                ->where('code_pers', $request->code_pers)
                ->firstOrFail();

            // Log la modification
            AuditLogger::logUpdate('Programmation',
                "{$request->code_ec}-{$request->num_salle}-{$request->code_pers}",
                $oldProgrammation->toArray(),
                $programmation->toArray());

            return response()->json([
                'message' => 'Programmation modifiée avec succès',
                'data' => $programmation
            ], 200);

        } catch (\Throwable $th) {
            if ($th instanceof ValidationException) {
                throw $th;
            }
            AuditLogger::logError('UPDATE_PROGRAMMATION', $th->getMessage());
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function destroy(Request $request)
    {
        try {
            $programmation = Programmation::where('code_ec', $request->code_ec)
                ->where('num_salle', $request->num_salle)
                ->where('code_pers', $request->code_pers)
                ->first();

            if (!$programmation) {
                return response()->json([
                    'message' => 'Programmation non trouvée'
                ], 404);
            }

            $deletedData = $programmation->toArray();
            $programmation->delete();

            // Log la suppression
            AuditLogger::logDelete('Programmation',
                "{$request->code_ec}-{$request->num_salle}-{$request->code_pers}",
                $deletedData);

            return response()->json([
                'message' => 'Suppression réussie'
            ], 200);

        } catch (\Throwable $th) {
            AuditLogger::logError('DELETE_PROGRAMMATION', $th->getMessage());
            return response()->json([
                'message' => 'Programmation non trouvée'
            ], 404);
        }
    }

    public function getByDate($date)
    {
        try {
            $programmations = Programmation::where('date', $date)
                ->with(['ec', 'salle', 'personnel'])
                ->get();

            return response()->json($programmations, 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function getBySalle($num_salle)
    {
        try {
            $programmations = Programmation::where('num_salle', $num_salle)
                ->with(['ec', 'salle', 'personnel'])
                ->get();

            return response()->json($programmations, 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function getByPersonnel($code_pers)
    {
        try {
            $programmations = Programmation::where('code_pers', $code_pers)
                ->with(['ec', 'salle', 'personnel'])
                ->get();

            return response()->json($programmations, 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
