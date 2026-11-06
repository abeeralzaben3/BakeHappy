<?php
class OrderItem extends Model
{
    public function add(int $orderId, array $item): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO order_items (order_id, product_id, product_name, price, quantity)
             VALUES (?, ?, ?, ?, ?)'
        );
        $stmt->execute([$orderId, $item['id'], $item['name'], $item['price'], $item['qty']]);
    }

    public function byOrder(int $orderId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM order_items WHERE order_id = ?');
        $stmt->execute([$orderId]);
        return $stmt->fetchAll();
    }
}
