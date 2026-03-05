<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class InitTokoBuah extends Migration
{
    public function up(): void
    {
        // Disable foreign key checks during table creation
        $this->db->query('SET FOREIGN_KEY_CHECKS = 0');

        // -------------------------------------------------------
        // cabang
        // -------------------------------------------------------
        $this->forge->addField([
            'id'   => ['type' => 'INT', 'auto_increment' => true],
            'nama' => ['type' => 'VARCHAR', 'constraint' => 100, 'collate' => 'utf8mb4_general_ci'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('cabang', true, [
            'ENGINE'          => 'InnoDB',
            'DEFAULT CHARSET' => 'utf8mb4',
            'COLLATE'         => 'utf8mb4_general_ci',
        ]);
        $this->db->query('ALTER TABLE `cabang` AUTO_INCREMENT = 12');

        // -------------------------------------------------------
        // jenis
        // -------------------------------------------------------
        $this->forge->addField([
            'id'   => ['type' => 'INT', 'auto_increment' => true],
            'nama' => ['type' => 'VARCHAR', 'constraint' => 100, 'collate' => 'utf8mb4_general_ci'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('jenis', true, [
            'ENGINE'          => 'InnoDB',
            'DEFAULT CHARSET' => 'utf8mb4',
            'COLLATE'         => 'utf8mb4_general_ci',
        ]);
        $this->db->query('ALTER TABLE `jenis` AUTO_INCREMENT = 23');

        // -------------------------------------------------------
        // satuan
        // -------------------------------------------------------
        $this->forge->addField([
            'id'   => ['type' => 'INT', 'auto_increment' => true],
            'nama' => ['type' => 'VARCHAR', 'constraint' => 50, 'collate' => 'utf8mb4_general_ci'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('satuan', true, [
            'ENGINE'          => 'InnoDB',
            'DEFAULT CHARSET' => 'utf8mb4',
            'COLLATE'         => 'utf8mb4_general_ci',
        ]);
        $this->db->query('ALTER TABLE `satuan` AUTO_INCREMENT = 17');

        // -------------------------------------------------------
        // users
        // -------------------------------------------------------
        $this->forge->addField([
            'id'        => ['type' => 'INT', 'auto_increment' => true],
            'username'  => ['type' => 'VARCHAR', 'constraint' => 100, 'collate' => 'utf8mb4_general_ci'],
            'nama'      => ['type' => 'VARCHAR', 'constraint' => 150, 'collate' => 'utf8mb4_general_ci'],
            'password'  => ['type' => 'VARCHAR', 'constraint' => 255, 'collate' => 'utf8mb4_general_ci'],
            'cabang_id' => ['type' => 'INT', 'null' => true, 'default' => null],
            'is_active' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'role'      => ['type' => 'ENUM', 'constraint' => ['owner', 'admin', 'petugas'], 'collate' => 'utf8mb4_general_ci', 'default' => 'petugas'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('cabang_id');
        $this->forge->createTable('users', true, [
            'ENGINE'          => 'InnoDB',
            'DEFAULT CHARSET' => 'utf8mb4',
            'COLLATE'         => 'utf8mb4_general_ci',
        ]);
        $this->db->query('ALTER TABLE `users` AUTO_INCREMENT = 21');
        $this->db->query('ALTER TABLE `users` ADD CONSTRAINT `users_cabang_id_foreign` FOREIGN KEY (`cabang_id`) REFERENCES `cabang` (`id`) ON DELETE CASCADE ON UPDATE SET NULL');

        // -------------------------------------------------------
        // suplier
        // -------------------------------------------------------
        $this->forge->addField([
            'id'       => ['type' => 'INT', 'auto_increment' => true],
            'nama'     => ['type' => 'VARCHAR', 'constraint' => 150],
            'alamat'   => ['type' => 'TEXT'],
            'telepon'  => ['type' => 'VARCHAR', 'constraint' => 20],
            'email'    => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true, 'default' => null],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('suplier', true, [
            'ENGINE'          => 'InnoDB',
            'DEFAULT CHARSET' => 'utf8mb4',
            'COLLATE'         => 'utf8mb4_0900_ai_ci',
        ]);
        $this->db->query('ALTER TABLE `suplier` AUTO_INCREMENT = 7');

        // -------------------------------------------------------
        // gudang_utama
        // -------------------------------------------------------
        $this->forge->addField([
            'id'        => ['type' => 'INT', 'auto_increment' => true],
            'nama'      => ['type' => 'VARCHAR', 'constraint' => 150, 'collate' => 'utf8mb4_general_ci'],
            'mandor_id' => ['type' => 'INT', 'null' => true, 'default' => null],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('gudang_utama', true, [
            'ENGINE'          => 'InnoDB',
            'DEFAULT CHARSET' => 'utf8mb4',
            'COLLATE'         => 'utf8mb4_general_ci',
        ]);
        $this->db->query('ALTER TABLE `gudang_utama` AUTO_INCREMENT = 4');

        // -------------------------------------------------------
        // barang
        // -------------------------------------------------------
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'auto_increment' => true],
            'barcode'     => ['type' => 'VARCHAR', 'constraint' => 100, 'collate' => 'utf8mb4_general_ci'],
            'nama'        => ['type' => 'VARCHAR', 'constraint' => 150, 'collate' => 'utf8mb4_general_ci'],
            'jenis_id'    => ['type' => 'INT'],
            'satuan_id'   => ['type' => 'INT'],
            'harga_pokok' => ['type' => 'DECIMAL', 'constraint' => '15,2'],
            'harga_jual'  => ['type' => 'DECIMAL', 'constraint' => '15,2'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('barcode');
        $this->forge->addKey('jenis_id');
        $this->forge->addKey('satuan_id');
        $this->forge->createTable('barang', true, [
            'ENGINE'          => 'InnoDB',
            'DEFAULT CHARSET' => 'utf8mb4',
            'COLLATE'         => 'utf8mb4_general_ci',
        ]);
        $this->db->query('ALTER TABLE `barang` AUTO_INCREMENT = 186');
        $this->db->query('ALTER TABLE `barang` ADD CONSTRAINT `barang_jenis_id_foreign` FOREIGN KEY (`jenis_id`) REFERENCES `jenis` (`id`) ON DELETE CASCADE ON UPDATE CASCADE');
        $this->db->query('ALTER TABLE `barang` ADD CONSTRAINT `barang_satuan_id_foreign` FOREIGN KEY (`satuan_id`) REFERENCES `satuan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE');

        // -------------------------------------------------------
        // customer
        // -------------------------------------------------------
        $this->forge->addField([
            'id'        => ['type' => 'INT', 'auto_increment' => true],
            'nama'      => ['type' => 'VARCHAR', 'constraint' => 150],
            'alamat'    => ['type' => 'TEXT'],
            'telepon'   => ['type' => 'VARCHAR', 'constraint' => 20],
            'cabang_id' => ['type' => 'INT', 'null' => true, 'default' => null],
            'added_by'  => ['type' => 'INT'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('cabang_id');
        $this->forge->addKey('added_by');
        $this->forge->createTable('customer', true, [
            'ENGINE'          => 'InnoDB',
            'DEFAULT CHARSET' => 'utf8mb4',
            'COLLATE'         => 'utf8mb4_0900_ai_ci',
        ]);
        $this->db->query('ALTER TABLE `customer` AUTO_INCREMENT = 11');
        $this->db->query('ALTER TABLE `customer` ADD CONSTRAINT `customer_ibfk_1` FOREIGN KEY (`cabang_id`) REFERENCES `cabang` (`id`) ON DELETE SET NULL');
        $this->db->query('ALTER TABLE `customer` ADD CONSTRAINT `customer_ibfk_2` FOREIGN KEY (`added_by`) REFERENCES `users` (`id`) ON DELETE CASCADE');

        // -------------------------------------------------------
        // diskon_terbatas
        // -------------------------------------------------------
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'nama'        => ['type' => 'VARCHAR', 'constraint' => 150, 'collate' => 'utf8mb4_general_ci'],
            'cabang_id'   => ['type' => 'INT', 'unsigned' => true],
            'tgl_mulai'   => ['type' => 'DATE'],
            'tgl_selesai' => ['type' => 'DATE'],
            'status'      => ['type' => 'ENUM', 'constraint' => ['aktif', 'nonaktif'], 'collate' => 'utf8mb4_general_ci', 'default' => 'aktif'],
            'created_by'  => ['type' => 'INT', 'unsigned' => true, 'null' => true, 'default' => null],
            'created_at'  => ['type' => 'TIMESTAMP', 'null' => true, 'default' => null],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('cabang_id');
        $this->forge->addKey(['tgl_mulai', 'tgl_selesai'], false, false, 'tgl_mulai_tgl_selesai');
        $this->forge->createTable('diskon_terbatas', true, [
            'ENGINE'          => 'InnoDB',
            'DEFAULT CHARSET' => 'utf8mb4',
            'COLLATE'         => 'utf8mb4_general_ci',
        ]);
        $this->db->query('ALTER TABLE `diskon_terbatas` AUTO_INCREMENT = 3');

        // -------------------------------------------------------
        // diskon_terbatas_item
        // -------------------------------------------------------
        $this->forge->addField([
            'id'                 => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'diskon_terbatas_id' => ['type' => 'INT', 'unsigned' => true],
            'barang_id'          => ['type' => 'INT', 'unsigned' => true],
            'nominal_diskon'     => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => '0.00'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['diskon_terbatas_id', 'barang_id'], 'diskon_terbatas_id_barang_id');
        $this->forge->addKey('diskon_terbatas_id');
        $this->forge->addKey('barang_id');
        $this->forge->createTable('diskon_terbatas_item', true, [
            'ENGINE'          => 'InnoDB',
            'DEFAULT CHARSET' => 'utf8mb4',
            'COLLATE'         => 'utf8mb4_general_ci',
        ]);
        $this->db->query('ALTER TABLE `diskon_terbatas_item` AUTO_INCREMENT = 7');
        $this->db->query('ALTER TABLE `diskon_terbatas_item` ADD CONSTRAINT `fk_dti_diskon_terbatas` FOREIGN KEY (`diskon_terbatas_id`) REFERENCES `diskon_terbatas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE');

        // -------------------------------------------------------
        // inventory
        // -------------------------------------------------------
        $this->forge->addField([
            'id'        => ['type' => 'INT', 'auto_increment' => true],
            'barang_id' => ['type' => 'INT'],
            'cabang_id' => ['type' => 'INT'],
            'stock'     => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => '0.00'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('barang_id');
        $this->forge->addKey('cabang_id');
        $this->forge->createTable('inventory', true, [
            'ENGINE'          => 'InnoDB',
            'DEFAULT CHARSET' => 'utf8mb4',
            'COLLATE'         => 'utf8mb4_general_ci',
        ]);
        $this->db->query('ALTER TABLE `inventory` AUTO_INCREMENT = 99');
        $this->db->query('ALTER TABLE `inventory` ADD CONSTRAINT `inventory_barang_id_foreign` FOREIGN KEY (`barang_id`) REFERENCES `barang` (`id`) ON DELETE CASCADE ON UPDATE CASCADE');
        $this->db->query('ALTER TABLE `inventory` ADD CONSTRAINT `inventory_cabang_id_foreign` FOREIGN KEY (`cabang_id`) REFERENCES `cabang` (`id`) ON DELETE CASCADE ON UPDATE CASCADE');

        // -------------------------------------------------------
        // pembayaran
        // -------------------------------------------------------
        $this->forge->addField([
            'id'               => ['type' => 'INT', 'auto_increment' => true],
            'jenis_pembayaran' => ['type' => 'VARCHAR', 'constraint' => 100, 'collate' => 'utf8mb4_general_ci'],
            'diskon_persen'    => ['type' => 'DECIMAL', 'constraint' => '5,2', 'null' => true, 'default' => null],
            'diskon_nominal'   => ['type' => 'DECIMAL', 'constraint' => '15,2', 'null' => true, 'default' => null],
            'nominal_bayar'    => ['type' => 'DECIMAL', 'constraint' => '15,2'],
            'kembalian'        => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => '0.00'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('pembayaran', true, [
            'ENGINE'          => 'InnoDB',
            'DEFAULT CHARSET' => 'utf8mb4',
            'COLLATE'         => 'utf8mb4_general_ci',
        ]);
        $this->db->query('ALTER TABLE `pembayaran` AUTO_INCREMENT = 45');

        // -------------------------------------------------------
        // surat_jalan
        // -------------------------------------------------------
        $this->forge->addField([
            'id'             => ['type' => 'INT', 'auto_increment' => true],
            'kode_po'        => ['type' => 'VARCHAR', 'constraint' => 100, 'collate' => 'utf8mb4_general_ci'],
            'suplier_id'     => ['type' => 'INT'],
            'gudang_id'      => ['type' => 'INT'],
            'waktu_po'       => ['type' => 'TIMESTAMP'],
            'status'         => ['type' => 'ENUM', 'constraint' => ['order', 'selesai', 'dibatalkan'], 'collate' => 'utf8mb4_general_ci', 'default' => 'order'],
            'total_nominal'  => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => '0.00'],
            'operator_id'    => ['type' => 'INT'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('kode_po');
        $this->forge->addKey('suplier_id');
        $this->forge->addKey('operator_id');
        $this->forge->addKey('gudang_id');
        $this->forge->createTable('surat_jalan', true, [
            'ENGINE'          => 'InnoDB',
            'DEFAULT CHARSET' => 'utf8mb4',
            'COLLATE'         => 'utf8mb4_general_ci',
        ]);
        $this->db->query('ALTER TABLE `surat_jalan` AUTO_INCREMENT = 21');
        $this->db->query('ALTER TABLE `surat_jalan` ADD CONSTRAINT `fk_surat_jalan_gudang` FOREIGN KEY (`gudang_id`) REFERENCES `gudang_utama` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE');
        $this->db->query('ALTER TABLE `surat_jalan` ADD CONSTRAINT `pre_order_operator_id_foreign` FOREIGN KEY (`operator_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE');
        $this->db->query('ALTER TABLE `surat_jalan` ADD CONSTRAINT `pre_order_suplier_id_foreign` FOREIGN KEY (`suplier_id`) REFERENCES `suplier` (`id`) ON DELETE CASCADE ON UPDATE CASCADE');

        // -------------------------------------------------------
        // surat_jalan_item
        // -------------------------------------------------------
        $this->forge->addField([
            'id'             => ['type' => 'INT', 'auto_increment' => true],
            'surat_jalan_id' => ['type' => 'INT'],
            'barang_id'      => ['type' => 'INT'],
            'satuan_id'      => ['type' => 'INT'],
            'harga_beli'     => ['type' => 'DECIMAL', 'constraint' => '15,2'],
            'qty'            => ['type' => 'INT'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('surat_jalan_id');
        $this->forge->addKey('barang_id');
        $this->forge->addKey('satuan_id');
        $this->forge->createTable('surat_jalan_item', true, [
            'ENGINE'          => 'InnoDB',
            'DEFAULT CHARSET' => 'utf8mb4',
            'COLLATE'         => 'utf8mb4_general_ci',
        ]);
        $this->db->query('ALTER TABLE `surat_jalan_item` AUTO_INCREMENT = 35');
        $this->db->query('ALTER TABLE `surat_jalan_item` ADD CONSTRAINT `pre_order_item_barang_id_foreign` FOREIGN KEY (`barang_id`) REFERENCES `barang` (`id`) ON DELETE CASCADE ON UPDATE CASCADE');
        $this->db->query('ALTER TABLE `surat_jalan_item` ADD CONSTRAINT `pre_order_item_pre_order_id_foreign` FOREIGN KEY (`surat_jalan_id`) REFERENCES `surat_jalan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE');
        $this->db->query('ALTER TABLE `surat_jalan_item` ADD CONSTRAINT `surat_jalan_item_ibfk_1` FOREIGN KEY (`satuan_id`) REFERENCES `satuan` (`id`)');

        // -------------------------------------------------------
        // penerimaan_gudang
        // -------------------------------------------------------
        $this->forge->addField([
            'id'               => ['type' => 'INT', 'auto_increment' => true],
            'kode_penerimaan'  => ['type' => 'VARCHAR', 'constraint' => 100, 'collate' => 'utf8mb4_general_ci'],
            'surat_jalan_id'   => ['type' => 'INT'],
            'kode_supplier'    => ['type' => 'VARCHAR', 'constraint' => 100, 'collate' => 'utf8mb4_general_ci', 'null' => true, 'default' => null],
            'gudang_id'        => ['type' => 'INT'],
            'waktu_penerimaan' => ['type' => 'TIMESTAMP'],
            'operator_id'      => ['type' => 'INT'],
            'status'           => ['type' => 'ENUM', 'constraint' => ['ditoko', 'digudang', 'dibatalkan'], 'collate' => 'utf8mb4_general_ci', 'null' => true, 'default' => null],
            'created_at'       => ['type' => 'TIMESTAMP', 'null' => true, 'default' => null],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('kode_penerimaan');
        $this->forge->addUniqueKey('kode_supplier', 'kode_surat_jalan');
        $this->forge->addKey('surat_jalan_id');
        $this->forge->addKey('gudang_id');
        $this->forge->addKey('operator_id');
        $this->forge->createTable('penerimaan_gudang', true, [
            'ENGINE'          => 'InnoDB',
            'DEFAULT CHARSET' => 'utf8mb4',
            'COLLATE'         => 'utf8mb4_general_ci',
        ]);
        $this->db->query('ALTER TABLE `penerimaan_gudang` AUTO_INCREMENT = 9');
        $this->db->query('ALTER TABLE `penerimaan_gudang` ADD CONSTRAINT `penerimaan_gudang_gudang_id_foreign` FOREIGN KEY (`gudang_id`) REFERENCES `gudang_utama` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT');
        $this->db->query('ALTER TABLE `penerimaan_gudang` ADD CONSTRAINT `penerimaan_gudang_operator_id_foreign` FOREIGN KEY (`operator_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE');
        $this->db->query('ALTER TABLE `penerimaan_gudang` ADD CONSTRAINT `penerimaan_gudang_surat_jalan_id_foreign` FOREIGN KEY (`surat_jalan_id`) REFERENCES `surat_jalan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE');

        // -------------------------------------------------------
        // penerimaan_gudang_item
        // -------------------------------------------------------
        $this->forge->addField([
            'id'                  => ['type' => 'INT', 'auto_increment' => true],
            'penerimaan_gudang_id' => ['type' => 'INT'],
            'barang_id'           => ['type' => 'INT'],
            'qty_dipesan'         => ['type' => 'INT'],
            'qty_diterima'        => ['type' => 'INT'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('penerimaan_gudang_id');
        $this->forge->addKey('barang_id');
        $this->forge->createTable('penerimaan_gudang_item', true, [
            'ENGINE'          => 'InnoDB',
            'DEFAULT CHARSET' => 'utf8mb4',
            'COLLATE'         => 'utf8mb4_general_ci',
        ]);
        $this->db->query('ALTER TABLE `penerimaan_gudang_item` AUTO_INCREMENT = 11');
        $this->db->query('ALTER TABLE `penerimaan_gudang_item` ADD CONSTRAINT `penerimaan_gudang_item_barang_id_foreign` FOREIGN KEY (`barang_id`) REFERENCES `barang` (`id`) ON DELETE CASCADE ON UPDATE CASCADE');
        $this->db->query('ALTER TABLE `penerimaan_gudang_item` ADD CONSTRAINT `penerimaan_gudang_item_penerimaan_gudang_id_foreign` FOREIGN KEY (`penerimaan_gudang_id`) REFERENCES `penerimaan_gudang` (`id`) ON DELETE CASCADE ON UPDATE CASCADE');

        // -------------------------------------------------------
        // pengiriman_gudang
        // -------------------------------------------------------
        $this->forge->addField([
            'id'                => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'kode_pengiriman'   => ['type' => 'VARCHAR', 'constraint' => 100, 'collate' => 'utf8mb4_general_ci'],
            'gudang_id'         => ['type' => 'INT', 'unsigned' => true],
            'cabang_id'         => ['type' => 'INT', 'unsigned' => true],
            'operator_id'       => ['type' => 'INT', 'unsigned' => true],
            'waktu_pengiriman'  => ['type' => 'TIMESTAMP'],
            'status'            => ['type' => 'ENUM', 'constraint' => ['dikirim', 'diterima', 'dibatalkan'], 'collate' => 'utf8mb4_general_ci', 'default' => 'dikirim'],
            'created_at'        => ['type' => 'TIMESTAMP', 'null' => true, 'default' => null],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('kode_pengiriman');
        $this->forge->addKey('gudang_id');
        $this->forge->addKey('cabang_id');
        $this->forge->addKey('operator_id');
        $this->forge->createTable('pengiriman_gudang', true, [
            'ENGINE'          => 'InnoDB',
            'DEFAULT CHARSET' => 'utf8mb4',
            'COLLATE'         => 'utf8mb4_general_ci',
        ]);
        $this->db->query('ALTER TABLE `pengiriman_gudang` AUTO_INCREMENT = 4');

        // -------------------------------------------------------
        // pengiriman_gudang_item
        // -------------------------------------------------------
        $this->forge->addField([
            'id'                    => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'pengiriman_gudang_id'  => ['type' => 'INT', 'unsigned' => true],
            'barang_id'             => ['type' => 'INT', 'unsigned' => true],
            'qty'                   => ['type' => 'DECIMAL', 'constraint' => '15,2'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('pengiriman_gudang_id');
        $this->forge->addKey('barang_id');
        $this->forge->createTable('pengiriman_gudang_item', true, [
            'ENGINE'          => 'InnoDB',
            'DEFAULT CHARSET' => 'utf8mb4',
            'COLLATE'         => 'utf8mb4_general_ci',
        ]);
        $this->db->query('ALTER TABLE `pengiriman_gudang_item` AUTO_INCREMENT = 6');
        $this->db->query('ALTER TABLE `pengiriman_gudang_item` ADD CONSTRAINT `pengiriman_gudang_item_pengiriman_gudang_id_foreign` FOREIGN KEY (`pengiriman_gudang_id`) REFERENCES `pengiriman_gudang` (`id`) ON DELETE CASCADE ON UPDATE CASCADE');

        // -------------------------------------------------------
        // barang_masuk
        // -------------------------------------------------------
        $this->forge->addField([
            'id'                    => ['type' => 'INT', 'auto_increment' => true],
            'kode_masuk'            => ['type' => 'VARCHAR', 'constraint' => 100, 'collate' => 'utf8mb4_general_ci'],
            'pengiriman_gudang_id'  => ['type' => 'INT', 'unsigned' => true, 'null' => true, 'default' => null],
            'waktu_masuk'           => ['type' => 'TIMESTAMP'],
            'operator_id'           => ['type' => 'INT'],
            'cabang_id'             => ['type' => 'INT', 'null' => true, 'default' => null],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('kode_masuk');
        $this->forge->addKey('operator_id');
        $this->forge->addKey('cabang_id');
        $this->forge->addKey('pengiriman_gudang_id', false, false, 'barang_masuk_pengiriman_gudang_id_index');
        $this->forge->createTable('barang_masuk', true, [
            'ENGINE'          => 'InnoDB',
            'DEFAULT CHARSET' => 'utf8mb4',
            'COLLATE'         => 'utf8mb4_general_ci',
        ]);
        $this->db->query('ALTER TABLE `barang_masuk` AUTO_INCREMENT = 13');
        $this->db->query('ALTER TABLE `barang_masuk` ADD CONSTRAINT `barang_masuk_ibfk_1` FOREIGN KEY (`cabang_id`) REFERENCES `cabang` (`id`)');
        $this->db->query('ALTER TABLE `barang_masuk` ADD CONSTRAINT `barang_masuk_operator_id_foreign` FOREIGN KEY (`operator_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE');

        // -------------------------------------------------------
        // barang_masuk_item
        // -------------------------------------------------------
        $this->forge->addField([
            'id'              => ['type' => 'INT', 'auto_increment' => true],
            'barang_masuk_id' => ['type' => 'INT'],
            'barang_id'       => ['type' => 'INT'],
            'qty_kiriman'     => ['type' => 'INT'],
            'qty_aktual'      => ['type' => 'DECIMAL', 'constraint' => '15,2'],
            'selisih'         => ['type' => 'DECIMAL', 'constraint' => '15,2'],
            'satuan_kirim'    => ['type' => 'VARCHAR', 'constraint' => 10, 'collate' => 'utf8mb4_general_ci'],
            'satuan_simpan'   => ['type' => 'VARCHAR', 'constraint' => 10, 'collate' => 'utf8mb4_general_ci'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('barang_masuk_id');
        $this->forge->addKey('barang_id');
        $this->forge->createTable('barang_masuk_item', true, [
            'ENGINE'          => 'InnoDB',
            'DEFAULT CHARSET' => 'utf8mb4',
            'COLLATE'         => 'utf8mb4_general_ci',
        ]);
        $this->db->query('ALTER TABLE `barang_masuk_item` AUTO_INCREMENT = 17');
        $this->db->query('ALTER TABLE `barang_masuk_item` ADD CONSTRAINT `barang_masuk_item_barang_id_foreign` FOREIGN KEY (`barang_id`) REFERENCES `barang` (`id`) ON DELETE CASCADE ON UPDATE CASCADE');
        $this->db->query('ALTER TABLE `barang_masuk_item` ADD CONSTRAINT `barang_masuk_item_barang_masuk_id_foreign` FOREIGN KEY (`barang_masuk_id`) REFERENCES `barang_masuk` (`id`) ON DELETE CASCADE ON UPDATE CASCADE');

        // -------------------------------------------------------
        // stok_gudang
        // -------------------------------------------------------
        $this->forge->addField([
            'id'        => ['type' => 'INT', 'auto_increment' => true],
            'gudang_id' => ['type' => 'INT'],
            'barang_id' => ['type' => 'INT'],
            'satuan_id' => ['type' => 'INT'],
            'stock'     => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => '0.00'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['gudang_id', 'barang_id', 'satuan_id'], 'uq_stok_gudang');
        $this->forge->addKey('barang_id');
        $this->forge->addKey('satuan_id');
        $this->forge->createTable('stok_gudang', true, [
            'ENGINE'          => 'InnoDB',
            'DEFAULT CHARSET' => 'utf8mb4',
            'COLLATE'         => 'utf8mb4_general_ci',
        ]);
        $this->db->query('ALTER TABLE `stok_gudang` AUTO_INCREMENT = 5');
        $this->db->query('ALTER TABLE `stok_gudang` ADD CONSTRAINT `stok_gudang_barang_id_foreign` FOREIGN KEY (`barang_id`) REFERENCES `barang` (`id`) ON DELETE CASCADE ON UPDATE CASCADE');
        $this->db->query('ALTER TABLE `stok_gudang` ADD CONSTRAINT `stok_gudang_gudang_id_foreign` FOREIGN KEY (`gudang_id`) REFERENCES `gudang_utama` (`id`) ON DELETE CASCADE ON UPDATE CASCADE');
        $this->db->query('ALTER TABLE `stok_gudang` ADD CONSTRAINT `stok_gudang_satuan_id_foreign` FOREIGN KEY (`satuan_id`) REFERENCES `satuan` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT');

        // -------------------------------------------------------
        // penjualan
        // -------------------------------------------------------
        $this->forge->addField([
            'id'                 => ['type' => 'INT', 'auto_increment' => true],
            'faktur'             => ['type' => 'VARCHAR', 'constraint' => 100, 'collate' => 'utf8mb4_general_ci'],
            'pembayaran_id'      => ['type' => 'INT', 'null' => true, 'default' => null],
            'nominal_penjualan'  => ['type' => 'DECIMAL', 'constraint' => '15,2'],
            'operator_id'        => ['type' => 'INT'],
            'cabang_id'          => ['type' => 'INT'],
            'created_at'         => ['type' => 'TIMESTAMP', 'null' => true, 'default' => null],
            'customer_id'        => ['type' => 'INT', 'null' => true, 'default' => null],
            'print_out'          => ['type' => 'TINYINT', 'default' => 0],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('faktur');
        $this->forge->addKey('pembayaran_id');
        $this->forge->addKey('operator_id');
        $this->forge->addKey('cabang_id');
        $this->forge->addKey('customer_id');
        $this->forge->createTable('penjualan', true, [
            'ENGINE'          => 'InnoDB',
            'DEFAULT CHARSET' => 'utf8mb4',
            'COLLATE'         => 'utf8mb4_general_ci',
        ]);
        $this->db->query('ALTER TABLE `penjualan` AUTO_INCREMENT = 38');
        $this->db->query('ALTER TABLE `penjualan` ADD CONSTRAINT `penjualan_cabang_id_foreign` FOREIGN KEY (`cabang_id`) REFERENCES `cabang` (`id`) ON DELETE CASCADE ON UPDATE CASCADE');
        $this->db->query('ALTER TABLE `penjualan` ADD CONSTRAINT `penjualan_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`id`)');
        $this->db->query('ALTER TABLE `penjualan` ADD CONSTRAINT `penjualan_operator_id_foreign` FOREIGN KEY (`operator_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE');
        $this->db->query('ALTER TABLE `penjualan` ADD CONSTRAINT `penjualan_pembayaran_id_foreign` FOREIGN KEY (`pembayaran_id`) REFERENCES `pembayaran` (`id`) ON DELETE CASCADE ON UPDATE CASCADE');

        // -------------------------------------------------------
        // penjualan_item
        // -------------------------------------------------------
        $this->forge->addField([
            'id'                  => ['type' => 'INT', 'auto_increment' => true],
            'penjualan_id'        => ['type' => 'INT', 'null' => true, 'default' => null],
            'inventory_id'        => ['type' => 'INT'],
            'harga_satuan'        => ['type' => 'DECIMAL', 'constraint' => '15,2'],
            'nominal_diskon'      => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => '0.00', 'comment' => 'Nominal diskon per satuan dari diskon_terbatas (0 = tidak ada diskon)'],
            'harga_setelah_diskon' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => '0.00', 'comment' => 'harga_satuan - nominal_diskon (harga efektif yang dipakai untuk subtotal)'],
            'qty'                 => ['type' => 'INT'],
            'subtotal'            => ['type' => 'DECIMAL', 'constraint' => '15,2'],
            'keterangan'          => ['type' => 'TEXT', 'collate' => 'utf8mb4_general_ci', 'null' => true, 'default' => null],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('penjualan_id');
        $this->forge->addKey('inventory_id');
        $this->forge->createTable('penjualan_item', true, [
            'ENGINE'          => 'InnoDB',
            'DEFAULT CHARSET' => 'utf8mb4',
            'COLLATE'         => 'utf8mb4_general_ci',
        ]);
        $this->db->query('ALTER TABLE `penjualan_item` AUTO_INCREMENT = 29');
        $this->db->query('ALTER TABLE `penjualan_item` ADD CONSTRAINT `penjualan_item_inventory_id_foreign` FOREIGN KEY (`inventory_id`) REFERENCES `inventory` (`id`) ON DELETE CASCADE ON UPDATE CASCADE');
        $this->db->query('ALTER TABLE `penjualan_item` ADD CONSTRAINT `penjualan_item_penjualan_id_foreign` FOREIGN KEY (`penjualan_id`) REFERENCES `penjualan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE');

        // Re-enable foreign key checks
        $this->db->query('SET FOREIGN_KEY_CHECKS = 1');
    }

    public function down(): void
    {
        $this->db->query('SET FOREIGN_KEY_CHECKS = 0');

        $tables = [
            'penjualan_item',
            'penjualan',
            'barang_masuk_item',
            'barang_masuk',
            'pengiriman_gudang_item',
            'pengiriman_gudang',
            'stok_gudang',
            'penerimaan_gudang_item',
            'penerimaan_gudang',
            'surat_jalan_item',
            'surat_jalan',
            'inventory',
            'diskon_terbatas_item',
            'diskon_terbatas',
            'customer',
            'barang',
            'pembayaran',
            'gudang_utama',
            'suplier',
            'users',
            'satuan',
            'jenis',
            'cabang',
        ];

        foreach ($tables as $table) {
            $this->forge->dropTable($table, true);
        }

        $this->db->query('SET FOREIGN_KEY_CHECKS = 1');
    }
}
