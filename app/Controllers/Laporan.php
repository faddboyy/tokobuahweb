<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Laporan extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    /* =========================================================================
     | PENJUALAN
     * ====================================================================== */

    public function lap_penjualan()
    {
        return view('laporan/lap_penjualan', ['title' => 'Laporan Penjualan']);
    }

    public function list_penjualan()
    {
        $tgl_awal         = $this->request->getGet('tgl_awal');
        $tgl_akhir        = $this->request->getGet('tgl_akhir');
        $cabang_id_filter = $this->request->getGet('cabang_id');

        $user_role   = session()->get('role');
        $user_cabang = session()->get('cabang_id');

        $builder = $this->db->table('penjualan p')
            ->select('
                p.*,
                b.jenis_pembayaran,
                u.nama    AS nama_operator,
                c.nama    AS nama_cabang,
                cust.nama AS nama_customer
            ')
            ->join('pembayaran b',  'b.id    = p.pembayaran_id')
            ->join('users u',       'u.id    = p.operator_id')
            ->join('cabang c',      'c.id    = p.cabang_id')
            ->join('customer cust', 'cust.id = p.customer_id', 'left');

        if ($user_role === 'petugas') {
            $builder->where('p.cabang_id', $user_cabang);
        } elseif ($cabang_id_filter) {
            $builder->where('p.cabang_id', $cabang_id_filter);
        }

        if ($tgl_awal && $tgl_akhir) {
            $builder->where('DATE(p.created_at) >=', $tgl_awal)
                ->where('DATE(p.created_at) <=', $tgl_akhir);
        }

        $data = $builder->orderBy('p.id', 'DESC')->get()->getResultArray();

        $cabang_list = [];
        if ($user_role !== 'petugas') {
            $cabang_list = $this->db->table('cabang')->get()->getResultArray();
        }

        return $this->response->setJSON([
            'data'   => $data,
            'cabang' => $cabang_list,
        ]);
    }

    public function penjualan_detail($id)
    {
        return view('laporan/penjualan_detail', ['id' => $id, 'title' => 'Detail Penjualan']);
    }

    public function get_detail($id)
    {
        $header = $this->db->table('penjualan p')
            ->select('
            p.*,
            b.jenis_pembayaran, b.nominal_bayar, b.kembalian, b.diskon_nominal,
            u.nama    AS operator,
            cust.nama AS nama_customer,
            c.nama    AS nama_cabang
        ')
            ->join('cabang c',      'c.id    = p.cabang_id')
            ->join('pembayaran b',  'b.id    = p.pembayaran_id')
            ->join('users u',       'u.id    = p.operator_id')
            ->join('customer cust', 'cust.id = p.customer_id', 'left')
            ->where('p.id', $id)
            ->get()->getRowArray();

        $items = $this->db->table('penjualan_item pi')
            ->select('
            pi.*,
            b.nama AS nama_barang,
            s.nama AS satuan
        ')
            ->join('inventory i', 'i.id = pi.inventory_id')
            ->join('barang b',    'b.id = i.barang_id')
            ->join('satuan s',    's.id = b.satuan_id')
            ->where('pi.penjualan_id', $id)
            ->get()->getResultArray();

        // ── Kalkulasi ringkasan diskon ─────────────────────────────────────
        // subtotal_kotor = SUM(harga_satuan x qty) — sebelum diskon apapun
        $subtotal_kotor    = 0;
        // diskon_promo    = SUM(nominal_diskon x qty) dari item yang kena promo
        $diskon_promo      = 0;

        foreach ($items as &$item) {
            $hargaSatuan  = (float)$item['harga_satuan'];
            $qty          = (int)$item['qty'];
            $nominalDiskon = (float)($item['nominal_diskon'] ?? 0);

            $item['subtotal_kotor']      = $hargaSatuan * $qty;
            $item['ada_diskon']          = $nominalDiskon > 0;
            $item['total_diskon_item']   = $nominalDiskon * $qty;
            // harga_setelah_diskon sudah tersimpan di DB, fallback ke harga_satuan jika 0
            $item['harga_setelah_diskon'] = $item['harga_setelah_diskon'] > 0
                ? (float)$item['harga_setelah_diskon']
                : $hargaSatuan;

            $subtotal_kotor += $item['subtotal_kotor'];
            $diskon_promo   += $item['total_diskon_item'];
        }
        unset($item);

        $diskon_tambahan    = (float)($header['diskon_nominal'] ?? 0);
        $total_semua_diskon = $diskon_promo + $diskon_tambahan;

        // nominal_penjualan di header = SUM(subtotal item) = sudah setelah diskon promo
        // total_akhir = nominal_penjualan - diskon_tambahan
        $total_akhir = (float)$header['nominal_penjualan'] - $diskon_tambahan;

        return $this->response->setJSON([
            'header' => $header,
            'items'  => $items,
            'summary' => [
                'subtotal_kotor'     => $subtotal_kotor,     // sebelum diskon apapun
                'diskon_promo'       => $diskon_promo,       // dari promo per item
                'diskon_tambahan'    => $diskon_tambahan,    // dari input kasir
                'total_semua_diskon' => $total_semua_diskon, // gabungan
                'total_akhir'        => $total_akhir,        // yang harus dibayar
            ],
        ]);
    }

    /* =========================================================================
     | SURAT JALAN
     * ====================================================================== */

    public function lap_surat_jalan()
    {
        return view('laporan/lap_surat_jalan', ['title' => 'Laporan Surat Jalan']);
    }

    public function list_surat_jalan()
    {
        $tgl_awal   = $this->request->getGet('tgl_awal');
        $tgl_akhir  = $this->request->getGet('tgl_akhir');
        $suplier_id = $this->request->getGet('suplier_id');
        $gudang_id  = $this->request->getGet('gudang_id');
        $status     = $this->request->getGet('status');

        $builder = $this->db->table('surat_jalan sj')
            ->select('
                sj.id,
                sj.kode_po,
                sj.waktu_po,
                sj.status,
                sj.total_nominal,
                sp.nama AS nama_suplier,
                g.nama  AS nama_gudang,
                u.nama  AS nama_operator,
                (SELECT COUNT(*) FROM surat_jalan_item
                 WHERE surat_jalan_id = sj.id) AS jumlah_item
            ')
            ->join('suplier sp',     'sp.id = sj.suplier_id', 'left')
            ->join('gudang_utama g', 'g.id  = sj.gudang_id',  'left')
            ->join('users u',        'u.id  = sj.operator_id', 'left');

        if ($tgl_awal && $tgl_akhir) {
            $builder->where('DATE(sj.waktu_po) >=', $tgl_awal)
                ->where('DATE(sj.waktu_po) <=', $tgl_akhir);
        }
        if ($suplier_id) $builder->where('sj.suplier_id', $suplier_id);
        if ($gudang_id)  $builder->where('sj.gudang_id',  $gudang_id);
        if ($status)     $builder->where('sj.status',     $status);

        $data = $builder->orderBy('sj.id', 'DESC')->get()->getResultArray();

        $suplier_list = $this->db->table('suplier')->select('id, nama')->get()->getResultArray();
        $gudang_list  = $this->db->table('gudang_utama')->select('id, nama')->get()->getResultArray();

        return $this->response->setJSON([
            'data'    => $data,
            'suplier' => $suplier_list,
            'gudang'  => $gudang_list,
        ]);
    }

    public function surat_jalan_detail($id)
    {
        return view('laporan/surat_jalan_detail', ['id' => $id, 'title' => 'Detail Surat Jalan']);
    }

    public function get_surat_jalan_detail($id)
    {
        // ── Header surat_jalan ────────────────────────────────────────
        $header = $this->db->table('surat_jalan sj')
            ->select([
                'sj.id',
                'sj.kode_po',
                'sj.waktu_po',
                'sj.status',
                'sj.total_nominal',

                // Supplier
                'sp.nama    AS nama_suplier',
                'sp.alamat  AS alamat_suplier',
                'sp.telepon AS telepon_suplier',
                'sp.email   AS email_suplier',

                // Gudang tujuan
                'gu.nama    AS nama_gudang',

                // Operator pembuat PO
                'u.nama     AS nama_operator',
            ])
            ->join('suplier sp',      'sp.id = sj.suplier_id',  'left')
            ->join('gudang_utama gu', 'gu.id = sj.gudang_id',   'left')
            ->join('users u',         'u.id  = sj.operator_id', 'left')
            ->where('sj.id', $id)
            ->get()->getRowArray();

        if (!$header) {
            return $this->response
                ->setStatusCode(404)
                ->setJSON(['message' => 'Data tidak ditemukan']);
        }

        // ── Items surat_jalan_item ────────────────────────────────────
        $items = $this->db->table('surat_jalan_item sji')
            ->select([
                'sji.id',
                'sji.barang_id',
                'sji.qty',
                'sji.harga_beli',                          // total harga baris
                'b.nama AS nama_barang',
                'b.barcode',
                's.nama AS nama_satuan',
            ])
            ->join('barang b',  'b.id = sji.barang_id', 'left')
            ->join('satuan s',  's.id = sji.satuan_id',  'left')
            ->where('sji.surat_jalan_id', $id)
            ->orderBy('b.nama', 'ASC')
            ->get()->getResultArray();

        foreach ($items as &$item) {
            $item['qty']        = (int)   $item['qty'];
            $item['harga_beli'] = (float) $item['harga_beli'];
            // Harga per satuan untuk kemudahan tampilan di frontend
            $item['harga_satuan'] = $item['qty'] > 0
                ? round($item['harga_beli'] / $item['qty'])
                : 0;
        }
        unset($item);

        return $this->response->setJSON([
            'header' => $header,
            'items'  => $items,
        ]);
    }


    /* =========================================================================
     | BARANG MASUK — HALAMAN LIST BIASA (non-grouped)
     |    GET /laporan/barang-masuk
     * ====================================================================== */

    public function lap_barang_masuk()
    {
        return view('laporan/lap_barang_masuk', ['title' => 'Laporan Barang Masuk']);
    }

    /**
     * API JSON untuk halaman list biasa (view lama).
     * GET /laporan/list-laporan-bm
     */
    public function list_laporan_bm()
    {
        $tgl_awal  = $this->request->getGet('tgl_awal');
        $tgl_akhir = $this->request->getGet('tgl_akhir');
        $gudang_id = $this->request->getGet('gudang_id');

        $builder = $this->db->table('barang_masuk bm')
            ->select('
                bm.id,
                bm.kode_masuk,
                bm.waktu_masuk,
                bm.total_nominal,

                sj.kode_po,
                sj.status           AS status_sj,
                sp.nama             AS nama_suplier,

                g.nama              AS nama_gudang,
                u_sj.nama           AS nama_operator,

                c.nama              AS nama_cabang,
                u_bm.nama           AS operator_bm
            ')
            ->join('surat_jalan sj', 'sj.id    = bm.surat_jalan_id', 'left')
            ->join('suplier sp',     'sp.id    = sj.suplier_id',     'left')
            ->join('gudang_utama g', 'g.id     = bm.gudang_id',      'left')
            ->join('users u_sj',     'u_sj.id  = sj.operator_id',   'left')
            ->join('users u_bm',     'u_bm.id  = bm.operator_id',   'left')
            ->join('cabang c',       'c.id     = u_bm.cabang_id',   'left')
            ->notLike('bm.kode_masuk', 'BM-DRAFT');

        if ($tgl_awal && $tgl_akhir) {
            $builder->where('DATE(bm.waktu_masuk) >=', $tgl_awal)
                ->where('DATE(bm.waktu_masuk) <=', $tgl_akhir);
        }
        if ($gudang_id) {
            $builder->where('bm.gudang_id', $gudang_id);
        }

        $headers = $builder->orderBy('bm.id', 'DESC')->get()->getResultArray();

        foreach ($headers as &$row) {
            $row['items'] = $this->_getItemsBM((int) $row['id']);
        }
        unset($row);

        $gudang_list = $this->db->table('gudang_utama')->select('id, nama')->get()->getResultArray();

        return $this->response->setJSON([
            'data'   => $headers,
            'gudang' => $gudang_list,
        ]);
    }

    /* =========================================================================
     | BARANG MASUK — LAPORAN PER CABANG / TOKO
     |    GET /laporan/barang-masuk-cabang
     * ====================================================================== */

    /**
     * Halaman laporan barang masuk per cabang/toko.
     */
    public function lap_barang_masuk_cabang()
    {
        return view('laporan/lap_barang_masuk_cabang', [
            'title' => 'Laporan Barang Masuk Per Cabang',
        ]);
    }

    /**
     * API JSON — daftar barang masuk flat, siap di-group per cabang di Vue.
     *
     * GET /laporan/list-barang-masuk
     *     ?tgl_awal=YYYY-MM-DD
     *     &tgl_akhir=YYYY-MM-DD
     *     &gudang_id=              (opsional — filter per gudang asal)
     *     &cabang_id=              (opsional — filter per toko/cabang penerima)
     *
     * Kedua filter gudang_id dan cabang_id dapat digunakan bersamaan.
     *
     * Kolom yang dikembalikan per baris:
     *   id, kode_masuk, waktu_masuk, total_nominal
     *   surat_jalan_id, kode_po, status_sj, nama_suplier
     *   gudang_id, nama_gudang          ← gudang asal kiriman
     *   operator_sj                     ← operator pembuat surat jalan
     *   cabang_id, nama_cabang          ← toko/cabang penerima (via users BM)
     *   operator_bm                     ← operator yang terima barang masuk
     *   items[]                         ← detail barang
     */
    public function list_barang_masuk()
    {
        $tgl_awal         = $this->request->getGet('tgl_awal');
        $tgl_akhir        = $this->request->getGet('tgl_akhir');
        $gudang_id        = $this->request->getGet('gudang_id');   // filter per gudang
        $cabang_id_filter = $this->request->getGet('cabang_id');   // filter per toko

        $user_role      = session()->get('role');
        $user_cabang_id = session()->get('cabang_id');

        $builder = $this->db->table('barang_masuk bm')
            ->select('
                bm.id,
                bm.kode_masuk,
                bm.waktu_masuk,
                bm.total_nominal,

                sj.id               AS surat_jalan_id,
                sj.kode_po,
                sj.status           AS status_sj,
                sp.nama             AS nama_suplier,

                g.id                AS gudang_id,
                g.nama              AS nama_gudang,
                u_sj.nama           AS operator_sj,

                u_bm.cabang_id      AS cabang_id,
                c.nama              AS nama_cabang,
                u_bm.nama           AS operator_bm
            ')
            ->join('surat_jalan sj', 'sj.id    = bm.surat_jalan_id', 'left')
            ->join('suplier sp',     'sp.id    = sj.suplier_id',     'left')
            ->join('gudang_utama g', 'g.id     = bm.gudang_id',      'left')
            ->join('users u_sj',     'u_sj.id  = sj.operator_id',   'left')
            ->join('users u_bm',     'u_bm.id  = bm.operator_id')           // INNER JOIN
            ->join('cabang c',       'c.id     = u_bm.cabang_id',   'left')
            ->notLike('bm.kode_masuk', 'BM-DRAFT');

        // ── Role restriction ────────────────────────────────────────────────
        // Petugas hanya boleh lihat cabangnya sendiri; filter cabang dari URL diabaikan
        if ($user_role === 'petugas') {
            $builder->where('u_bm.cabang_id', $user_cabang_id);
        } elseif ($cabang_id_filter) {
            // Owner / admin bisa filter per toko
            $builder->where('u_bm.cabang_id', (int) $cabang_id_filter);
        }

        // ── Filter per gudang asal (bisa dikombinasikan dengan filter toko) ─
        if ($gudang_id) {
            $builder->where('bm.gudang_id', (int) $gudang_id);
        }

        // ── Filter rentang tanggal ──────────────────────────────────────────
        if ($tgl_awal && $tgl_akhir) {
            $builder->where('DATE(bm.waktu_masuk) >=', $tgl_awal)
                ->where('DATE(bm.waktu_masuk) <=', $tgl_akhir);
        }

        // Urut per nama cabang (untuk grouping di Vue), lalu waktu terbaru
        $headers = $builder
            ->orderBy('c.nama',         'ASC')
            ->orderBy('bm.waktu_masuk', 'DESC')
            ->get()->getResultArray();

        // Lampirkan detail item per barang masuk
        foreach ($headers as &$row) {
            $row['items'] = $this->_getItemsBM(
                (int) $row['id'],
                isset($row['surat_jalan_id']) ? (int) $row['surat_jalan_id'] : null
            );
        }
        unset($row);

        // ── Dropdown referensi filter ───────────────────────────────────────
        $gudang_list = $this->db->table('gudang_utama')
            ->select('id, nama')
            ->orderBy('nama', 'ASC')
            ->get()->getResultArray();

        // Cabang list hanya untuk owner/admin; petugas tidak perlu dropdown
        $cabang_list = [];
        if ($user_role !== 'petugas') {
            $cabang_list = $this->db->table('cabang')
                ->select('id, nama')
                ->orderBy('nama', 'ASC')
                ->get()->getResultArray();
        }

        return $this->response->setJSON([
            'data'   => $headers,
            'gudang' => $gudang_list,
            'cabang' => $cabang_list,
        ]);
    }

    /* =========================================================================
     | BARANG MASUK — DETAIL SATU TRANSAKSI
     |    GET /laporan/barang-masuk-detail/{id}
     * ====================================================================== */

    public function barang_masuk_detail($id)
    {
        return view('laporan/barang_masuk_detail', ['id' => (int) $id]);
    }

    /**
     * API JSON — header + items untuk satu barang masuk.
     *
     * Header berisi:
     *   Semua kolom bm.* + kode_po, nama_suplier, alamat_suplier, telepon_suplier,
     *   nama_gudang, operator_sj, cabang_id, nama_cabang, operator_bm
     *
     * Items berisi perbandingan qty aktual vs qty surat jalan + selisih.
     *
     * GET /laporan/get-barang-masuk-detail/{id}
     */
    public function get_barang_masuk_detail($id)
    {
        $header = $this->db->table('barang_masuk bm')
            ->select('
                bm.*,

                sj.kode_po,
                sj.id               AS surat_jalan_id,
                sj.status           AS status_sj,
                sp.nama             AS nama_suplier,
                sp.alamat           AS alamat_suplier,
                sp.telepon          AS telepon_suplier,

                g.nama              AS nama_gudang,
                u_sj.nama           AS operator_sj,

                u_bm.cabang_id      AS cabang_id,
                c.nama              AS nama_cabang,
                u_bm.nama           AS operator_bm
            ')
            ->join('surat_jalan sj', 'sj.id    = bm.surat_jalan_id', 'left')
            ->join('suplier sp',     'sp.id    = sj.suplier_id',     'left')
            ->join('gudang_utama g', 'g.id     = bm.gudang_id',      'left')
            ->join('users u_sj',     'u_sj.id  = sj.operator_id',   'left')
            ->join('users u_bm',     'u_bm.id  = bm.operator_id',   'left')
            ->join('cabang c',       'c.id     = u_bm.cabang_id',   'left')
            ->where('bm.id', (int) $id)
            ->get()->getRowArray();

        if (!$header) {
            return $this->response->setStatusCode(404)
                ->setJSON(['message' => 'Data tidak ditemukan.']);
        }

        $items = $this->_getItemsBM(
            (int) $id,
            isset($header['surat_jalan_id']) ? (int) $header['surat_jalan_id'] : null,
            withSelisih: true
        );

        return $this->response->setJSON([
            'header' => $header,
            'items'  => $items,
        ]);
    }

    /* =========================================================================
     | PRIVATE HELPERS
     * ====================================================================== */

    /**
     * Ambil item barang masuk beserta data perbandingan dari surat jalan.
     *
     * Kolom yang dikembalikan:
     *   id, barang_id, nama_barang
     *   qty_bm              — qty aktual tersimpan di barang_masuk_item
     *   harga_pokok_satuan  — dari barang_masuk_item
     *   satuan_bm_id, satuan_bm — satuan master barang
     *   subtotal
     *   qty_sj              — qty di surat_jalan_item (null jika tidak ada SJ)
     *   satuan_sj_id, satuan_sj — satuan di SJ
     *   harga_beli_sj       — harga dari surat_jalan_item (referensi supplier)
     *   selisih             — qty_bm - qty_sj (hanya jika $withSelisih = true)
     *
     * @param  int       $barang_masuk_id
     * @param  int|null  $sj_id           Kirim langsung jika sudah diketahui (hindari query tambahan)
     * @param  bool      $withSelisih     Tambahkan kolom selisih qty
     * @return array
     */
    private function _getItemsBM(int $barang_masuk_id, ?int $sj_id = null, bool $withSelisih = false): array
    {
        // Resolve sj_id jika belum diberikan
        if ($sj_id === null) {
            $bm    = $this->db->table('barang_masuk')->where('id', $barang_masuk_id)->get()->getRow();
            $sj_id = $bm->surat_jalan_id ?? null;
        }

        // Ambil item BM + satuan master barang
        $items = $this->db->table('barang_masuk_item bmi')
            ->select('
                bmi.id,
                bmi.barang_id,
                bmi.qty             AS qty_bm,
                bmi.harga_pokok_satuan,
                bmi.subtotal,
                b.nama              AS nama_barang,
                b.harga_pokok,
                sb.id               AS satuan_bm_id,
                sb.nama             AS satuan_bm
            ')
            ->join('barang b',  'b.id  = bmi.barang_id')
            ->join('satuan sb', 'sb.id = b.satuan_id')
            ->where('bmi.barang_masuk_id', $barang_masuk_id)
            ->get()->getResultArray();

        // Ambil semua baris SJ sekaligus (hindari N+1)
        $sj_map = [];
        if ($sj_id) {
            $sj_rows = $this->db->table('surat_jalan_item sji')
                ->select('
                    sji.barang_id,
                    sji.qty        AS qty_sj,
                    sji.harga_beli AS harga_beli_sj,
                    sji.satuan_id  AS satuan_sj_id,
                    s.nama         AS satuan_sj
                ')
                ->join('satuan s', 's.id = sji.satuan_id')
                ->where('sji.surat_jalan_id', $sj_id)
                ->get()->getResultArray();

            foreach ($sj_rows as $r) {
                $sj_map[$r['barang_id']] = $r;
            }
        }

        // Gabungkan data SJ ke tiap item
        foreach ($items as &$item) {
            $sj = $sj_map[$item['barang_id']] ?? null;

            $item['qty_sj']        = $sj['qty_sj']        ?? null;
            $item['satuan_sj_id']  = $sj['satuan_sj_id']  ?? null;
            $item['satuan_sj']     = $sj['satuan_sj']      ?? '-';
            $item['harga_beli_sj'] = $sj['harga_beli_sj'] ?? null;

            if ($withSelisih) {
                $item['selisih'] = ($item['qty_sj'] !== null)
                    ? ((int) $item['qty_bm'] - (int) $item['qty_sj'])
                    : null;
            }
        }
        unset($item);

        return $items;
    }
}
