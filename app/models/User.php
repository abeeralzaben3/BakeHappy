<?php
class User extends Model
{
    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE email = ?');
        $stmt->execute([$email]);
        return $stmt->fetch() ?: null;
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function create(string $name, string $email, string $password, string $phone): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO users (name, email, password_hash, phone) VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT), $phone]);
        return (int) $this->db->lastInsertId();
    }

    public function countCustomers(): int
    {
        return (int) $this->db->query("SELECT COUNT(*) FROM users WHERE role = 'customer'")->fetchColumn();
    }
}
