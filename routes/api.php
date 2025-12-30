<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FiliereController;
use App\Http\Controllers\NiveauController;
use App\Http\Controllers\UEController;
use App\Http\Controllers\EcController;
use App\Http\Controllers\PersonnelController;
use App\Http\Controllers\SalleController;
use App\Http\Controllers\ProgrammationController;
use App\Http\Controllers\EnseigneController;
use App\Http\Controllers\AuthController;

// Route de test
Route::get('/test', function () {
    return response()->json([
        'message' => 'API fonctionne correctement',
        'timestamp' => now()->toDateTimeString()
    ]);
});

// Routes publiques (sans authentification)
Route::apiResource("personnels", PersonnelController::class);
Route::apiResource("filieres", FiliereController::class);
Route::apiResource("niveaux", NiveauController::class);
Route::apiResource("ues", UEController::class);
Route::apiResource("ecs", EcController::class);
Route::apiResource("salles", SalleController::class);

// Routes d'export pour les filières
Route::prefix('filieres')->group(function () {
    Route::get('/export/excel', [FiliereController::class, 'exportExcel']);
    Route::get('/export/pdf', [FiliereController::class, 'exportPdf']);
});

// Routes d'export pour les EC
Route::prefix('ecs')->group(function () {
    Route::get('/export/excel', [EcController::class, 'exportExcel']);
    Route::get('/export/pdf', [EcController::class, 'exportPdf']);
});

// Routes API pour les programmations
Route::prefix('programmations')->group(function () {
    Route::get('/', [ProgrammationController::class, 'index']);
    Route::post('/', [ProgrammationController::class, 'store']);
    Route::get('/date/{date}', [ProgrammationController::class, 'getByDate']);
    Route::get('/salle/{num_salle}', [ProgrammationController::class, 'getBySalle']);
    Route::get('/personnel/{code_pers}', [ProgrammationController::class, 'getByPersonnel']);
    Route::get('/show', [ProgrammationController::class, 'show']);
    Route::put('/update', [ProgrammationController::class, 'update']);
    Route::delete('/delete', [ProgrammationController::class, 'destroy']);
});

// Routes API pour les enseignements
Route::prefix('enseignes')->group(function () {
    Route::get('/', [EnseigneController::class, 'index']);
    Route::post('/', [EnseigneController::class, 'store']);
    Route::get('/personnel/{code_pers}', [EnseigneController::class, 'getByPersonnel']);
    Route::get('/ec/{code_ec}', [EnseigneController::class, 'getByEc']);
    Route::get('/show', [EnseigneController::class, 'show']);
    Route::put('/update', [EnseigneController::class, 'update']);
    Route::delete('/delete', [EnseigneController::class, 'destroy']);
});

// Route publique pour le login
Route::post('/login', [AuthController::class, 'login']);

// Routes protégées par Sanctum (si nécessaire plus tard)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
});

