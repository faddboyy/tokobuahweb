<?php

namespace App\Models;

use CodeIgniter\Model;

class GudangUtamaModel extends Model
{
    protected $table      = 'gudang_utama';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'nama',
        'mandor_id'
    ];

    protected $useTimestamps = false;
}