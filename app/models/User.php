<?php

class User extends Model
{
    public function findByEmail($email)
    {
        return $this->fetchOne('SELECT * FROM users WHERE email = ?', array($email));
    }

    public function find($id)
    {
        return $this->fetchOne('SELECT id, name, email, role, address, city, country, created_at FROM users WHERE id = ?', array($id));
    }

    public function all()
    {
        return $this->fetchAll('SELECT id, name, email, role, created_at FROM users ORDER BY created_at DESC');
    }

    public function create($name, $email, $password, $role = 'client')
    {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $this->exec(
            'INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, ?)',
            array($name, $email, $hash, $role)
        );
        return $this->lastInsertId();
    }

    public function verifyPassword($user, $password)
    {
        $hash = '';
        if (isset($user['password_hash'])) {
            $hash = $user['password_hash'];
        }
        return password_verify($password, $hash);
    }

    public function findByProvider($provider, $providerId)
    {
        return $this->fetchOne(
            'SELECT * FROM users WHERE provider = ? AND provider_id = ?',
            array($provider, $providerId)
        );
    }

    public function createOAuth($name, $email, $provider, $providerId, $avatarUrl = null)
    {
        $this->exec(
            'INSERT INTO users (name, email, provider, provider_id, avatar_url, role)
             VALUES (?, ?, ?, ?, ?, ?)',
            array($name, $email, $provider, $providerId, $avatarUrl, 'client')
        );
        return $this->lastInsertId();
    }

    public function linkProvider($id, $provider, $providerId, $avatarUrl = null)
    {
        $this->exec(
            'UPDATE users SET provider = ?, provider_id = ?, avatar_url = COALESCE(?, avatar_url) WHERE id = ?',
            array($provider, $providerId, $avatarUrl, $id)
        );
    }

    public function updateRole($id, $role)
    {
        $this->exec('UPDATE users SET role = ? WHERE id = ?', array($role, $id));
    }

    public function delete($id)
    {
        $this->exec('DELETE FROM users WHERE id = ?', array($id));
    }

    public function count()
    {
        $r = $this->fetchOne('SELECT COUNT(*) c FROM users');
        if ($r && isset($r['c'])) {
            return (int)$r['c'];
        }
        return 0;
    }
}
