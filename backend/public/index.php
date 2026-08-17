<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/response.php';
require_once __DIR__ . '/../helpers/security.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../controllers/AuthController.php';

// Allow CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$authController = new Controllers\AuthController();

if ($uri === '/api/auth/register' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $authController->register();
} elseif ($uri === '/api/auth/login' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $authController->login();
} elseif ($uri === '/api/auth/me' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $authController->me();
} else {
    Helpers\Response::error('Endpoint not found', [], 404);
}