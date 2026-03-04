<?php

namespace App\Models;

use CodeIgniter\Model;

class PembayaranModel extends Model
{
    protected $table = 'pembayaran';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'jenis_pembayaran',
        'diskon_persen',
        'diskon_nominal',
        'nominal_bayar',
        'kembalian',
    ];
}
