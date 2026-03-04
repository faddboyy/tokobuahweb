<?php

namespace App\Models;

use CodeIgniter\Model;

class InventoryModel extends Model
{
    protected $table = 'inventory';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'barang_id',
        'cabang_id',
        'stock',
    ];
}
