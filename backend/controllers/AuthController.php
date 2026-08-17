<?php
namespace Controllers;

use Config\Database;
use Helpers\Response;
use Middleware\AuthMiddleware;
use Firebase\JWT\JWT;
use PDO;

class AuthController {
    private PDO $db;
    private string $jwtSecret;

    public function __construct() {
        $this->db = Database::getConnection();
        $this->jwtSecret = $_ENV['JWT_SECRET'] ?? 'default_fallback_secret_key';
    }

    // POST /api/auth/register
    public function register(): void {
        $data = json_decode(file_get_contents('php://input'), true) ?? [];

        $firstName = trim($data['first_name'] ?? '');
        $email = strtolower(trim($data['email'] ?? ''));
        $phone = trim($data['phone'] ?? '');
        $password = $data['password'] ?? '';

        // Validation
        if (empty($firstName) || empty($email) || empty($phone) || empty($password)) {
            Response::error('All fields (first_name, email, phone, password) are required.', [], 422);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Response::error('Invalid email address format.', [], 422);
        }

        if (strlen($password) < 6) {
            Response::error('Password must be at least 6 characters long.', [], 422);
        }

        // Check duplicate email/phone
        $stmt = $this->db->prepare("SELECT id FROM users WHERE email = :email OR phone = :phone LIMIT 1");
        $stmt->execute(['email' => $email, 'phone' => $phone]);
        if ($stmt->fetch()) {
            Response::error('An account with this email or phone number already exists.', [], 409);
        }

        // Hash Password & Insert User
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

        $userId = $this->db->lastInsertId();

        // Fetch user record for response
        $stmt = $this->db->prepare("SELECT id, first_name, email, phone, onboarding_completed, created_at FROM users WHERE id = :id");
        $stmt->execute(['id' => $userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Generate JWT Token
        $token = $this->generateJWT((string)$user['id'], $user['email']);

        Response::success([
            'token' => $token,
            'user' => $user
        ], 'Registration successful', 201);
    }

    // POST /api/auth/login
    public function login(): void {
        $data = json_decode(file_get_contents('php://input'), true) ?? [];

        $email = strtolower(trim($data['email'] ?? ''));
        $password = $data['password'] ?? '';

        if (empty($email) || empty($password)) {
            Response::error('Email and password are required.', [], 422);
        }

        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user || !password_verify($password, $user['password_hash'])) {
            Response::error('Invalid email or password.', [], 401);
        }

        unset($user['password_hash']);

        // Generate JWT Token
        $token = $this->generateJWT((string)$user['id'], $user['email']);

        Response::success([
            'token' => $token,
            'user' => $user
        ], 'Login successful');
    }

    // GET /api/auth/me
    public function me(): void {
        $userData = AuthMiddleware::authenticate();
        $userId = $userData['user_id'];

        $stmt = $this->db->prepare("SELECT id, first_name, email, phone, onboarding_completed, created_at FROM users WHERE id = :id");
        $stmt->execute(['id' => $userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            Response::error('User not found.', [], 404);
        }

        Response::success(['user' => $user]);
    }

    private function generateJWT(string $userId, string $email): string {
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
    }
}