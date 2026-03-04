<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Struktur:
 *
 * diskon_terbatas          — header periode diskon
 *   id, nama, cabang_id, tgl_mulai, tgl_selesai, created_by, created_at
 *
 * diskon_terbatas_item     — detail barang yang didiskon dalam satu periode
 *   id, diskon_terbatas_id, barang_id, nominal_diskon (per satuan)
 *
 * Satu periode → satu cabang → banyak barang → nominal diskon per barang berbeda.
 */
class CreateDiskonTerbatas extends Migration
{
    private string $tHeader = 'diskon_terbatas';
    private string $tItem   = 'diskon_terbatas_item';

    // ════════════════════════════════════════════════════════════════════════
    //  UP
    // ════════════════════════════════════════════════════════════════════════
    public function up(): void
    {
        // ── diskon_terbatas (header) ─────────────────────────────────────
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            // Nama / label periode, misal "Promo Akhir Bulan Feb 2026"
            'nama' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => false,
            ],
            // Cabang yang mendapat diskon ini
            'cabang_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
            // Rentang periode berlaku
            'tgl_mulai' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'tgl_selesai' => [
                'type' => 'DATE',
                'null' => false,
            ],
            // Status aktif / nonaktif
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['aktif', 'nonaktif'],
                'default'    => 'aktif',
                'null'       => false,
            ],
            'created_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'default'    => null,
            ],
            'created_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('cabang_id');
        $this->forge->addKey(['tgl_mulai', 'tgl_selesai']);

        $this->forge->createTable($this->tHeader, true);

        // ── diskon_terbatas_item (detail per barang) ─────────────────────
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'diskon_terbatas_id' => [
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
            // Nominal potongan harga per satuan barang (bukan persen)
            'nominal_diskon' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'null'       => false,
                'default'    => 0.00,
            ],
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('diskon_terbatas_id');
        $this->forge->addKey('barang_id');
        // Satu barang hanya boleh muncul sekali dalam satu periode diskon
        $this->forge->addUniqueKey(['diskon_terbatas_id', 'barang_id']);

        $this->forge->createTable($this->tItem, true);

        // ── Foreign keys ─────────────────────────────────────────────────
        // item → header
        $this->db->query("
            ALTER TABLE `{$this->tItem}`
            ADD CONSTRAINT `fk_dti_diskon_terbatas`
                FOREIGN KEY (`diskon_terbatas_id`)
                REFERENCES `{$this->tHeader}` (`id`)
                ON DELETE CASCADE
                ON UPDATE CASCADE
        ");

        // header → cabang
        $this->db->query("
            ALTER TABLE `{$this->tHeader}`
            ADD CONSTRAINT `fk_dt_cabang`
                FOREIGN KEY (`cabang_id`)
                REFERENCES `cabang` (`id`)
                ON DELETE RESTRICT
                ON UPDATE CASCADE
        ");

        // item → barang
        $this->db->query("
            ALTER TABLE `{$this->tItem}`
            ADD CONSTRAINT `fk_dti_barang`
                FOREIGN KEY (`barang_id`)
                REFERENCES `barang` (`id`)
                ON DELETE RESTRICT
                ON UPDATE CASCADE
        ");
    }

    // ════════════════════════════════════════════════════════════════════════
    //  DOWN
    // ════════════════════════════════════════════════════════════════════════
    public function down(): void
    {
        // Drop item dulu karena ada FK ke header
        $this->db->query("
            ALTER TABLE `{$this->tItem}`
            DROP FOREIGN KEY `fk_dti_diskon_terbatas`,
            DROP FOREIGN KEY `fk_dti_barang`
        ");
        $this->db->query("
            ALTER TABLE `{$this->tHeader}`
            DROP FOREIGN KEY `fk_dt_cabang`
        ");

        $this->forge->dropTable($this->tItem,   true);
        $this->forge->dropTable($this->tHeader, true);
    }
}