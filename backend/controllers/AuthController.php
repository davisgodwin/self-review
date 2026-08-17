<?php
namespace Controllers;

use Models\User;
use Helpers\Response;
use Helpers\Security;
use Middleware\AuthMiddleware;

class AuthController {
    private User $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    public function register(): void {
        $data = json_decode(file_get_contents('php://input'), true) ?? [];

        $errors = [];
        if (empty($data['first_name'])) $errors['first_name'] = 'First name is required';
        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Valid email is required';
        if (empty($data['phone'])) $errors['phone'] = 'Phone number is required';
        if (empty($data['password']) || strlen($data['password']) < 8) $errors['password'] = 'Password must be at least 8 characters';
        if (($data['password'] ?? '') !== ($data['confirm_password'] ?? '')) $errors['confirm_password'] = 'Passwords do not match';
        if (empty($data['terms'])) $errors['terms'] = 'You must accept the Terms & Privacy';

        if (!empty($errors)) {
            Response::error('Validation failed', $errors, 422);
        }

        if ($this->userModel->findByEmailOrPhone($data['email'])) {
            Response::error('Email is already registered', ['email' => 'Email taken'], 409);
        }
        if ($this->userModel->findByEmailOrPhone($data['phone'])) {
            Response::error('Phone number is already registered', ['phone' => 'Phone number taken'], 409);
        }

        $hash = Security::hashPassword($data['password']);
        $user = $this->userModel->create($data['first_name'], $data['email'], $data['phone'], $hash);
        $token = Security::generateJWT(['user_id' => $user['id']]);

        Response::success([
            'token' => $token,
            'user' => $user
        ], 'Registration successful', 201);
    }

    public function login(): void {
        $data = json_decode(file_get_contents('php://input'), true) ?? [];

        $identifier = trim($data['identifier'] ?? '');
        $password = $data['password'] ?? '';

        if (empty($identifier) || empty($password)) {
            Response::error('Email/Phone and password are required', [], 422);
        }

        $user = $this->userModel->findByEmailOrPhone($identifier);

        if (!$user || !Security::verifyPassword($password, $user['password_hash'])) {
            Response::error('Invalid credentials', [], 401);
        }

        $token = Security::generateJWT(['user_id' => $user['id']]);
        unset($user['password_hash']);

        Response::success([
            'token' => $token,
            'user' => $user
        ], 'Login successful');
    }

    public function me(): void {
        $authUser = AuthMiddleware::authenticate();
        $user = $this->userModel->findById($authUser['user_id']);

        if (!$user) {
            Response::error('User not found', [], 404);
        }

        Response::success(['user' => $user]);
    }
}