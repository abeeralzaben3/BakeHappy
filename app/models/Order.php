<?php
class Order extends Model
{
    public const STATUSES = ['Pending', 'Processing', 'Shipped', 'Delivered', 'Cancelled'];
    public const PAYMENTS = ['Unpaid', 'Paid'];

    // Saves the order + its items + reduces stock. All or nothing (transaction).
    // payment_method=COD, payment_status=Unpaid, status=Pending come from the table defaults.
    public function place(int $userId, array $d, array $items, float $total): int
    {
        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare(
                'INSERT INTO orders (user_id, full_name, phone, city, address, notes, total)
                 VALUES (?, ?, ?, ?, ?, ?, ?)'
            );
            $stmt->execute([$userId, $d['full_name'], $d['phone'], $d['city'], $d['address'], $d['notes'], $total]);
            $orderId = (int) $this->db->lastInsertId();

            $orderItem = new OrderItem();
            $stock = $this->db->prepare(
                'UPDATE products SET stock = stock - ? WHERE id = ? AND stock >= ?'
            );
            foreach ($items as $item) {
                $stock->execute([$item['qty'], $item['id'], $item['qty']]);
                if ($stock->rowCount() === 0) {
                    throw new Exception('Not enough stock for ' . $item['name']);
                }
                $orderItem->add($orderId, $item);
            }

            $this->db->commit();
            return $orderId;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT o.*, u.name AS customer, u.email
             FROM orders o JOIN users u ON u.id = o.user_id
             WHERE o.id = ?'
        );
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function byUser(int $userId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM orders WHERE user_id = ? ORDER BY id DESC');
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function all(?string $status = null): array
    {
        $sql = 'SELECT o.*, u.name AS customer FROM orders o JOIN users u ON u.id = o.user_id';
        $params = [];
        if ($status) {
            $sql .= ' WHERE o.status = ?';
            $params[] = $status;
        }
        $sql .= ' ORDER BY o.id DESC';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function recent(int $limit): array
    {
        $limit = (int) $limit;
        return $this->db->query(
            "SELECT o.*, u.name AS customer FROM orders o JOIN users u ON u.id = o.user_id
             ORDER BY o.id DESC LIMIT $limit"
        )->fetchAll();
    }

    public function updateStatus(int $id, string $status, string $payment): void
    {
        $stmt = $this->db->prepare('UPDATE orders SET status = ?, payment_status = ? WHERE id = ?');
        $stmt->execute([$status, $payment, $id]);
    }

    public function count(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM orders')->fetchColumn();
    }

    public function revenue(): float
    {
        return (float) $this->db->query(
            "SELECT COALESCE(SUM(total), 0) FROM orders WHERE payment_status = 'Paid'"
        )->fetchColumn();
    }
}
