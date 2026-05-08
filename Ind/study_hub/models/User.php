<?php

require_once __DIR__ . '/../config/database.php';

/**
 * User Model
 */
class User
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * Find a user by email.
     *
     * @param string $email
     * @return array|false The user data or false if not found.
     */
    public function findByEmail(string $email): array|false
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        return $stmt->fetch();
    }

    /**
     * Find a user by ID.
     *
     * @param int $id
     * @return array|false The user data or false if not found.
     */
    public function findById(int $id): array|false
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    /**
     * Find all users.
     *
     * @return array The list of users.
     */
    public function findAll(): array
    {
        $stmt = $this->db->query("SELECT id, username, email, role, created_at FROM users ORDER BY created_at DESC");
        return $stmt->fetchAll();
    }

    /**
     * Create a new user.
     *
     * @param string $username
     * @param string $email
     * @param string $passwordHash
     * @return bool True on success, false on failure.
     */
    public function create(string $username, string $email, string $passwordHash): bool
    {
        $stmt = $this->db->prepare(
            "INSERT INTO users (username, email, password_hash) VALUES (:username, :email, :password_hash)"
        );
        return $stmt->execute([
            'username' => $username,
            'email' => $email,
            'password_hash' => $passwordHash,
        ]);
    }

    /**
     * Set a reset token for a user.
     *
     * @param int $id
     * @param string $token
     * @return bool
     */
    public function setResetToken(int $id, string $token): bool
    {
        $stmt = $this->db->prepare("UPDATE users SET reset_token = :token WHERE id = :id");
        return $stmt->execute(['token' => $token, 'id' => $id]);
    }

    /**
     * Update user role.
     *
     * @param int $id
     * @param string $role
     * @return bool
     */
    public function updateRole(int $id, string $role): bool
    {
        $stmt = $this->db->prepare("UPDATE users SET role = :role WHERE id = :id");
        return $stmt->execute(['role' => $role, 'id' => $id]);
    }
}
