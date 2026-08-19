<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/Response.php'; // Capital R
require_once __DIR__ . '/../helpers/Security.php'; // Capital S
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../controllers/OnboardingController.php';

// Allow CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

$authController = new Controllers\AuthController();
$onboardingController = new Controllers\OnboardingController();

// Auth Routes
if ($uri === '/api/auth/register' && $method === 'POST') {
    $authController->register();
} elseif ($uri === '/api/auth/login' && $method === 'POST') {
    $authController->login();
} elseif ($uri === '/api/auth/me' && $method === 'GET') {
    $authController->me();

// Onboarding Routes
} elseif ($uri === '/api/onboarding/options' && $method === 'GET') {
    $onboardingController->getOptions();
} elseif ($uri === '/api/onboarding/complete' && $method === 'POST') {
    $onboardingController->complete();

} else {
    Helpers\Response::error('Endpoint not found', [], 404);
}