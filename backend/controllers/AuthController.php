<?php
namespace Controllers;

use Config\Database;
use Helpers\Response;
use Middleware\AuthMiddleware;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use PDO;
use Exception;

class AuthController {
    private PDO $db;
    private string $jwtSecret;

    public function __construct() {
        $this->db = Database::getConnection();
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $this->jwtSecret = $_ENV['JWT_SECRET'] ?? 'default_fallback_secret_key';
    }

    // POST /api/auth/register
    public function register(): void {
        try {
            $data = json_decode(file_get_contents('php://input'), true) ?? [];

            $firstName = trim($data['first_name'] ?? '');
            $email = strtolower(trim($data['email'] ?? ''));
            $phone = trim($data['phone'] ?? '');
            $password = $data['password'] ?? '';

            if (empty($firstName) || empty($email) || empty($phone) || empty($password)) {
                Response::error('All fields (first_name, email, phone, password) are required.', [], 422);
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                Response::error('Invalid email address format.', [], 422);
            }

            if (strlen($password) < 6) {
                Response::error('Password must be at least 6 characters long.', [], 422);
            }

            // Check duplicate email or phone
            $stmt = $this->db->prepare("SELECT id FROM users WHERE email = :email OR phone = :phone LIMIT 1");
            $stmt->execute(['email' => $email, 'phone' => $phone]);
            if ($stmt->fetch()) {
                Response::error('An account with this email or phone number already exists.', [], 409);
            }

            $passwordHash = password_hash($password, PASSWORD_BCRYPT);

            $stmt = $this->db->prepare("
                INSERT INTO users (first_name, email, phone, password_hash, onboarding_completed)
                VALUES (:first_name, :email, :phone, :password_hash, FALSE)
            ");

            $stmt->execute([
                'first_name' => $firstName,
                'email' => $email,
                'phone' => $phone,
                'password_hash' => $passwordHash
            ]);

            // Fetch created user
            $stmt = $this->db->prepare("
                SELECT id, first_name, email, phone, onboarding_completed, created_at 
                FROM users 
                WHERE email = :email 
                LIMIT 1
            ");
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user || !isset($user['id'])) {
                Response::error('User registration failed: User record could not be retrieved.', [], 500);
            }

            // Safely generate token
            $token = $this->generateJWT((string)$user['id'], (string)$user['email']);

            Response::success([
                'token' => $token,
                'user' => $user
            ], 'Registration successful', 201);

        } catch (Exception $e) {
            Response::error('Registration Error: ' . $e->getMessage(), [], 500);
        }
    }

    private function generateJWT(string $userId, string $email): string {
        try {
            $issuedAt = time();
            $expirationTime = $issuedAt + (60 * 60 * 24 * 7); // 7 days token validity

            $payload = [
                'iat' => $issuedAt,
                'exp' => $expirationTime,
                'data' => [
                    'user_id' => $userId,
                    'email' => $email
                ]
            ];

            return JWT::encode($payload, $this->jwtSecret, 'HS256');
        } catch (Exception $e) {
            // Fallback for native HMAC token in case firebase/php-jwt library throws driver exception
            $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
            $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
            
            $payloadData = json_encode([
                'iat' => time(),
                'exp' => time() + (60 * 60 * 24 * 7),
                'data' => ['user_id' => $userId, 'email' => $email]
            ]);
            $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payloadData));

            $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $this->jwtSecret, true);
            $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

            return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
        }
    }
}