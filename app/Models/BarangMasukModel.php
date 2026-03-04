<?php

namespace App\Models;

use CodeIgniter\Model;

class BarangMasukModel extends Model
{
    protected $table          = 'barang_masuk';
    protected $primaryKey     = 'id';
    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'kode_masuk',
        'penerimaan_gudang_id',  // FK → penerimaan_gudang (bukan surat_jalan)
        'waktu_masuk',
        'gudang_id',             // diambil dari penerimaan_gudang.gudang_id
        'cabang_id',             // diambil dari session
        'total_nominal',
        'operator_id',
    ];

    protected $useTimestamps = false;
}
