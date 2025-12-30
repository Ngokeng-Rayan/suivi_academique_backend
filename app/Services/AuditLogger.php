<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class AuditLogger
{
    /**
     * Récupère l'ID de l'utilisateur connecté
     *
     * @return string
     */
    private static function getUserId(): string
    {
        $user = Auth::user();
        return $user ? $user->code_pers : 'system';
    }

    /**
     * Logger une action de création
     *
     * @param string $entity
     * @param array $data
     * @param string|null $userId
     */
    public static function logCreate(string $entity, array $data, ?string $userId = null): void
    {
        Log::channel('audit')->info("CREATE - {$entity}", [
            'entity' => $entity,
            'data' => $data,
            'user_id' => $userId ?? self::getUserId(),
            'ip' => request()->getClientIp(),
            'timestamp' => now()->toDateTimeString(),
        ]);
    }

    /**
     * Logger une action de modification
     *
     * @param string $entity
     * @param mixed $entityId
     * @param array $oldData
     * @param array $newData
     * @param string|null $userId
     */
    public static function logUpdate(string $entity, $entityId, array $oldData, array $newData, ?string $userId = null): void
    {
        Log::channel('audit')->info("UPDATE - {$entity}", [
            'entity' => $entity,
            'entity_id' => $entityId,
            'old_data' => $oldData,
            'new_data' => $newData,
            'user_id' => $userId ?? self::getUserId(),
            'ip' => request()->getClientIp(),
            'timestamp' => now()->toDateTimeString(),
        ]);
    }

    /**
     * Logger une action de suppression
     *
     * @param string $entity
     * @param mixed $entityId
     * @param array $data
     * @param string|null $userId
     */
    public static function logDelete(string $entity, $entityId, array $data, ?string $userId = null): void
    {
        Log::channel('audit')->warning("DELETE - {$entity}", [
            'entity' => $entity,
            'entity_id' => $entityId,
            'deleted_data' => $data,
            'user_id' => $userId ?? self::getUserId(),
            'ip' => request()->getClientIp(),
            'timestamp' => now()->toDateTimeString(),
        ]);
    }

    /**
     * Logger une tentative de login
     *
     * @param string $login
     * @param bool $success
     * @param string|null $userId
     */
    public static function logLogin(string $login, bool $success, ?string $userId = null): void
    {
        $level = $success ? 'info' : 'warning';
        $action = $success ? 'LOGIN_SUCCESS' : 'LOGIN_FAILED';

        Log::channel('audit')->$level("AUTH - {$action}", [
            'login' => $login,
            'user_id' => $userId,
            'ip' => request()->getClientIp(),
            'timestamp' => now()->toDateTimeString(),
        ]);
    }

    /**
     * Logger une action d'erreur
     *
     * @param string $action
     * @param string $message
     * @param array $context
     */
    public static function logError(string $action, string $message, array $context = []): void
    {
        Log::channel('error')->error("ERROR - {$action}", array_merge([
            'message' => $message,
            'user_id' => self::getUserId(),
            'ip' => request()->getClientIp(),
            'timestamp' => now()->toDateTimeString(),
        ], $context));
    }

    /**
     * Logger une action personnalisée
     *
     * @param string $action
     * @param array $data
     * @param string $level
     */
    public static function log(string $action, array $data, string $level = 'info'): void
    {
        Log::channel('audit')->$level("CUSTOM - {$action}", array_merge([
            'user_id' => self::getUserId(),
            'ip' => request()->getClientIp(),
            'timestamp' => now()->toDateTimeString(),
        ], $data));
    }
}
