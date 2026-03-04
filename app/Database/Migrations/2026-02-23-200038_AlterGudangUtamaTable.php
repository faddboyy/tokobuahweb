<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterGudangUtamaTable extends Migration
{
    public function up()
    {
        /*
        |--------------------------------------------------------------------------
        | ADD FIELD MANDOR_ID
        |--------------------------------------------------------------------------
        */
            $this->forge->addColumn('gudang_utama', [
                'mandor_id' => [
                    'type' => 'INT',
                    'null' => true,
                    'after' => 'nama',
                ],
            ]);

            $this->forge->addForeignKey(
                'mandor_id',
                'users',
                'id',
                'SET NULL',
                'CASCADE'
            );
    }

    public function down()
    {
        /*
        |--------------------------------------------------------------------------
        | REMOVE MANDOR_ID
        |--------------------------------------------------------------------------
        */
            $this->forge->dropForeignKey('gudang_utama', 'gudang_utama_mandor_id_foreign');
            $this->forge->dropColumn('gudang_utama', 'mandor_id');

        /*
        |--------------------------------------------------------------------------
        | RESTORE ALAMAT
        |--------------------------------------------------------------------------
        */
            $this->forge->addColumn('gudang_utama', [
                'alamat' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
            ]);
    }
}