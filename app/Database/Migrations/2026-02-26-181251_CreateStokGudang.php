<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateStokGudang extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'auto_increment' => true,
            ],
            'gudang_id' => [
                'type' => 'INT',
            ],
            'barang_id' => [
                'type' => 'INT',
            ],
            'satuan_id' => [
                'type' => 'INT',
            ],
            'stock' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => '0.00',
            ],
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey(['gudang_id', 'barang_id', 'satuan_id'], 'uq_stok_gudang');
        $this->forge->addForeignKey('gudang_id', 'gudang_utama', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('barang_id', 'barang', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('satuan_id', 'satuan', 'id', 'RESTRICT', 'CASCADE');

        $this->forge->createTable('stok_gudang');
    }

    public function down(): void
    {
        $this->forge->dropTable('stok_gudang', true);
    }
}