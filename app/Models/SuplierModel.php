<?php

namespace App\Models;

use CodeIgniter\Model;

class SuplierModel extends Model
{
    protected $table = 'suplier';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'nama',
        'alamat',
        'telepon',
        'email'
    ];
}
