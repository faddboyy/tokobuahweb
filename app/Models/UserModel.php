<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'username',
        'nama',
        'password',
        'cabang_id',
        'is_active',
        'role',
    ];
}
