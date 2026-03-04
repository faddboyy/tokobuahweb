<?php

namespace App\Models;

use CodeIgniter\Model;

class StokGudangModel extends Model
{
    protected $table            = 'stok_gudang';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $useTimestamps    = false;

    protected $allowedFields = [
        'gudang_id',
        'barang_id',
        'satuan_id',
        'stock',
    ];
}