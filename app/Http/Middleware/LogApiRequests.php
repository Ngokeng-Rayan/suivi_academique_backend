<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class LogApiRequests
{
    /**
     * Récupère l'ID de l'utilisateur connecté à partir du Bearer token
     */
    private function getUserId(Request $request): string
    {
        // Essayer d'abord Auth::user() (si Sanctum a authentifié)
        try {
            $user = Auth::guard('sanctum')->user();
            if ($user && isset($user->code_pers)) {
                return $user->code_pers;
            }
        } catch (\Exception $e) {
            // Continuer
        }

        // Chercher le token Bearer dans l'Authorization header
        $token = $request->bearerToken();
        
        if ($token) {
            try {
                // Sanctum stocke le token haché en SHA256 dans la base de données
                $hashedToken = hash('sha256', $token);
                
                $personalAccessToken = DB::table('personal_access_tokens')
                    ->where('token', $hashedToken)
                    ->where('tokenable_type', 'App\\Models\\Personnel')
                    ->first();
                
                if ($personalAccessToken && $personalAccessToken->tokenable_id) {
                    return $personalAccessToken->tokenable_id;
                }
            } catch (\Exception $e) {
                \Log::channel('api')->error('Error retrieving user from token', [
                    'error' => $e->getMessage(),
                    'path' => $request->getPathInfo()
                ]);
            }
        }

        return 'anonymous';
    }

    /**
     * Middleware pour logger toutes les requêtes API
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Log la requête entrante
        $startTime = microtime(true);
        $userId = $this->getUserId($request);

        Log::channel('api')->info('API Request', [
            'method' => $request->getMethod(),
            'path' => $request->getPathInfo(),
            'user_id' => $userId,
            'ip' => $request->getClientIp(),
            'user_agent' => $request->userAgent(),
            'query_params' => $request->query->all(),
            'timestamp' => now()->toDateTimeString(),
        ]);

        $response = $next($request);

        // Log la réponse
        $executionTime = (microtime(true) - $startTime) * 1000;

        Log::channel('api')->info('API Response', [
            'method' => $request->getMethod(),
            'path' => $request->getPathInfo(),
            'user_id' => $userId,
            'status_code' => $response->getStatusCode(),
            'execution_time_ms' => round($executionTime, 2),
            'timestamp' => now()->toDateTimeString(),
        ]);

        return $response;
    }
}
