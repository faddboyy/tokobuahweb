<?php

namespace App\Models;

use CodeIgniter\Model;

class PenjualanModel extends Model
{
    protected $table = 'penjualan';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'faktur',
        'customer_id',
        'pembayaran_id',
        'nominal_penjualan',
        'operator_id',
        'cabang_id'
    ];
}
