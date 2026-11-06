<?php
class Product extends Model
{
    // Search by title and/or filter by category (both optional)
    public function search(?string $q = null, ?int $categoryId = null): array
    {
        $sql = 'SELECT p.*, c.name AS category_name
                FROM products p
                JOIN categories c ON c.id = p.category_id
                WHERE 1 = 1';
        $params = [];

        if ($q) {
            $sql .= ' AND p.name LIKE ?';
            $params[] = '%' . $q . '%';
        }
        if ($categoryId) {
            $sql .= ' AND p.category_id = ?';
            $params[] = $categoryId;
        }
        $sql .= ' ORDER BY p.id DESC';

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT p.*, c.name AS category_name
             FROM products p
             JOIN categories c ON c.id = p.category_id
             WHERE p.id = ?'
        );
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function latest(int $limit): array
    {
        $limit = (int) $limit;
        return $this->db->query(
            "SELECT p.*, c.name AS category_name
             FROM products p
             JOIN categories c ON c.id = p.category_id
             ORDER BY p.id DESC LIMIT $limit"
        )->fetchAll();
    }

    public function create(array $d, ?string $image): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO products (category_id, name, description, price, stock, image)
             VALUES (?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([$d['category_id'], $d['name'], $d['description'], $d['price'], $d['stock'], $image]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $d, ?string $image): void
    {
        $stmt = $this->db->prepare(
            'UPDATE products
             SET category_id = ?, name = ?, description = ?, price = ?, stock = ?, image = ?
             WHERE id = ?'
        );
        $stmt->execute([$d['category_id'], $d['name'], $d['description'], $d['price'], $d['stock'], $image, $id]);
    }

    public function delete(int $id): void
    {
        $stmt = $this->db->prepare('DELETE FROM products WHERE id = ?');
        $stmt->execute([$id]);
    }

    public function count(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM products')->fetchColumn();
    }
}
