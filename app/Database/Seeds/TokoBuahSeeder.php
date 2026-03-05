<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class TokoBuahSeeder extends Seeder
{
    public function run(): void
    {
        $this->db->query('SET FOREIGN_KEY_CHECKS = 0');

        // -------------------------------------------------------
        // cabang
        // -------------------------------------------------------
        $this->db->table('cabang')->insertBatch([
            ['id' => 10, 'nama' => 'Toko Satu'],
            ['id' => 11, 'nama' => 'Toko Dua'],
        ]);

        // -------------------------------------------------------
        // jenis
        // -------------------------------------------------------
        $this->db->table('jenis')->insertBatch([
            ['id' => 20, 'nama' => 'Semangka'],
            ['id' => 21, 'nama' => 'Jeruk'],
            ['id' => 22, 'nama' => 'Salak'],
        ]);

        // -------------------------------------------------------
        // satuan
        // -------------------------------------------------------
        $this->db->table('satuan')->insertBatch([
            ['id' => 14, 'nama' => 'Kg'],
            ['id' => 15, 'nama' => 'Kwintal'],
            ['id' => 16, 'nama' => 'Dus'],
        ]);

        // -------------------------------------------------------
        // users
        // -------------------------------------------------------
        $this->db->table('users')->insertBatch([
            ['id' => 1,  'username' => 'owner', 'nama' => 'Pak Bos',              'password' => '$2y$12$RUAubh5i/CDNzS4S2SseSOKJKe/NlmwGQJzFIv8sTAdoaBYMF2wvW', 'cabang_id' => null, 'is_active' => 1, 'role' => 'owner'],
            ['id' => 2,  'username' => 'admin', 'nama' => 'Admin',                'password' => '$2y$12$LMb6P2McsrqnrlRr1J8GWuLQ9GGkbEwct2uO9ss8tLwaJrpbdJDzW', 'cabang_id' => null, 'is_active' => 1, 'role' => 'admin'],
            ['id' => 17, 'username' => 'p0101', 'nama' => 'Petugas Toko Satu (1)', 'password' => '$2y$12$e05pw.0XppmUJV66QP2AzeOZmD58UfhiKXXJQLRTkORjTzDopnTQO', 'cabang_id' => 10,   'is_active' => 1, 'role' => 'petugas'],
            ['id' => 18, 'username' => 'p0201', 'nama' => 'Petugas Toko Dua (1)',  'password' => '$2y$12$132.h4TdSWyeaUKbnFoXF.gUAIOz4nMpYpXFRmZTe18/7Dn/cUSlO', 'cabang_id' => 11,   'is_active' => 1, 'role' => 'petugas'],
            ['id' => 19, 'username' => 'p0102', 'nama' => 'Petugas Toko Satu (2)', 'password' => '$2y$12$ybQU0hxnxwR1/b46p80jVeFcuC3AkvZdvXqU5jiPQRv77NquTtfDm', 'cabang_id' => 10,   'is_active' => 1, 'role' => 'petugas'],
            ['id' => 20, 'username' => 'p0202', 'nama' => 'Petugas Toko Dua (2)',  'password' => '$2y$12$NhHA4UX.sZCPKjYU0xSe0.1drOpK8JUF.sc.rPxzHPdK0sIvzd0gq', 'cabang_id' => 11,   'is_active' => 1, 'role' => 'petugas'],
        ]);

        // -------------------------------------------------------
        // suplier
        // -------------------------------------------------------
        $this->db->table('suplier')->insert([
            'id'      => 6,
            'nama'    => 'PT Sumber Buah Nusantara',
            'alamat'  => "Jl Jend Achmad Yani, Cimahi, Jawa Barat\n",
            'telepon' => '082276543210',
            'email'   => 'marketing@sumberbuah.co.id',
        ]);

        // -------------------------------------------------------
        // gudang_utama
        // -------------------------------------------------------
        $this->db->table('gudang_utama')->insert([
            'id'        => 3,
            'nama'      => 'Gudang Utama',
            'mandor_id' => 2,
        ]);

        // -------------------------------------------------------
        // barang
        // -------------------------------------------------------
        $this->db->table('barang')->insertBatch([
            ['id' => 182, 'barcode' => '1010101010', 'nama' => 'Semangka Merah',    'jenis_id' => 20, 'satuan_id' => 14, 'harga_pokok' => 24000.00, 'harga_jual' => 28000.00],
            ['id' => 183, 'barcode' => '2020202020', 'nama' => 'Semangka Kuning',   'jenis_id' => 20, 'satuan_id' => 14, 'harga_pokok' => 32000.00, 'harga_jual' => 37000.00],
            ['id' => 184, 'barcode' => '3030303030', 'nama' => 'Salak Madu ',       'jenis_id' => 22, 'satuan_id' => 14, 'harga_pokok' => 10000.00, 'harga_jual' => 16000.00],
            ['id' => 185, 'barcode' => '4040404040', 'nama' => 'Jeruk Pontianak',   'jenis_id' => 21, 'satuan_id' => 14, 'harga_pokok' =>  7500.00, 'harga_jual' => 11500.00],
        ]);

        // -------------------------------------------------------
        // customer
        // -------------------------------------------------------
        $this->db->table('customer')->insert([
            'id'        => 10,
            'nama'      => 'Tn Van Den Wijk',
            'alamat'    => 'Pantai Indah Kapuk Golf Island Blok Emerald No. 12, Jakarta Utara',
            'telepon'   => '081234567890',
            'cabang_id' => null,
            'added_by'  => 1,
        ]);

        // -------------------------------------------------------
        // diskon_terbatas
        // -------------------------------------------------------
        $this->db->table('diskon_terbatas')->insert([
            'id'          => 2,
            'nama'        => 'Promo Ramadhan 2026',
            'cabang_id'   => 10,
            'tgl_mulai'   => '2026-03-02',
            'tgl_selesai' => '2026-03-09',
            'status'      => 'aktif',
            'created_by'  => 1,
            'created_at'  => '2026-03-03 07:17:37',
        ]);

        // -------------------------------------------------------
        // diskon_terbatas_item
        // -------------------------------------------------------
        $this->db->table('diskon_terbatas_item')->insert([
            'id'                 => 6,
            'diskon_terbatas_id' => 2,
            'barang_id'          => 182,
            'nominal_diskon'     => 2500.00,
        ]);

        // -------------------------------------------------------
        // inventory
        // -------------------------------------------------------
        $this->db->table('inventory')->insertBatch([
            ['id' => 91, 'barang_id' => 185, 'cabang_id' => 10, 'stock' =>   494.00],
            ['id' => 92, 'barang_id' => 182, 'cabang_id' => 10, 'stock' =>   423.00],
            ['id' => 93, 'barang_id' => 183, 'cabang_id' => 10, 'stock' =>   171.00],
            ['id' => 94, 'barang_id' => 184, 'cabang_id' => 10, 'stock' =>   279.50],
            ['id' => 95, 'barang_id' => 182, 'cabang_id' => 11, 'stock' =>     0.00],
            ['id' => 96, 'barang_id' => 183, 'cabang_id' => 11, 'stock' =>     0.00],
            ['id' => 97, 'barang_id' => 185, 'cabang_id' => 11, 'stock' =>     0.00],
            ['id' => 98, 'barang_id' => 184, 'cabang_id' => 11, 'stock' =>     0.00],
        ]);

        // -------------------------------------------------------
        // pembayaran
        // -------------------------------------------------------
        $this->db->table('pembayaran')->insertBatch([
            ['id' => 30, 'jenis_pembayaran' => 'tunai',    'diskon_persen' => null, 'diskon_nominal' =>     0.00, 'nominal_bayar' => 1600000.00, 'kembalian' =>  79000.00],
            ['id' => 31, 'jenis_pembayaran' => 'qris',     'diskon_persen' => null, 'diskon_nominal' =>     0.00, 'nominal_bayar' => 1204000.00, 'kembalian' =>      0.00],
            ['id' => 32, 'jenis_pembayaran' => 'qris',     'diskon_persen' => null, 'diskon_nominal' =>     0.00, 'nominal_bayar' =>  616000.00, 'kembalian' =>      0.00],
            ['id' => 33, 'jenis_pembayaran' => 'qris',     'diskon_persen' => null, 'diskon_nominal' =>     0.00, 'nominal_bayar' =>  588000.00, 'kembalian' =>      0.00],
            ['id' => 34, 'jenis_pembayaran' => 'qris',     'diskon_persen' => null, 'diskon_nominal' =>     0.00, 'nominal_bayar' =>  160000.00, 'kembalian' =>      0.00],
            ['id' => 35, 'jenis_pembayaran' => 'tunai',    'diskon_persen' => null, 'diskon_nominal' =>     0.00, 'nominal_bayar' =>  161000.00, 'kembalian' =>    500.00],
            ['id' => 36, 'jenis_pembayaran' => 'qris',     'diskon_persen' => null, 'diskon_nominal' =>     0.00, 'nominal_bayar' =>  370000.00, 'kembalian' =>      0.00],
            ['id' => 37, 'jenis_pembayaran' => 'transfer', 'diskon_persen' => null, 'diskon_nominal' => 10000.00, 'nominal_bayar' =>  450000.00, 'kembalian' =>  16000.00],
            ['id' => 38, 'jenis_pembayaran' => 'transfer', 'diskon_persen' => null, 'diskon_nominal' => 99000.00, 'nominal_bayar' => 1230000.00, 'kembalian' => 100000.00],
            ['id' => 39, 'jenis_pembayaran' => 'qris',     'diskon_persen' => null, 'diskon_nominal' =>     0.00, 'nominal_bayar' =>  140000.00, 'kembalian' =>      0.00],
            ['id' => 40, 'jenis_pembayaran' => 'qris',     'diskon_persen' => null, 'diskon_nominal' =>     0.00, 'nominal_bayar' =>   56000.00, 'kembalian' =>      0.00],
            ['id' => 41, 'jenis_pembayaran' => 'qris',     'diskon_persen' => null, 'diskon_nominal' =>     0.00, 'nominal_bayar' =>   28000.00, 'kembalian' =>      0.00],
            ['id' => 42, 'jenis_pembayaran' => 'qris',     'diskon_persen' => null, 'diskon_nominal' =>     0.00, 'nominal_bayar' =>   28000.00, 'kembalian' =>      0.00],
            ['id' => 43, 'jenis_pembayaran' => 'qris',     'diskon_persen' => null, 'diskon_nominal' => 10000.00, 'nominal_bayar' =>  316500.00, 'kembalian' =>  10000.00],
            ['id' => 44, 'jenis_pembayaran' => 'qris',     'diskon_persen' => null, 'diskon_nominal' =>     0.00, 'nominal_bayar' =>   46000.00, 'kembalian' =>      0.00],
        ]);

        // -------------------------------------------------------
        // surat_jalan
        // -------------------------------------------------------
        $this->db->table('surat_jalan')->insertBatch([
            ['id' => 11, 'kode_po' => 'PO-20260227011855', 'suplier_id' => 6, 'gudang_id' => 3, 'waktu_po' => '2026-02-26 18:16:55', 'status' => 'selesai',     'total_nominal' => 1760000.00, 'operator_id' => 2],
            ['id' => 12, 'kode_po' => 'PO-20260227014053', 'suplier_id' => 6, 'gudang_id' => 3, 'waktu_po' => '2026-02-26 18:40:53', 'status' => 'dibatalkan',  'total_nominal' =>       0.00, 'operator_id' => 2],
            ['id' => 13, 'kode_po' => 'PO-20260227023054', 'suplier_id' => 6, 'gudang_id' => 3, 'waktu_po' => '2026-02-26 19:09:37', 'status' => 'selesai',     'total_nominal' =>  800000.00, 'operator_id' => 2],
            ['id' => 14, 'kode_po' => 'PO-20260227034515', 'suplier_id' => 6, 'gudang_id' => 3, 'waktu_po' => '2026-02-26 20:44:50', 'status' => 'selesai',     'total_nominal' =>  200000.00, 'operator_id' => 2],
            ['id' => 15, 'kode_po' => 'PO-20260227223747', 'suplier_id' => 6, 'gudang_id' => 3, 'waktu_po' => '2026-02-27 15:37:10', 'status' => 'selesai',     'total_nominal' =>  840000.00, 'operator_id' => 2],
            ['id' => 16, 'kode_po' => 'PO-20260227230023', 'suplier_id' => 6, 'gudang_id' => 3, 'waktu_po' => '2026-02-27 15:45:26', 'status' => 'selesai',     'total_nominal' =>  600000.00, 'operator_id' => 2],
            ['id' => 17, 'kode_po' => 'PO-20260227233233', 'suplier_id' => 6, 'gudang_id' => 3, 'waktu_po' => '2026-02-27 16:31:50', 'status' => 'selesai',     'total_nominal' =>  150000.00, 'operator_id' => 2],
            ['id' => 18, 'kode_po' => 'PO-20260302144050', 'suplier_id' => 6, 'gudang_id' => 3, 'waktu_po' => '2026-03-02 07:40:20', 'status' => 'selesai',     'total_nominal' => 2000000.00, 'operator_id' => 2],
            ['id' => 19, 'kode_po' => 'PO-20260302144318', 'suplier_id' => 6, 'gudang_id' => 3, 'waktu_po' => '2026-03-02 07:42:58', 'status' => 'selesai',     'total_nominal' =>  500000.00, 'operator_id' => 2],
            ['id' => 20, 'kode_po' => 'PO-20260303020503', 'suplier_id' => 6, 'gudang_id' => 3, 'waktu_po' => '2026-03-02 19:03:53', 'status' => 'order',       'total_nominal' =>   98000.00, 'operator_id' => 2],
        ]);

        // -------------------------------------------------------
        // surat_jalan_item
        // -------------------------------------------------------
        $this->db->table('surat_jalan_item')->insertBatch([
            ['id' => 20, 'surat_jalan_id' => 11, 'barang_id' => 182, 'satuan_id' => 16, 'harga_beli' =>   480000.00, 'qty' => 10],
            ['id' => 21, 'surat_jalan_id' => 11, 'barang_id' => 183, 'satuan_id' => 16, 'harga_beli' =>  1280000.00, 'qty' => 20],
            ['id' => 23, 'surat_jalan_id' => 13, 'barang_id' => 185, 'satuan_id' => 16, 'harga_beli' =>   300000.00, 'qty' => 40],
            ['id' => 24, 'surat_jalan_id' => 13, 'barang_id' => 184, 'satuan_id' => 16, 'harga_beli' =>   500000.00, 'qty' => 50],
            ['id' => 26, 'surat_jalan_id' => 14, 'barang_id' => 184, 'satuan_id' => 16, 'harga_beli' =>   200000.00, 'qty' => 10],
            ['id' => 27, 'surat_jalan_id' => 15, 'barang_id' => 182, 'satuan_id' => 16, 'harga_beli' =>   840000.00, 'qty' => 20],
            ['id' => 28, 'surat_jalan_id' => 16, 'barang_id' => 184, 'satuan_id' => 16, 'harga_beli' =>   600000.00, 'qty' => 30],
            ['id' => 29, 'surat_jalan_id' => 17, 'barang_id' => 185, 'satuan_id' => 16, 'harga_beli' =>   150000.00, 'qty' => 10],
            ['id' => 30, 'surat_jalan_id' => 18, 'barang_id' => 182, 'satuan_id' => 16, 'harga_beli' =>  2000000.00, 'qty' => 20],
            ['id' => 31, 'surat_jalan_id' => 19, 'barang_id' => 182, 'satuan_id' => 16, 'harga_beli' =>   500000.00, 'qty' =>  5],
            ['id' => 32, 'surat_jalan_id' => 20, 'barang_id' => 184, 'satuan_id' => 16, 'harga_beli' =>    20000.00, 'qty' =>  1],
            ['id' => 33, 'surat_jalan_id' => 20, 'barang_id' => 185, 'satuan_id' => 16, 'harga_beli' =>    30000.00, 'qty' =>  2],
            ['id' => 34, 'surat_jalan_id' => 20, 'barang_id' => 182, 'satuan_id' => 16, 'harga_beli' =>    48000.00, 'qty' =>  1],
        ]);

        // -------------------------------------------------------
        // penerimaan_gudang
        // -------------------------------------------------------
        $this->db->table('penerimaan_gudang')->insertBatch([
            ['id' => 1, 'kode_penerimaan' => 'PG-20260227020833', 'surat_jalan_id' => 11, 'kode_supplier' => 'SJ-2022270111298',   'gudang_id' => 3, 'waktu_penerimaan' => '2026-02-26 19:08:33', 'operator_id' => 2, 'status' => 'digudang', 'created_at' => '2026-02-26 19:08:33'],
            ['id' => 2, 'kode_penerimaan' => 'PG-20260227032155', 'surat_jalan_id' => 13, 'kode_supplier' => 'SJ-1212130099',      'gudang_id' => 3, 'waktu_penerimaan' => '2026-02-26 20:21:55', 'operator_id' => 2, 'status' => 'digudang', 'created_at' => '2026-02-26 20:21:55'],
            ['id' => 3, 'kode_penerimaan' => 'PG-20260227035510', 'surat_jalan_id' => 14, 'kode_supplier' => 'SJ-12818318318031', 'gudang_id' => 3, 'waktu_penerimaan' => '2026-02-26 20:55:10', 'operator_id' => 2, 'status' => 'digudang', 'created_at' => '2026-02-26 20:55:10'],
            ['id' => 4, 'kode_penerimaan' => 'PG-20260227231459', 'surat_jalan_id' => 15, 'kode_supplier' => 'SJ-10920910229',    'gudang_id' => 3, 'waktu_penerimaan' => '2026-02-27 16:14:59', 'operator_id' => 2, 'status' => 'digudang', 'created_at' => '2026-02-27 16:14:59'],
            ['id' => 5, 'kode_penerimaan' => 'PG-20260227232806', 'surat_jalan_id' => 16, 'kode_supplier' => 'SJ-989829812109',   'gudang_id' => 3, 'waktu_penerimaan' => '2026-02-27 16:28:06', 'operator_id' => 2, 'status' => 'digudang', 'created_at' => '2026-02-27 16:28:06'],
            ['id' => 6, 'kode_penerimaan' => 'PG-20260227233517', 'surat_jalan_id' => 17, 'kode_supplier' => 'SJ-12019021209',    'gudang_id' => 3, 'waktu_penerimaan' => '2026-02-27 16:35:17', 'operator_id' => 2, 'status' => 'digudang', 'created_at' => '2026-02-27 16:35:17'],
            ['id' => 7, 'kode_penerimaan' => 'PG-20260302144208', 'surat_jalan_id' => 18, 'kode_supplier' => 'Sj-61',             'gudang_id' => 3, 'waktu_penerimaan' => '2026-03-02 07:42:08', 'operator_id' => 2, 'status' => 'digudang', 'created_at' => '2026-03-02 07:42:08'],
            ['id' => 8, 'kode_penerimaan' => 'PG-20260302144405', 'surat_jalan_id' => 19, 'kode_supplier' => 'Sj-004',            'gudang_id' => 3, 'waktu_penerimaan' => '2026-03-02 07:44:05', 'operator_id' => 2, 'status' => 'digudang', 'created_at' => '2026-03-02 07:44:05'],
        ]);

        // -------------------------------------------------------
        // penerimaan_gudang_item
        // -------------------------------------------------------
        $this->db->table('penerimaan_gudang_item')->insertBatch([
            ['id' =>  1, 'penerimaan_gudang_id' => 1, 'barang_id' => 183, 'qty_dipesan' => 20, 'qty_diterima' => 18],
            ['id' =>  2, 'penerimaan_gudang_id' => 1, 'barang_id' => 182, 'qty_dipesan' => 10, 'qty_diterima' =>  9],
            ['id' =>  3, 'penerimaan_gudang_id' => 2, 'barang_id' => 184, 'qty_dipesan' => 50, 'qty_diterima' => 48],
            ['id' =>  4, 'penerimaan_gudang_id' => 2, 'barang_id' => 185, 'qty_dipesan' => 40, 'qty_diterima' => 39],
            ['id' =>  5, 'penerimaan_gudang_id' => 3, 'barang_id' => 184, 'qty_dipesan' => 10, 'qty_diterima' => 10],
            ['id' =>  6, 'penerimaan_gudang_id' => 4, 'barang_id' => 182, 'qty_dipesan' => 20, 'qty_diterima' => 19],
            ['id' =>  7, 'penerimaan_gudang_id' => 5, 'barang_id' => 184, 'qty_dipesan' => 30, 'qty_diterima' => 29],
            ['id' =>  8, 'penerimaan_gudang_id' => 6, 'barang_id' => 185, 'qty_dipesan' => 10, 'qty_diterima' =>  9],
            ['id' =>  9, 'penerimaan_gudang_id' => 7, 'barang_id' => 182, 'qty_dipesan' => 20, 'qty_diterima' => 15],
            ['id' => 10, 'penerimaan_gudang_id' => 8, 'barang_id' => 182, 'qty_dipesan' =>  5, 'qty_diterima' =>  2],
        ]);

        // -------------------------------------------------------
        // pengiriman_gudang
        // -------------------------------------------------------
        $this->db->table('pengiriman_gudang')->insertBatch([
            ['id' => 1, 'kode_pengiriman' => 'KRG-20260301234345', 'gudang_id' => 3, 'cabang_id' => 10, 'operator_id' => 2, 'waktu_pengiriman' => '2026-03-01 16:43:45', 'status' => 'diterima', 'created_at' => '2026-03-01 16:43:45'],
            ['id' => 2, 'kode_pengiriman' => 'KRG-20260302000503', 'gudang_id' => 3, 'cabang_id' => 11, 'operator_id' => 2, 'waktu_pengiriman' => '2026-03-01 17:05:03', 'status' => 'dikirim',  'created_at' => '2026-03-01 17:05:03'],
            ['id' => 3, 'kode_pengiriman' => 'KRG-20260302144818', 'gudang_id' => 3, 'cabang_id' => 10, 'operator_id' => 2, 'waktu_pengiriman' => '2026-03-02 07:48:18', 'status' => 'diterima', 'created_at' => '2026-03-02 07:48:18'],
        ]);

        // -------------------------------------------------------
        // pengiriman_gudang_item
        // -------------------------------------------------------
        $this->db->table('pengiriman_gudang_item')->insertBatch([
            ['id' => 1, 'pengiriman_gudang_id' => 1, 'barang_id' => 182, 'qty' =>  8.00],
            ['id' => 2, 'pengiriman_gudang_id' => 1, 'barang_id' => 183, 'qty' =>  5.00],
            ['id' => 3, 'pengiriman_gudang_id' => 2, 'barang_id' => 184, 'qty' => 20.00],
            ['id' => 4, 'pengiriman_gudang_id' => 2, 'barang_id' => 185, 'qty' => 18.00],
            ['id' => 5, 'pengiriman_gudang_id' => 3, 'barang_id' => 182, 'qty' => 10.00],
        ]);

        // -------------------------------------------------------
        // barang_masuk
        // -------------------------------------------------------
        $this->db->table('barang_masuk')->insertBatch([
            ['id' => 11, 'kode_masuk' => 'BM-20260302004050', 'pengiriman_gudang_id' => 1, 'waktu_masuk' => '2026-03-01 17:40:50', 'operator_id' => 17, 'cabang_id' => 10],
            ['id' => 12, 'kode_masuk' => 'BM-20260302145031', 'pengiriman_gudang_id' => 3, 'waktu_masuk' => '2026-03-02 07:50:31', 'operator_id' => 17, 'cabang_id' => 10],
        ]);

        // -------------------------------------------------------
        // barang_masuk_item
        // -------------------------------------------------------
        $this->db->table('barang_masuk_item')->insertBatch([
            ['id' => 14, 'barang_masuk_id' => 11, 'barang_id' => 182, 'qty_kiriman' =>  8, 'qty_aktual' => 16.00, 'selisih' =>  0.00, 'satuan_kirim' => 'Dus', 'satuan_simpan' => 'Kg'],
            ['id' => 15, 'barang_masuk_id' => 11, 'barang_id' => 183, 'qty_kiriman' =>  5, 'qty_aktual' =>  9.00, 'selisih' => -1.00, 'satuan_kirim' => 'Dus', 'satuan_simpan' => 'Kg'],
            ['id' => 16, 'barang_masuk_id' => 12, 'barang_id' => 182, 'qty_kiriman' => 10, 'qty_aktual' => 18.00, 'selisih' => -2.00, 'satuan_kirim' => 'Dus', 'satuan_simpan' => 'Kg'],
        ]);

        // -------------------------------------------------------
        // stok_gudang
        // -------------------------------------------------------
        $this->db->table('stok_gudang')->insertBatch([
            ['id' => 1, 'gudang_id' => 3, 'barang_id' => 183, 'satuan_id' => 16, 'stock' => 13.00],
            ['id' => 2, 'gudang_id' => 3, 'barang_id' => 182, 'satuan_id' => 16, 'stock' => 27.00],
            ['id' => 3, 'gudang_id' => 3, 'barang_id' => 184, 'satuan_id' => 16, 'stock' => 67.00],
            ['id' => 4, 'gudang_id' => 3, 'barang_id' => 185, 'satuan_id' => 16, 'stock' => 30.00],
        ]);

        // -------------------------------------------------------
        // penjualan
        // -------------------------------------------------------
        $this->db->table('penjualan')->insertBatch([
            ['id' => 17, 'faktur' => 'PJ-20260225032553',    'pembayaran_id' => 30,   'nominal_penjualan' => 1521000.00, 'operator_id' => 17, 'cabang_id' => 10, 'created_at' => '2026-02-24 20:25:53', 'customer_id' => 10,   'print_out' => 0],
            ['id' => 18, 'faktur' => 'PJ-20260225162657',    'pembayaran_id' => 31,   'nominal_penjualan' => 1204000.00, 'operator_id' => 17, 'cabang_id' => 10, 'created_at' => '2026-02-25 09:26:57', 'customer_id' => null, 'print_out' => 0],
            ['id' => 19, 'faktur' => 'DRAFT-20260225162727', 'pembayaran_id' => null, 'nominal_penjualan' =>   28000.00, 'operator_id' => 17, 'cabang_id' => 10, 'created_at' => null,                   'customer_id' => null, 'print_out' => 0],
            ['id' => 20, 'faktur' => 'PJ-20260225214303',    'pembayaran_id' => 32,   'nominal_penjualan' =>  616000.00, 'operator_id' => 17, 'cabang_id' => 10, 'created_at' => '2026-02-25 14:43:03', 'customer_id' => null, 'print_out' => 0],
            ['id' => 21, 'faktur' => 'PJ-20260225214902',    'pembayaran_id' => 33,   'nominal_penjualan' =>  588000.00, 'operator_id' => 17, 'cabang_id' => 10, 'created_at' => '2026-02-25 14:49:02', 'customer_id' => null, 'print_out' => 0],
            ['id' => 22, 'faktur' => 'PJ-20260225220114',    'pembayaran_id' => 34,   'nominal_penjualan' =>  160000.00, 'operator_id' => 17, 'cabang_id' => 10, 'created_at' => '2026-02-25 15:01:14', 'customer_id' => null, 'print_out' => 0],
            ['id' => 23, 'faktur' => 'PJ-20260225220333',    'pembayaran_id' => 35,   'nominal_penjualan' =>  160500.00, 'operator_id' => 17, 'cabang_id' => 10, 'created_at' => '2026-02-25 15:03:33', 'customer_id' => null, 'print_out' => 0],
            ['id' => 24, 'faktur' => 'PJ-20260225220358',    'pembayaran_id' => 36,   'nominal_penjualan' =>  370000.00, 'operator_id' => 17, 'cabang_id' => 10, 'created_at' => '2026-02-25 15:03:58', 'customer_id' => null, 'print_out' => 0],
            ['id' => 25, 'faktur' => 'PJ-20260225224416',    'pembayaran_id' => 37,   'nominal_penjualan' =>  444000.00, 'operator_id' => 17, 'cabang_id' => 10, 'created_at' => '2026-02-25 15:44:16', 'customer_id' => null, 'print_out' => 0],
            ['id' => 26, 'faktur' => 'PJ-20260225224912',    'pembayaran_id' => 38,   'nominal_penjualan' => 1229000.00, 'operator_id' => 17, 'cabang_id' => 10, 'created_at' => '2026-02-25 15:49:12', 'customer_id' => 10,   'print_out' => 0],
            ['id' => 27, 'faktur' => 'DRAFT-20260225224912', 'pembayaran_id' => null, 'nominal_penjualan' =>       0.00, 'operator_id' => 17, 'cabang_id' => 10, 'created_at' => null,                   'customer_id' => null, 'print_out' => 0],
            ['id' => 28, 'faktur' => 'DRAFT-20260227105453', 'pembayaran_id' => null, 'nominal_penjualan' =>       0.00, 'operator_id' => 17, 'cabang_id' => 10, 'created_at' => null,                   'customer_id' => null, 'print_out' => 0],
            ['id' => 29, 'faktur' => 'PJ-20260302145244',    'pembayaran_id' => 39,   'nominal_penjualan' =>  140000.00, 'operator_id' => 17, 'cabang_id' => 10, 'created_at' => '2026-03-02 07:52:44', 'customer_id' => null, 'print_out' => 0],
            ['id' => 30, 'faktur' => 'PJ-20260302145304',    'pembayaran_id' => 40,   'nominal_penjualan' =>   56000.00, 'operator_id' => 17, 'cabang_id' => 10, 'created_at' => '2026-03-02 07:53:04', 'customer_id' => null, 'print_out' => 0],
            ['id' => 31, 'faktur' => 'PJ-20260302145740',    'pembayaran_id' => 41,   'nominal_penjualan' =>   28000.00, 'operator_id' => 17, 'cabang_id' => 10, 'created_at' => '2026-03-02 07:57:40', 'customer_id' => null, 'print_out' => 0],
            ['id' => 32, 'faktur' => 'PJ-20260302150219',    'pembayaran_id' => 42,   'nominal_penjualan' =>   28000.00, 'operator_id' => 17, 'cabang_id' => 10, 'created_at' => '2026-03-02 08:02:19', 'customer_id' => null, 'print_out' => 0],
            ['id' => 33, 'faktur' => 'DRAFT-20260302150219', 'pembayaran_id' => null, 'nominal_penjualan' =>   28000.00, 'operator_id' => 17, 'cabang_id' => 10, 'created_at' => null,                   'customer_id' => null, 'print_out' => 0],
            ['id' => 34, 'faktur' => 'PJ-20260303015151',    'pembayaran_id' => 43,   'nominal_penjualan' =>  316500.00, 'operator_id' => 17, 'cabang_id' => 10, 'created_at' => '2026-03-02 18:51:51', 'customer_id' => null, 'print_out' => 0],
            ['id' => 35, 'faktur' => 'DRAFT-20260303015151', 'pembayaran_id' => null, 'nominal_penjualan' =>       0.00, 'operator_id' => 17, 'cabang_id' => 10, 'created_at' => null,                   'customer_id' => null, 'print_out' => 0],
            ['id' => 36, 'faktur' => 'PJ-20260303141913',    'pembayaran_id' => 44,   'nominal_penjualan' =>   46000.00, 'operator_id' => 17, 'cabang_id' => 10, 'created_at' => '2026-03-03 07:19:13', 'customer_id' => null, 'print_out' => 0],
            ['id' => 37, 'faktur' => 'DRAFT-20260303141913', 'pembayaran_id' => null, 'nominal_penjualan' =>       0.00, 'operator_id' => 17, 'cabang_id' => 10, 'created_at' => null,                   'customer_id' => null, 'print_out' => 0],
        ]);

        // -------------------------------------------------------
        // penjualan_item
        // -------------------------------------------------------
        $this->db->table('penjualan_item')->insertBatch([
            ['id' =>  1, 'penjualan_id' => 17, 'inventory_id' => 91, 'harga_satuan' => 11500.00, 'nominal_diskon' =>    0.00, 'harga_setelah_diskon' => 11500.00, 'qty' => 30, 'subtotal' =>  345000.00, 'keterangan' => null],
            ['id' =>  2, 'penjualan_id' => 17, 'inventory_id' => 92, 'harga_satuan' => 28000.00, 'nominal_diskon' =>    0.00, 'harga_setelah_diskon' => 28000.00, 'qty' => 42, 'subtotal' => 1176000.00, 'keterangan' => null],
            ['id' =>  3, 'penjualan_id' => 18, 'inventory_id' => 92, 'harga_satuan' => 28000.00, 'nominal_diskon' =>    0.00, 'harga_setelah_diskon' => 28000.00, 'qty' => 43, 'subtotal' => 1204000.00, 'keterangan' => null],
            ['id' =>  4, 'penjualan_id' => 19, 'inventory_id' => 92, 'harga_satuan' => 28000.00, 'nominal_diskon' =>    0.00, 'harga_setelah_diskon' => 28000.00, 'qty' =>  1, 'subtotal' =>   28000.00, 'keterangan' => null],
            ['id' =>  5, 'penjualan_id' => 20, 'inventory_id' => 92, 'harga_satuan' => 28000.00, 'nominal_diskon' =>    0.00, 'harga_setelah_diskon' => 28000.00, 'qty' => 22, 'subtotal' =>  616000.00, 'keterangan' => null],
            ['id' =>  7, 'penjualan_id' => 21, 'inventory_id' => 92, 'harga_satuan' => 28000.00, 'nominal_diskon' =>    0.00, 'harga_setelah_diskon' => 28000.00, 'qty' => 21, 'subtotal' =>  588000.00, 'keterangan' => null],
            ['id' => 10, 'penjualan_id' => 22, 'inventory_id' => 94, 'harga_satuan' => 16000.00, 'nominal_diskon' =>    0.00, 'harga_setelah_diskon' => 16000.00, 'qty' => 10, 'subtotal' =>  160000.00, 'keterangan' => null],
            ['id' => 11, 'penjualan_id' => 23, 'inventory_id' => 91, 'harga_satuan' => 11500.00, 'nominal_diskon' =>    0.00, 'harga_setelah_diskon' => 11500.00, 'qty' =>  7, 'subtotal' =>   80500.00, 'keterangan' => null],
            ['id' => 12, 'penjualan_id' => 23, 'inventory_id' => 94, 'harga_satuan' => 16000.00, 'nominal_diskon' =>    0.00, 'harga_setelah_diskon' => 16000.00, 'qty' =>  5, 'subtotal' =>   80000.00, 'keterangan' => null],
            ['id' => 13, 'penjualan_id' => 24, 'inventory_id' => 93, 'harga_satuan' => 37000.00, 'nominal_diskon' =>    0.00, 'harga_setelah_diskon' => 37000.00, 'qty' => 10, 'subtotal' =>  370000.00, 'keterangan' => null],
            ['id' => 14, 'penjualan_id' => 25, 'inventory_id' => 93, 'harga_satuan' => 37000.00, 'nominal_diskon' =>    0.00, 'harga_setelah_diskon' => 37000.00, 'qty' => 12, 'subtotal' =>  444000.00, 'keterangan' => null],
            ['id' => 15, 'penjualan_id' => 26, 'inventory_id' => 92, 'harga_satuan' => 28000.00, 'nominal_diskon' =>    0.00, 'harga_setelah_diskon' => 28000.00, 'qty' => 15, 'subtotal' =>  420000.00, 'keterangan' => null],
            ['id' => 16, 'penjualan_id' => 26, 'inventory_id' => 93, 'harga_satuan' => 37000.00, 'nominal_diskon' =>    0.00, 'harga_setelah_diskon' => 37000.00, 'qty' => 14, 'subtotal' =>  518000.00, 'keterangan' => null],
            ['id' => 17, 'penjualan_id' => 26, 'inventory_id' => 94, 'harga_satuan' => 16000.00, 'nominal_diskon' =>    0.00, 'harga_setelah_diskon' => 16000.00, 'qty' => 11, 'subtotal' =>  176000.00, 'keterangan' => null],
            ['id' => 18, 'penjualan_id' => 26, 'inventory_id' => 91, 'harga_satuan' => 11500.00, 'nominal_diskon' =>    0.00, 'harga_setelah_diskon' => 11500.00, 'qty' => 10, 'subtotal' =>  115000.00, 'keterangan' => null],
            ['id' => 19, 'penjualan_id' => 29, 'inventory_id' => 92, 'harga_satuan' => 28000.00, 'nominal_diskon' =>    0.00, 'harga_setelah_diskon' => 28000.00, 'qty' =>  5, 'subtotal' =>  140000.00, 'keterangan' => null],
            ['id' => 20, 'penjualan_id' => 30, 'inventory_id' => 92, 'harga_satuan' => 28000.00, 'nominal_diskon' =>    0.00, 'harga_setelah_diskon' => 28000.00, 'qty' =>  2, 'subtotal' =>   56000.00, 'keterangan' => null],
            ['id' => 21, 'penjualan_id' => 31, 'inventory_id' => 92, 'harga_satuan' => 28000.00, 'nominal_diskon' =>    0.00, 'harga_setelah_diskon' => 28000.00, 'qty' =>  1, 'subtotal' =>   28000.00, 'keterangan' => null],
            ['id' => 22, 'penjualan_id' => 32, 'inventory_id' => 92, 'harga_satuan' => 28000.00, 'nominal_diskon' =>    0.00, 'harga_setelah_diskon' => 28000.00, 'qty' =>  1, 'subtotal' =>   28000.00, 'keterangan' => null],
            ['id' => 23, 'penjualan_id' => 33, 'inventory_id' => 92, 'harga_satuan' => 28000.00, 'nominal_diskon' =>    0.00, 'harga_setelah_diskon' => 28000.00, 'qty' =>  1, 'subtotal' =>   28000.00, 'keterangan' => null],
            ['id' => 24, 'penjualan_id' => 34, 'inventory_id' => 92, 'harga_satuan' => 28000.00, 'nominal_diskon' => 3500.00, 'harga_setelah_diskon' => 24500.00, 'qty' =>  5, 'subtotal' =>  122500.00, 'keterangan' => null],
            ['id' => 25, 'penjualan_id' => 34, 'inventory_id' => 91, 'harga_satuan' => 11500.00, 'nominal_diskon' => 1500.00, 'harga_setelah_diskon' => 10000.00, 'qty' =>  4, 'subtotal' =>   40000.00, 'keterangan' => null],
            ['id' => 26, 'penjualan_id' => 34, 'inventory_id' => 94, 'harga_satuan' => 16000.00, 'nominal_diskon' =>    0.00, 'harga_setelah_diskon' => 16000.00, 'qty' =>  5, 'subtotal' =>   80000.00, 'keterangan' => null],
            ['id' => 27, 'penjualan_id' => 34, 'inventory_id' => 93, 'harga_satuan' => 37000.00, 'nominal_diskon' =>    0.00, 'harga_setelah_diskon' => 37000.00, 'qty' =>  2, 'subtotal' =>   74000.00, 'keterangan' => null],
            ['id' => 28, 'penjualan_id' => 36, 'inventory_id' => 92, 'harga_satuan' => 28000.00, 'nominal_diskon' => 5000.00, 'harga_setelah_diskon' => 23000.00, 'qty' =>  2, 'subtotal' =>   46000.00, 'keterangan' => null],
        ]);

        $this->db->query('SET FOREIGN_KEY_CHECKS = 1');
    }
}