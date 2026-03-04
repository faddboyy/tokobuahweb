<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RefactorPenerimaanGudang extends Migration
{
    public function up(): void
    {
        // -------------------------------------------------------
        // 1. Ubah status surat_jalan: hapus nilai 'diterima'
        //    Enum baru: order, selesai, dibatalkan
        // -------------------------------------------------------
        $this->db->query("
            ALTER TABLE `surat_jalan`
            MODIFY COLUMN `status`
                ENUM('order','selesai','dibatalkan')
                CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci
                NOT NULL DEFAULT 'order'
        ");

        // -------------------------------------------------------
        // 2. Buat table penerimaan_gudang
        // -------------------------------------------------------
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'auto_increment' => true,
            ],
            'kode_penerimaan' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'unique'     => true,
            ],
            'surat_jalan_id' => [
                'type' => 'INT',
            ],
            'gudang_id' => [
                'type' => 'INT',
            ],
            'waktu_penerimaan' => [
                'type' => 'TIMESTAMP',
            ],
            'catatan' => [
                'type'    => 'TEXT',
                'null'    => true,
                'default' => null,
            ],
            'operator_id' => [
                'type' => 'INT',
            ],
            'created_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('surat_jalan_id', 'surat_jalan', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('gudang_id', 'gudang_utama', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->addForeignKey('operator_id', 'users', 'id', 'CASCADE', 'CASCADE');

        $this->forge->createTable('penerimaan_gudang');

        // -------------------------------------------------------
        // 3. Buat table penerimaan_gudang_item
        // -------------------------------------------------------
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'auto_increment' => true,
            ],
            'penerimaan_gudang_id' => [
                'type' => 'INT',
            ],
            'barang_id' => [
                'type' => 'INT',
            ],
            'satuan_id' => [
                'type' => 'INT',
            ],
            'harga_pokok_satuan' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
            ],
            'qty_dipesan' => [
                'type' => 'INT',
            ],
            'qty_diterima' => [
                'type' => 'INT',
            ],
            'subtotal' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
            ],
            'keterangan' => [
                'type'    => 'TEXT',
                'null'    => true,
                'default' => null,
            ],
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('penerimaan_gudang_id', 'penerimaan_gudang', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('barang_id', 'barang', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('satuan_id', 'satuan', 'id', 'RESTRICT', 'CASCADE');

        $this->forge->createTable('penerimaan_gudang_item');

        // -------------------------------------------------------
        // 4. Ubah barang_masuk: drop FK & kolom surat_jalan_id,
        //    ganti dengan penerimaan_gudang_id
        // -------------------------------------------------------

        // Drop foreign key lama (nama constraint dari SQL dump)
        $this->db->query('ALTER TABLE `barang_masuk` DROP FOREIGN KEY `barang_masuk_pre_order_id_foreign`');

        // Drop kolom surat_jalan_id lama
        $this->forge->dropColumn('barang_masuk', 'surat_jalan_id');

        // Tambah kolom penerimaan_gudang_id
        $fields = [
            'penerimaan_gudang_id' => [
                'type'    => 'INT',
                'null'    => true,
                'default' => null,
                'after'   => 'kode_masuk',
            ],
        ];
        $this->forge->addColumn('barang_masuk', $fields);

        // Tambah FK baru
        $this->db->query('
            ALTER TABLE `barang_masuk`
            ADD CONSTRAINT `barang_masuk_penerimaan_gudang_id_foreign`
                FOREIGN KEY (`penerimaan_gudang_id`)
                REFERENCES `penerimaan_gudang` (`id`)
                ON DELETE SET NULL ON UPDATE CASCADE
        ');
    }

    public function down(): void
    {
        // -------------------------------------------------------
        // Rollback: kembalikan barang_masuk ke surat_jalan_id
        // -------------------------------------------------------
        $this->db->query('ALTER TABLE `barang_masuk` DROP FOREIGN KEY `barang_masuk_penerimaan_gudang_id_foreign`');
        $this->forge->dropColumn('barang_masuk', 'penerimaan_gudang_id');

        $fields = [
            'surat_jalan_id' => [
                'type'    => 'INT',
                'null'    => true,
                'default' => null,
                'after'   => 'kode_masuk',
            ],
        ];
        $this->forge->addColumn('barang_masuk', $fields);

        $this->db->query('
            ALTER TABLE `barang_masuk`
            ADD CONSTRAINT `barang_masuk_pre_order_id_foreign`
                FOREIGN KEY (`surat_jalan_id`)
                REFERENCES `surat_jalan` (`id`)
                ON DELETE CASCADE ON UPDATE SET NULL
        ');

        // -------------------------------------------------------
        // Drop tabel baru
        // -------------------------------------------------------
        $this->forge->dropTable('penerimaan_gudang_item', true);
        $this->forge->dropTable('penerimaan_gudang', true);

        // -------------------------------------------------------
        // Kembalikan enum status surat_jalan seperti semula
        // -------------------------------------------------------
        $this->db->query("
            ALTER TABLE `surat_jalan`
            MODIFY COLUMN `status`
                ENUM('order','diterima','selesai','dibatalkan')
                CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci
                NOT NULL DEFAULT 'order'
        ");
    }
}