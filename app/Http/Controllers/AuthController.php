<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Personnel;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use App\Services\AuditLogger;
use App\Mail\LoginCredentialsMail;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            "login_pers" => "required|email",
            "pwd_pers" => "required|string|min:6",
        ]);

        $personnel = Personnel::where("login_pers", $credentials["login_pers"])->first();

        if (!$personnel || !Hash::check($credentials["pwd_pers"], $personnel->pwd_pers)) {
            // Log la tentative échouée
            AuditLogger::logLogin($credentials["login_pers"], false);
            return response()->json(['message' => 'Invalid login or password'], 401);
        }

        // Supprimer les anciens tokens
        $old_token = DB::table('personal_access_tokens')
            ->where('tokenable_id', $personnel->code_pers)
            ->first();

        if ($old_token) {
            DB::table('personal_access_tokens')
                ->where('id', $old_token->id)
                ->delete();
        }

        // Créer un nouveau token
        $expiration = Carbon::now()->addDays(1);
        $token = $personnel->createToken('user_token', ["*"], $expiration)->plainTextToken;

        // Log la connexion réussie
        AuditLogger::logLogin($credentials["login_pers"], true, $personnel->code_pers);

        // Préparer les données de l'utilisateur sans le mot de passe
        $userData = [
            'code_pers' => $personnel->code_pers,
            'nom_pers' => $personnel->nom_pers,
            'prenom_pers' => $personnel->prenom_pers,
            'sexe_pers' => $personnel->sexe_pers,
            'phone_pers' => $personnel->phone_pers,
            'login_pers' => $personnel->login_pers,
            'type_pers' => $personnel->type_pers,
        ];

        return response()->json([
            "success" => true,
            "message" => "Login successful.",
            "user" => $userData,
            "access_token" => $token,
            "token_type" => "Bearer",
            "expires_at" => $expiration->toDateTimeString(),
        ]);
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        $user->currentAccessToken()->delete();

        // Log la déconnexion
        AuditLogger::log('LOGOUT', [
            'user_id' => $user->code_pers,
        ]);

        return response()->json(['message' => 'Logged out successfully'], 200);
    }
}
