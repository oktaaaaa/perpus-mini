<?php

namespace App\Models;

use App\Core\Model;

class User extends Model
{
    protected string $table = 'users';

    public function members(): array
    {
        return $this->where('role', 'member', 'created_at DESC');
    }

    public function countMembers(): int
    {
        $stmt = $this->db->query("SELECT COUNT(*) FROM users WHERE role = 'member'");
        return (int) $stmt->fetchColumn();
    }
}
