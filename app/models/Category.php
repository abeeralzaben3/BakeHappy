<?php
class Category extends Model
{
    public function all(): array
    {
        return $this->db->query('SELECT * FROM categories ORDER BY name')->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM categories WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function create(string $name, string $description, ?string $image): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO categories (name, description, image) VALUES (?, ?, ?)'
        );
        $stmt->execute([$name, $description, $image]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, string $name, string $description, ?string $image): void
    {
        $stmt = $this->db->prepare(
            'UPDATE categories SET name = ?, description = ?, image = ? WHERE id = ?'
        );
        $stmt->execute([$name, $description, $image, $id]);
    }

    public function delete(int $id): void
    {
        $stmt = $this->db->prepare('DELETE FROM categories WHERE id = ?');
        $stmt->execute([$id]);
    }

    public function hasProducts(int $id): bool
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM products WHERE category_id = ?');
        $stmt->execute([$id]);
        return (int) $stmt->fetchColumn() > 0;
    }
}
