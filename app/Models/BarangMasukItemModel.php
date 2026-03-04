<?php

namespace App\Models;

use CodeIgniter\Model;

class BarangMasukItemModel extends Model
{
    protected $table          = 'barang_masuk_item';
    protected $primaryKey     = 'id';
    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'barang_masuk_id',
        'barang_id',
        'qty_kiriman',    // qty yang dikirim gudang (snapshot dari penerimaan_gudang_item.qty_diterima)
        'qty_aktual',     // qty fisik aktual yang diterima petugas toko (input manual)
        'selisih',        // qty_aktual - qty_kiriman, dihitung otomatis saat update
        'satuan_kirim',   // satuan pengiriman, dari penerimaan_gudang_item → JOIN satuan
        'satuan_simpan',  // satuan penyimpanan, dari barang → JOIN satuan
        'aktual_nominal', // qty_aktual × barang.harga_pokok, dihitung otomatis saat update
    ];

    protected $useTimestamps = false;
}