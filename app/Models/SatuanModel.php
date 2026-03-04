<?php

namespace App\Models;

use CodeIgniter\Model;

class SatuanModel extends Model
{
    protected $table = 'satuan';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'nama',
    ];
}
