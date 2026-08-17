<?php
namespace Middleware;

use Helpers\Response;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;

class AuthMiddleware {
    public static function authenticate(): array {
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';

        if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            Response::error('Unauthorized access. Token missing.', [], 401);
        }

        $jwt = $matches[1];
        $jwtSecret = $_ENV['JWT_SECRET'] ?? 'default_fallback_secret_key';

        try {
            $decoded = JWT::decode($jwt, new Key($jwtSecret, 'HS256'));
            return (array) $decoded->data;
        } catch (Exception $e) {
            Response::error('Invalid or expired token.', [], 401);
        }
    }
}