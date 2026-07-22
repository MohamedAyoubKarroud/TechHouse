<?php

class Product extends Model
{
    public function find($id)
    {
        return $this->fetchOne(
            'SELECT p.*, c.name AS category_name, c.slug AS category_slug
             FROM products p JOIN categories c ON c.id = p.category_id WHERE p.id = ?',
            array($id)
        );
    }

    public function findBySlug($slug)
    {
        return $this->fetchOne(
            'SELECT p.*, c.name AS category_name, c.slug AS category_slug
             FROM products p JOIN categories c ON c.id = p.category_id WHERE p.slug = ?',
            array($slug)
        );
    }

    public function all()
    {
        return $this->fetchAll('SELECT * FROM products ORDER BY created_at DESC');
    }

    public function newest($limit = 8)
    {
        return $this->fetchAll(
            'SELECT p.*, c.slug AS category_slug FROM products p
             JOIN categories c ON c.id = p.category_id
             ORDER BY p.is_new DESC, p.created_at DESC LIMIT ' . (int)$limit
        );
    }

    /**
     * Filter products. $filters keys:
     *   category_id, brand, color, min_price, max_price, is_new, sort
     */
    public function filter($filters)
    {
        $sql = 'SELECT p.*, c.slug AS category_slug FROM products p JOIN categories c ON c.id = p.category_id WHERE 1=1';
        $params = array();

        if (!empty($filters['category_id'])) {
            $sql .= ' AND p.category_id = ?';
            $params[] = (int)$filters['category_id'];
        }
        if (!empty($filters['brand'])) {
            $sql .= ' AND p.brand = ?';
            $params[] = $filters['brand'];
        }
        if (!empty($filters['color'])) {
            $sql .= ' AND p.color = ?';
            $params[] = $filters['color'];
        }
        if (isset($filters['min_price']) && $filters['min_price'] !== '') {
            $sql .= ' AND p.price >= ?';
            $params[] = (float)$filters['min_price'];
        }
        if (isset($filters['max_price']) && $filters['max_price'] !== '') {
            $sql .= ' AND p.price <= ?';
            $params[] = (float)$filters['max_price'];
        }
        if (!empty($filters['is_new'])) {
            $sql .= ' AND p.is_new = 1';
        }

        if (isset($filters['sort'])) {
            $sort = $filters['sort'];
        } else {
            $sort = 'newest';
        }
        switch ($sort) {
            case 'price_asc':
                $sql .= ' ORDER BY p.price ASC';
                break;
            case 'price_desc':
                $sql .= ' ORDER BY p.price DESC';
                break;
            case 'name':
                $sql .= ' ORDER BY p.name ASC';
                break;
            default:
                $sql .= ' ORDER BY p.created_at DESC';
                break;
        }

        return $this->fetchAll($sql, $params);
    }

    public function priceBounds($categoryId = null)
    {
        $sql = 'SELECT MIN(price) AS min_p, MAX(price) AS max_p FROM products';
        $params = array();
        if ($categoryId) {
            $sql .= ' WHERE category_id = ?';
            $params[] = $categoryId;
        }
        $row = $this->fetchOne($sql, $params);
        if (!$row) {
            $row = array();
        }
        $minVal = isset($row['min_p']) ? (float)$row['min_p'] : 0;
        $maxVal = isset($row['max_p']) ? (float)$row['max_p'] : 1000;
        $min = (int)floor($minVal);
        $max = (int)ceil($maxVal);
        if ($max <= $min) {
            $max = $min + 1;
        }
        return array('min' => $min, 'max' => $max);
    }

    public function distinctBrands($categoryId = null)
    {
        $sql = 'SELECT DISTINCT brand FROM products WHERE brand IS NOT NULL';
        $params = array();
        if ($categoryId) {
            $sql .= ' AND category_id = ?';
            $params[] = $categoryId;
        }
        $sql .= ' ORDER BY brand';
        $rows = $this->fetchAll($sql, $params);
        $out = array();
        foreach ($rows as $r) {
            $out[] = $r['brand'];
        }
        return $out;
    }

    public function distinctColors($categoryId = null)
    {
        $sql = 'SELECT DISTINCT color FROM products WHERE color IS NOT NULL';
        $params = array();
        if ($categoryId) {
            $sql .= ' AND category_id = ?';
            $params[] = $categoryId;
        }
        $sql .= ' ORDER BY color';
        $rows = $this->fetchAll($sql, $params);
        $out = array();
        foreach ($rows as $r) {
            $out[] = $r['color'];
        }
        return $out;
    }

    public function search($keyword, $filters = array())
    {
        $kw = '%' . $keyword . '%';
        $sql = 'SELECT p.*, c.slug AS category_slug FROM products p JOIN categories c ON c.id = p.category_id
                WHERE (p.name LIKE ? OR p.brand LIKE ? OR p.description LIKE ?)';
        $params = array($kw, $kw, $kw);

        if (!empty($filters['category_id'])) {
            $sql .= ' AND p.category_id = ?';
            $params[] = (int)$filters['category_id'];
        }
        if (!empty($filters['brand'])) {
            $sql .= ' AND p.brand = ?';
            $params[] = $filters['brand'];
        }
        if (!empty($filters['min_price']) && $filters['min_price'] !== '') {
            $sql .= ' AND p.price >= ?';
            $params[] = (float)$filters['min_price'];
        }
        if (!empty($filters['max_price']) && $filters['max_price'] !== '') {
            $sql .= ' AND p.price <= ?';
            $params[] = (float)$filters['max_price'];
        }

        $sql .= ' ORDER BY p.created_at DESC';
        return $this->fetchAll($sql, $params);
    }

    public function create($d)
    {
        if (!empty($d['slug'])) {
            $slug = $d['slug'];
        } else {
            $slug = $this->slugify($d['name']);
        }
        $this->exec(
            'INSERT INTO products (category_id, name, slug, brand, color, description, price, stock, image, is_new, ai_tags)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
            array(
                (int)$d['category_id'],
                $d['name'],
                $slug,
                isset($d['brand']) ? $d['brand'] : null,
                isset($d['color']) ? $d['color'] : null,
                isset($d['description']) ? $d['description'] : null,
                (float)$d['price'],
                (int)$d['stock'],
                isset($d['image']) ? $d['image'] : null,
                !empty($d['is_new']) ? 1 : 0,
                isset($d['ai_tags']) ? $d['ai_tags'] : null,
            )
        );
        return $this->lastInsertId();
    }

    public function update($id, $d)
    {
        $this->exec(
            'UPDATE products SET category_id=?, name=?, brand=?, color=?, description=?, price=?, stock=?, image=?, is_new=?, ai_tags=? WHERE id=?',
            array(
                (int)$d['category_id'],
                $d['name'],
                isset($d['brand']) ? $d['brand'] : null,
                isset($d['color']) ? $d['color'] : null,
                isset($d['description']) ? $d['description'] : null,
                (float)$d['price'],
                (int)$d['stock'],
                isset($d['image']) ? $d['image'] : null,
                !empty($d['is_new']) ? 1 : 0,
                isset($d['ai_tags']) ? $d['ai_tags'] : null,
                $id,
            )
        );
    }

    public function delete($id)
    {
        $this->exec('DELETE FROM products WHERE id = ?', array($id));
    }

    public function decrementStock($id, $qty)
    {
        $this->exec('UPDATE products SET stock = GREATEST(stock - ?, 0) WHERE id = ?', array($qty, $id));
    }

    public function count()
    {
        $row = $this->fetchOne('SELECT COUNT(*) c FROM products');
        if ($row && isset($row['c'])) {
            return (int)$row['c'];
        }
        return 0;
    }

    private function slugify($s)
    {
        $s = strtolower(trim($s));
        $s = preg_replace('/[^a-z0-9]+/', '-', $s);
        return trim($s, '-');
    }
}
