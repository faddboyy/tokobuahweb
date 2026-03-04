<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class InitSchema extends Migration
{
    public function up()
    {
        /*
        |--------------------------------------------------------------------------
        | CABANG
        |--------------------------------------------------------------------------
        */
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'auto_increment' => true,
            ],
            'nama' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('cabang');

        /*
        |--------------------------------------------------------------------------
        | JENIS
        |--------------------------------------------------------------------------
        */
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'auto_increment' => true,
            ],
            'nama' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('jenis');

        /*
        |--------------------------------------------------------------------------
        | SATUAN
        |--------------------------------------------------------------------------
        */
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'auto_increment' => true,
            ],
            'nama' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('satuan');

        /*
        |--------------------------------------------------------------------------
        | USERS
        |--------------------------------------------------------------------------
        */
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'auto_increment' => true,
            ],
            'username' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'nama' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
            ],
            'password' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'cabang_id' => [
                'type' => 'INT',
                'null' => true,
            ],
            'is_active' => [
                'type' => 'BOOLEAN',
                'default' => 1,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['online', 'offline'],
                'default' => 'offline',
            ],
            'role' => [
                'type' => 'ENUM',
                'constraint' => ['owner', 'admin', 'petugas'],
                'default' => 'petugas',
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('cabang_id', 'cabang', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('users');

        /*
        |--------------------------------------------------------------------------
        | BARANG
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
            'jenis_id' => [
                'type' => 'INT',
            ],
            'satuan_id' => [
                'type' => 'INT',
            ],
            'harga_pokok' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
            ],
            'harga_jual' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('jenis_id', 'jenis', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('satuan_id', 'satuan', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('barang');

        /*
        |--------------------------------------------------------------------------
        | INVENTORY
        |--------------------------------------------------------------------------
        */
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'auto_increment' => true,
            ],
            'barang_id' => [
                'type' => 'INT',
            ],
            'cabang_id' => [
                'type' => 'INT',
            ],
            'stock' => [
                'type' => 'INT',
                'default' => 0,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('barang_id', 'barang', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('cabang_id', 'cabang', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('inventory');

        /*
        |--------------------------------------------------------------------------
        | PEMBAYARAN
        |--------------------------------------------------------------------------
        */
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'auto_increment' => true,
            ],
            'jenis_pembayaran' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'diskon_persen' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'null' => true,
            ],
            'diskon_nominal' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'null' => true,
            ],
            'nominal_bayar' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
            ],
            'kembalian' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => 0,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('pembayaran');

        /*
        |--------------------------------------------------------------------------
        | PENJUALAN
        |--------------------------------------------------------------------------
        */
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'auto_increment' => true,
            ],
            'faktur' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'customer_id' => [
                'type' => 'INT',
                'null' => true,
            ],
            'pembayaran_id' => [
                'type' => 'INT',
            ],
            'qty_penjualan' => [
                'type' => 'INT',
            ],
            'nominal_penjualan' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
            ],
            'operator_id' => [
                'type' => 'INT',
            ],
            'cabang_id' => [
                'type' => 'INT',
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('pembayaran_id', 'pembayaran', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('operator_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('cabang_id', 'cabang', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('penjualan');

        /*
        |--------------------------------------------------------------------------
        | PENJUALAN ITEM
        |--------------------------------------------------------------------------
        */
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'auto_increment' => true,
            ],
            'penjualan_id' => [
                'type' => 'INT',
            ],
            'inventory_id' => [
                'type' => 'INT',
            ],
            'harga_satuan' => [
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
            'keterangan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('penjualan_id', 'penjualan', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('inventory_id', 'inventory', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('penjualan_item');
    }

    public function down()
    {
        $this->forge->dropTable('penjualan_item', true);
        $this->forge->dropTable('penjualan', true);
        $this->forge->dropTable('pembayaran', true);
        $this->forge->dropTable('inventory', true);
        $this->forge->dropTable('barang', true);
        $this->forge->dropTable('users', true);
        $this->forge->dropTable('satuan', true);
        $this->forge->dropTable('jenis', true);
        $this->forge->dropTable('cabang', true);
    }
}
