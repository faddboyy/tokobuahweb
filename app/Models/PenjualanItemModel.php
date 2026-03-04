<?php

namespace App\Models;

use CodeIgniter\Model;

class PenjualanItemModel extends Model
{
    protected $table = 'penjualan_item';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'penjualan_id',
        'inventory_id',
        'harga_satuan',
        'qty',
        'subtotal',
        'keterangan',
    ];
}
