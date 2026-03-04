<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Refactor tabel barang_masuk & barang_masuk_item:
 *
 * barang_masuk:
 *   - DROP  total_nominal
 *   - DROP  gudang_id  (beserta FK-nya jika ada)
 *   - DROP  penerimaan_gudang_id  (beserta FK-nya jika ada)
 *   - ADD   pengiriman_gudang_id  INT UNSIGNED NULL
 *
 * barang_masuk_item:
 *   - DROP  aktual_nominal
 *   - DROP  satuan_kirim   (teks bebas → diganti satuan dari stok_gudang)
 *   - DROP  satuan_simpan  (teks bebas → diganti satuan dari stok_gudang)
 */
class RefactorBarangMasuk extends Migration
{
    // ── nama tabel ──────────────────────────────────────────────────────────
    private string $tBarangMasuk     = 'barang_masuk';
    private string $tBarangMasukItem = 'barang_masuk_item';

    // ── helper: cek apakah kolom ada ────────────────────────────────────────
    private function columnExists(string $table, string $column): bool
    {
        $query = $this->db->query(
            "SELECT COUNT(*) AS cnt
               FROM information_schema.COLUMNS
              WHERE TABLE_SCHEMA = DATABASE()
                AND TABLE_NAME   = ?
                AND COLUMN_NAME  = ?",
            [$table, $column]
        );
        return (int) $query->getRow()->cnt > 0;
    }

    // ── helper: cek apakah FK (constraint) ada ──────────────────────────────
    private function fkExists(string $table, string $constraint): bool
    {
        $query = $this->db->query(
            "SELECT COUNT(*) AS cnt
               FROM information_schema.TABLE_CONSTRAINTS
              WHERE TABLE_SCHEMA     = DATABASE()
                AND TABLE_NAME       = ?
                AND CONSTRAINT_NAME  = ?
                AND CONSTRAINT_TYPE  = 'FOREIGN KEY'",
            [$table, $constraint]
        );
        return (int) $query->getRow()->cnt > 0;
    }

    // ── helper: cek apakah index/key ada ────────────────────────────────────
    private function indexExists(string $table, string $keyName): bool
    {
        $query = $this->db->query(
            "SELECT COUNT(*) AS cnt
               FROM information_schema.STATISTICS
              WHERE TABLE_SCHEMA = DATABASE()
                AND TABLE_NAME   = ?
                AND INDEX_NAME   = ?",
            [$table, $keyName]
        );
        return (int) $query->getRow()->cnt > 0;
    }

    // ════════════════════════════════════════════════════════════════════════
    //  UP
    // ════════════════════════════════════════════════════════════════════════
    public function up(): void
    {
        // ── barang_masuk ────────────────────────────────────────────────────

        // 1. Hapus FK penerimaan_gudang_id jika ada
        foreach (['barang_masuk_penerimaan_gudang_id_foreign', 'penerimaan_gudang_id'] as $fk) {
            if ($this->fkExists($this->tBarangMasuk, $fk)) {
                $this->db->query("ALTER TABLE `{$this->tBarangMasuk}` DROP FOREIGN KEY `{$fk}`");
            }
        }

        // 2. Hapus FK gudang_id jika ada
        foreach (['barang_masuk_gudang_id_foreign', 'gudang_id'] as $fk) {
            if ($this->fkExists($this->tBarangMasuk, $fk)) {
                $this->db->query("ALTER TABLE `{$this->tBarangMasuk}` DROP FOREIGN KEY `{$fk}`");
            }
        }

        // 3. Hapus index penerimaan_gudang_id jika ada
        foreach (['barang_masuk_penerimaan_gudang_id_foreign', 'penerimaan_gudang_id'] as $idx) {
            if ($this->indexExists($this->tBarangMasuk, $idx)) {
                $this->db->query("ALTER TABLE `{$this->tBarangMasuk}` DROP INDEX `{$idx}`");
            }
        }

        // 4. Hapus index gudang_id jika ada
        foreach (['barang_masuk_gudang_id_foreign', 'gudang_id'] as $idx) {
            if ($this->indexExists($this->tBarangMasuk, $idx)) {
                $this->db->query("ALTER TABLE `{$this->tBarangMasuk}` DROP INDEX `{$idx}`");
            }
        }

        // 5. DROP kolom total_nominal
        if ($this->columnExists($this->tBarangMasuk, 'total_nominal')) {
            $this->forge->dropColumn($this->tBarangMasuk, 'total_nominal');
        }

        // 6. DROP kolom gudang_id
        if ($this->columnExists($this->tBarangMasuk, 'gudang_id')) {
            $this->forge->dropColumn($this->tBarangMasuk, 'gudang_id');
        }

        // 7. DROP kolom penerimaan_gudang_id
        if ($this->columnExists($this->tBarangMasuk, 'penerimaan_gudang_id')) {
            $this->forge->dropColumn($this->tBarangMasuk, 'penerimaan_gudang_id');
        }

        // 8. ADD kolom pengiriman_gudang_id  (setelah kode_masuk)
        if (!$this->columnExists($this->tBarangMasuk, 'pengiriman_gudang_id')) {
            $this->forge->addColumn($this->tBarangMasuk, [
                'pengiriman_gudang_id' => [
                    'type'       => 'INT',
                    'constraint' => 10,
                    'unsigned'   => true,
                    'null'       => true,
                    'default'    => null,
                    'after'      => 'kode_masuk',
                ],
            ]);

            // index biasa untuk performa query
            $this->db->query(
                "ALTER TABLE `{$this->tBarangMasuk}`
                 ADD INDEX `barang_masuk_pengiriman_gudang_id_index` (`pengiriman_gudang_id`)"
            );
        }

        // ── barang_masuk_item ───────────────────────────────────────────────

        // 9. DROP aktual_nominal
        if ($this->columnExists($this->tBarangMasukItem, 'aktual_nominal')) {
            $this->forge->dropColumn($this->tBarangMasukItem, 'aktual_nominal');
        }
    }

    // ════════════════════════════════════════════════════════════════════════
    //  DOWN  (rollback)
    // ════════════════════════════════════════════════════════════════════════
    public function down(): void
    {
        // ── barang_masuk ────────────────────────────────────────────────────

        // Hapus pengiriman_gudang_id (index dulu)
        if ($this->indexExists($this->tBarangMasuk, 'barang_masuk_pengiriman_gudang_id_index')) {
            $this->db->query(
                "ALTER TABLE `{$this->tBarangMasuk}`
                 DROP INDEX `barang_masuk_pengiriman_gudang_id_index`"
            );
        }
        if ($this->columnExists($this->tBarangMasuk, 'pengiriman_gudang_id')) {
            $this->forge->dropColumn($this->tBarangMasuk, 'pengiriman_gudang_id');
        }

        // Kembalikan penerimaan_gudang_id
        if (!$this->columnExists($this->tBarangMasuk, 'penerimaan_gudang_id')) {
            $this->forge->addColumn($this->tBarangMasuk, [
                'penerimaan_gudang_id' => [
                    'type'       => 'INT',
                    'null'       => true,
                    'default'    => null,
                    'after'      => 'kode_masuk',
                ],
            ]);
        }

        // Kembalikan gudang_id
        if (!$this->columnExists($this->tBarangMasuk, 'gudang_id')) {
            $this->forge->addColumn($this->tBarangMasuk, [
                'gudang_id' => [
                    'type'       => 'INT',
                    'null'       => false,
                    'default'    => 0,
                    'after'      => 'penerimaan_gudang_id',
                ],
            ]);
        }

        // Kembalikan total_nominal
        if (!$this->columnExists($this->tBarangMasuk, 'total_nominal')) {
            $this->forge->addColumn($this->tBarangMasuk, [
                'total_nominal' => [
                    'type'       => 'DECIMAL',
                    'constraint' => '15,2',
                    'null'       => false,
                    'default'    => 0.00,
                    'after'      => 'gudang_id',
                ],
            ]);
        }

        // ── barang_masuk_item ───────────────────────────────────────────────

        // Kembalikan aktual_nominal
        if (!$this->columnExists($this->tBarangMasukItem, 'aktual_nominal')) {
            $this->forge->addColumn($this->tBarangMasukItem, [
                'aktual_nominal' => [
                    'type'       => 'DECIMAL',
                    'constraint' => '15,2',
                    'null'       => false,
                    'default'    => 0.00,
                ],
            ]);
        }
    }
}