<?php

class Category extends Model
{
    public function all()
    {
        return $this->fetchAll('SELECT * FROM categories ORDER BY name');
    }

    public function findBySlug($slug)
    {
        return $this->fetchOne('SELECT * FROM categories WHERE slug = ?', array($slug));
    }

    public function find($id)
    {
        return $this->fetchOne('SELECT * FROM categories WHERE id = ?', array($id));
    }
}
