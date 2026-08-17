<?php
namespace Models;

use Config\Database;
use PDO;

class User {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function findByEmailOrPhone(string $identifier): ?array {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :identifier OR phone = :identifier LIMIT 1");
        $stmt->execute(['identifier' => $identifier]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public function findById(string $id): ?array {
        $stmt = $this->db->prepare("SELECT id, first_name, email, phone, onboarding_completed, created_at FROM users WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public function create(string $firstName, string $email, string $phone, string $passwordHash): array {
        $stmt = $this->db->prepare("
            INSERT INTO users (first_name, email, phone, password_hash)
            VALUES (:first_name, :email, :phone, :password_hash)
            RETURNING id, first_name, email, phone, onboarding_completed, created_at
        ");
        $stmt->execute([
            'first_name' => $firstName,
            'email' => $email,
            'phone' => $phone,
            'password_hash' => $passwordHash
        ]);
        return $stmt->fetch();
    }
}