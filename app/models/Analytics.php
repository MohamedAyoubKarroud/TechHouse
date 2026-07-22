<?php

class Analytics extends Model
{
    public function recordProductView($productId, $userId)
    {
        $this->exec('INSERT INTO analytics_product_views (product_id, user_id) VALUES (?, ?)', array($productId, $userId));
    }

    public function totalVisits()
    {
        $r = $this->fetchOne('SELECT COUNT(*) c FROM analytics_visits');
        if ($r && isset($r['c'])) {
            return (int)$r['c'];
        }
        return 0;
    }

    public function visitsLast7Days()
    {
        return $this->fetchAll(
            'SELECT DATE(visited_at) d, COUNT(*) c FROM analytics_visits
             WHERE visited_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
             GROUP BY DATE(visited_at) ORDER BY d'
        );
    }

    public function topPages($limit = 5)
    {
        return $this->fetchAll(
            'SELECT page, COUNT(*) c FROM analytics_visits GROUP BY page ORDER BY c DESC LIMIT ' . (int)$limit
        );
    }

    public function topProducts($limit = 5)
    {
        return $this->fetchAll(
            'SELECT p.id, p.name, COUNT(v.id) views
             FROM analytics_product_views v JOIN products p ON p.id = v.product_id
             GROUP BY p.id, p.name ORDER BY views DESC LIMIT ' . (int)$limit
        );
    }

    public function visitorsByCountry($limit = 10)
    {
        return $this->fetchAll(
            'SELECT COALESCE(country, "Unknown") country, COUNT(*) c
             FROM analytics_visits GROUP BY country ORDER BY c DESC LIMIT ' . (int)$limit
        );
    }

    public function uniqueVisitors()
    {
        $r = $this->fetchOne('SELECT COUNT(DISTINCT ip) c FROM analytics_visits');
        if ($r && isset($r['c'])) {
            return (int)$r['c'];
        }
        return 0;
    }
}
