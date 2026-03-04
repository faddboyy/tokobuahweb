<?php

namespace App\Models;

use CodeIgniter\Model;

class PenerimaanGudangModel extends Model
{
    protected $table            = 'penerimaan_gudang';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = '';

    protected $allowedFields = [
        'kode_penerimaan',
        'kode_supplier',
        'surat_jalan_id',
        'gudang_id',
        'waktu_penerimaan',
        'status',
        'operator_id',
    ];
}