<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddKodeSuratJalanToPenerimaanGudang extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('penerimaan_gudang', [
            'kode_surat_jalan' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'default'    => null,
                'after'      => 'surat_jalan_id',
            ],
        ]);
    }

    public function down(): void
    {
        $this->forge->dropColumn('penerimaan_gudang', 'kode_surat_jalan');
    }
}