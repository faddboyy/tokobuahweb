<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RenamePreOrderToSuratJalan extends Migration
{
    public function up()
    {
        // 1️⃣ Rename tabel
        $this->forge->renameTable('pre_order', 'surat_jalan');

        // 2️⃣ Rename field di barang_masuk
        $fields = [
            'pre_order_id' => [
                'name' => 'surat_jalan_id',
                'type' => 'INT',
                'null' => true,
            ],
        ];

        $this->forge->modifyColumn('barang_masuk', $fields);
    }

    public function down()
    {
        // Rollback rename field
        $fields = [
            'surat_jalan_id' => [
                'name' => 'pre_order_id',
                'type' => 'INT',
                'null' => true,
            ],
        ];

        $this->forge->modifyColumn('barang_masuk', $fields);

        // Rollback rename tabel
        $this->forge->renameTable('surat_jalan', 'pre_order');
    }
}