<?php
namespace Middleware;

use Helpers\Response;
use Helpers\Security;

class AuthMiddleware {
    public static function authenticate(): array {
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';

        if (!preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            Response::error('Unauthorized - Missing or invalid token format', [], 401);
        }

        $token = $matches[1];
        $decoded = Security::decodeJWT($token);

        if (!$decoded || !isset($decoded['user_id'])) {
            Response::error('Unauthorized - Invalid or expired token', [], 401);
        }

        return $decoded;
    }
}