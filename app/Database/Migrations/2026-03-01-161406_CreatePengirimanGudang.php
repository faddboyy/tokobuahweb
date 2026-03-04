<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePengirimanGudang extends Migration
{
    public function up(): void
    {
        // ── Tabel header pengiriman ──────────────────────────────────
        // Alur: gudang kirim barang → toko (cabang) tujuan
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            // Kode unik pengiriman, contoh: KRG-20260301123045
            'kode_pengiriman' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => false,
            ],
            // Gudang pengirim
            'gudang_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
            // Toko / cabang tujuan
            'cabang_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
            // Operator yang mencatat pengiriman
            'operator_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
            'waktu_pengiriman' => [
                'type' => 'TIMESTAMP',
                'null' => false,
            ],
            // Status: dikirim → diterima → dibatalkan
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['dikirim', 'diterima', 'dibatalkan'],
                'default'    => 'dikirim',
                'null'       => false,
            ],
            'catatan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('kode_pengiriman');
        $this->forge->addKey('gudang_id');
        $this->forge->addKey('cabang_id');
        $this->forge->addKey('operator_id');

        $this->forge->createTable('pengiriman_gudang', false, [
            'ENGINE'  => 'InnoDB',
            'CHARSET' => 'utf8mb4',
            'COLLATE' => 'utf8mb4_general_ci',
        ]);

        // ── Tabel item pengiriman ─────────────────────────────────────
        // Satuan mengikuti satuan barang (barang.satuan_id).
        // Konversi satuan dilakukan saat pencatatan barang masuk toko (nanti).
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'pengiriman_gudang_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
            'barang_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
            // Qty yang dikirim dari gudang, satuan mengikuti barang.satuan_id
            'qty' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'null'       => false,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('pengiriman_gudang_id');
        $this->forge->addKey('barang_id');

        $this->forge->addForeignKey('pengiriman_gudang_id', 'pengiriman_gudang', 'id', 'CASCADE', 'CASCADE');

        $this->forge->createTable('pengiriman_gudang_item', false, [
            'ENGINE'  => 'InnoDB',
            'CHARSET' => 'utf8mb4',
            'COLLATE' => 'utf8mb4_general_ci',
        ]);
    }

    public function down(): void
    {
        // Hapus item dulu karena ada foreign key ke header
        $this->forge->dropTable('pengiriman_gudang_item', true);
        $this->forge->dropTable('pengiriman_gudang', true);
    }
}