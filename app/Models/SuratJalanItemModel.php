<?php

namespace App\Models;

use CodeIgniter\Model;

class SuratJalanItemModel extends Model
{
    protected $table      = 'surat_jalan_item';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'surat_jalan_id',
        'barang_id',
        'qty',
        'satuan_id',
        'harga_beli'
    ];

    protected $useTimestamps = false;

    protected $validationRules = [
        'surat_jalan_id' => 'required|integer',
        'barang_id'      => 'required|integer',
        'qty'            => 'required|decimal'
    ];

    /*
    |--------------------------------------------------------------------------
    | Ambil Item by Surat Jalan
    |--------------------------------------------------------------------------
    */

    public function getBySuratJalan($suratJalanId)
    {
        return $this->db->table('surat_jalan_item sji')
            ->select('
                sji.*,
                b.nama as barang_nama
            ')
            ->join('barang b', 'b.id = sji.barang_id', 'left')
            ->where('sji.surat_jalan_id', $suratJalanId)
            ->get()
            ->getResultArray();
    }

    /*
    |--------------------------------------------------------------------------
    | Hapus Semua Item by Surat Jalan
    |--------------------------------------------------------------------------
    */

    public function deleteBySuratJalan($suratJalanId)
    {
        return $this->where('surat_jalan_id', $suratJalanId)->delete();
    }
}