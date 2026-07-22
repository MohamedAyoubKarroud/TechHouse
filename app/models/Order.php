<?php

class Order extends Model
{
    public function create($userId, $items, $shippingAddr, $totalOverride = null)
    {
        if ($totalOverride !== null) {
            $total = $totalOverride;
        } else {
            $total = 0.0;
            foreach ($items as $it) {
                $total += $it['price'] * $it['quantity'];
            }
        }

        $this->db->beginTransaction();
        try {
            $tracking = strtoupper(bin2hex(random_bytes(5)));
            $this->exec(
                'INSERT INTO orders (user_id, status, total, shipping_addr, tracking_code) VALUES (?, "pending", ?, ?, ?)',
                array($userId, $total, $shippingAddr, $tracking)
            );
            $orderId = $this->lastInsertId();

            $stmt = $this->db->prepare(
                'INSERT INTO order_items (order_id, product_id, name, unit_price, quantity) VALUES (?, ?, ?, ?, ?)'
            );
            foreach ($items as $it) {
                $stmt->execute(array($orderId, $it['id'], $it['name'], $it['price'], $it['quantity']));
            }

            $this->db->commit();
            return $orderId;
        } catch (PDOException $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function forUser($userId)
    {
        return $this->fetchAll('SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC', array($userId));
    }

    public function find($id)
    {
        return $this->fetchOne('SELECT * FROM orders WHERE id = ?', array($id));
    }

    public function findByTracking($code)
    {
        return $this->fetchOne('SELECT * FROM orders WHERE tracking_code = ?', array($code));
    }

    public function items($orderId)
    {
        return $this->fetchAll('SELECT * FROM order_items WHERE order_id = ?', array($orderId));
    }

    public function all()
    {
        return $this->fetchAll(
            'SELECT o.*, u.name AS user_name, u.email AS user_email
             FROM orders o JOIN users u ON u.id = o.user_id
             ORDER BY o.created_at DESC'
        );
    }

    public function updateStatus($id, $status)
    {
        $this->exec('UPDATE orders SET status = ? WHERE id = ?', array($status, $id));
    }

    public function revenue()
    {
        $r = $this->fetchOne('SELECT COALESCE(SUM(total),0) t FROM orders WHERE status <> "cancelled"');
        if ($r && isset($r['t'])) {
            return (float)$r['t'];
        }
        return 0.0;
    }

    public function count()
    {
        $r = $this->fetchOne('SELECT COUNT(*) c FROM orders');
        if ($r && isset($r['c'])) {
            return (int)$r['c'];
        }
        return 0;
    }
}
