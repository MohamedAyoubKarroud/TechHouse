<?php
// Base model. Wraps PDO and exposes prepared-statement helpers.

class Model
{
    protected $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    protected function query($sql, $params = array())
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    protected function fetchAll($sql, $params = array())
    {
        return $this->query($sql, $params)->fetchAll();
    }

    protected function fetchOne($sql, $params = array())
    {
        $row = $this->query($sql, $params)->fetch();
        if ($row) {
            return $row;
        }
        return null;
    }

    protected function exec($sql, $params = array())
    {
        return $this->query($sql, $params)->rowCount();
    }

    protected function lastInsertId()
    {
        return (int)$this->db->lastInsertId();
    }
}
