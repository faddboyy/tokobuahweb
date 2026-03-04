<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePurchasingSchema extends Migration
{
    public function up()
    {
        /*
        |--------------------------------------------------------------------------
        | GUDANG UTAMA
        |--------------------------------------------------------------------------
        */
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'auto_increment' => true,
            ],
            'nama' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('gudang_utama');

        /*
        |--------------------------------------------------------------------------
        | PRE ORDER (PO)
        |--------------------------------------------------------------------------
        */
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'auto_increment' => true,
            ],
            'kode_po' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'supalier_id' => [
                'type' => 'INT',
            ],
            'waktu_po' => [
                'type' => 'TIMESTAMP',
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['order', 'diterima', 'dibatalkan'],
                'default' => 'order',
            ],
            'total_nominal' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => 0,
            ],
            'operator_id' => [
                'type' => 'INT',
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('suplier_id', 'suplier', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('operator_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('pre_order');

        /*
        |--------------------------------------------------------------------------
        | PRE ORDER ITEM
        |--------------------------------------------------------------------------
        */
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'auto_increment' => true,
            ],
            'pre_order_id' => [
                'type' => 'INT',
            ],
            'barang_id' => [
                'type' => 'INT',
            ],
            'harga' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
            ],
            'qty' => [
                'type' => 'INT',
            ],
            'subtotal' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('pre_order_id', 'pre_order', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('barang_id', 'barang', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('pre_order_item');

        /*
        |--------------------------------------------------------------------------
        | BARANG MASUK
        |--------------------------------------------------------------------------
        */
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'auto_increment' => true,
            ],
            'kode_masuk' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'pre_order_id' => [
                'type' => 'INT',
                'null' => true,
            ],
            'tanggal_masuk' => [
                'type' => 'DATE',
            ],
            'gudang_id' => [
                'type' => 'INT',
            ],
            'total_nominal' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => 0,
            ],
            'operator_id' => [
                'type' => 'INT',
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('pre_order_id', 'pre_order', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('gudang_id', 'gudang_utama', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('operator_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('barang_masuk');

        /*
        |--------------------------------------------------------------------------
        | BARANG MASUK ITEM
        |--------------------------------------------------------------------------
        */
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'auto_increment' => true,
            ],
            'barang_masuk_id' => [
                'type' => 'INT',
            ],
            'barang_id' => [
                'type' => 'INT',
            ],
            'harga' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
            ],
            'qty' => [
                'type' => 'INT',
            ],
            'subtotal' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('barang_masuk_id', 'barang_masuk', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('barang_id', 'barang', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('barang_masuk_item');
    }

    public function down()
    {
        $this->forge->dropTable('barang_masuk_item', true);
        $this->forge->dropTable('barang_masuk', true);
        $this->forge->dropTable('pre_order_item', true);
        $this->forge->dropTable('pre_order', true);
        $this->forge->dropTable('supplier', true);
        $this->forge->dropTable('gudang_utama', true);
    }
}