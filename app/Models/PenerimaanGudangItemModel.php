<?php

namespace App\Models;

use CodeIgniter\Model;

class PenerimaanGudangItemModel extends Model
{
    protected $table            = 'penerimaan_gudang_item';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $useTimestamps    = false;

    protected $allowedFields = [
        'penerimaan_gudang_id',
        'barang_id',
        'qty_dipesan',
        'qty_diterima'
    ];
}