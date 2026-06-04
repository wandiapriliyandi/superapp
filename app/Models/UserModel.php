<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['username', 'password', 'nama_lengkap', 'role_id'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getUserWithRole($username)
    {
        return $this->select('users.*, roles.nama_role, roles.permissions')
            ->join('roles', 'roles.id = users.role_id', 'left')
            ->where('users.username', $username)
            ->first();
    }
}
