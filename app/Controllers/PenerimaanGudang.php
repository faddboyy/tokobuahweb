<?php

namespace App\Controllers;

class PenerimaanGudang extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        return view('transaksi/penerimaan_gudang', ['title' => 'Penerimaan Gudang']);
    }

    private function resolveGudangId(int $userId): ?int
    {
        $gudang = $this->db->table('gudang_utama')->where('mandor_id', $userId)->get()->getRow();
        if ($gudang) return (int) $gudang->id;

        $gudang = $this->db->table('gudang_utama')->orderBy('id', 'ASC')->limit(1)->get()->getRow();
        return $gudang ? (int) $gudang->id : null;
    }

    /* GET /penerimaan-gudang/scan-po?kode=PO-xxx */
    public function scanPo()
    {
        $kode = $this->request->getGet('kode');

        if (!$kode) {
            return $this->response->setStatusCode(400)->setJSON(['message' => 'Kode PO wajib diisi']);
        }

        $surat = $this->db->table('surat_jalan sj')
            ->select('sj.id, sj.kode_po, sj.status, sj.waktu_po,
                      sp.nama AS nama_suplier, g.nama AS nama_gudang, sj.gudang_id')
            ->join('suplier sp', 'sp.id = sj.suplier_id', 'left')
            ->join('gudang_utama g', 'g.id = sj.gudang_id', 'left')
            ->where('sj.kode_po', $kode)
            ->get()->getRowArray();

        if (!$surat) {
            return $this->response->setStatusCode(404)->setJSON(['message' => 'Kode PO tidak ditemukan']);
        }
        if ($surat['status'] === 'dibatalkan') {
            return $this->response->setStatusCode(422)->setJSON(['message' => 'Surat jalan ini sudah dibatalkan']);
        }
        if ($surat['status'] === 'selesai') {
            return $this->response->setStatusCode(422)->setJSON(['message' => 'Surat jalan ini sudah selesai diterima']);
        }

        $items = $this->db->table('surat_jalan_item sji')
            ->select('sji.id, sji.barang_id, sji.satuan_id, sji.qty, b.nama AS nama_barang, s.nama AS nama_satuan')
            ->join('barang b', 'b.id = sji.barang_id')
            ->join('satuan s', 's.id = sji.satuan_id')
            ->where('sji.surat_jalan_id', $surat['id'])
            ->get()->getResultArray();

        if (empty($items)) {
            return $this->response->setStatusCode(422)->setJSON(['message' => 'Surat jalan tidak memiliki item']);
        }

        $payload = ['status' => true, 'surat' => $surat, 'items' => $items];
        session()->set('pg_scan', ['kode' => $kode, 'payload' => $payload]);

        return $this->response->setJSON($payload);
    }

    /* GET /penerimaan-gudang/session-scan */
    public function sessionScan()
    {
        $scan = session()->get('pg_scan');
        if (!$scan) return $this->response->setJSON(['status' => false]);
        return $this->response->setJSON($scan['payload']);
    }

    /* POST /penerimaan-gudang/clear-session */
    public function clearSession()
    {
        session()->remove('pg_scan');
        return $this->response->setJSON(['status' => true]);
    }

    /* POST /penerimaan-gudang/simpan */
    public function simpan()
    {
        $operator_id = (int) session()->get('user_id');
        if (!$operator_id) {
            return $this->response->setStatusCode(401)->setJSON(['message' => 'Session tidak valid, silakan login ulang']);
        }

        $gudang_id = $this->resolveGudangId($operator_id);
        if (!$gudang_id) {
            return $this->response->setStatusCode(422)->setJSON(['message' => 'Gudang tidak ditemukan untuk operator ini']);
        }

        $data           = $this->request->getJSON(true);
        $surat_jalan_id = $data['surat_jalan_id'] ?? null;
        $kode_supplier  = trim($data['kode_supplier'] ?? '');
        $items          = $data['items'] ?? [];

        if (!$surat_jalan_id) {
            return $this->response->setStatusCode(400)->setJSON(['message' => 'Data surat jalan tidak lengkap']);
        }
        if (!$kode_supplier) {
            return $this->response->setStatusCode(400)->setJSON(['message' => 'Kode supplier wajib diisi']);
        }
        if (empty($items)) {
            return $this->response->setStatusCode(400)->setJSON(['message' => 'Tidak ada item penerimaan']);
        }
        foreach ($items as $item) {
            if ((int)($item['qty_diterima'] ?? 0) < 1) {
                return $this->response->setStatusCode(400)->setJSON(['message' => 'Qty diterima setiap barang minimal 1']);
            }
        }

        $surat = $this->db->table('surat_jalan')->where('id', $surat_jalan_id)->get()->getRow();
        if (!$surat) {
            return $this->response->setStatusCode(404)->setJSON(['message' => 'Surat jalan tidak ditemukan']);
        }
        if (in_array($surat->status, ['selesai', 'dibatalkan'])) {
            return $this->response->setStatusCode(422)->setJSON(['message' => 'Surat jalan sudah diproses atau dibatalkan']);
        }

        $dup = $this->db->table('penerimaan_gudang')
            ->where('kode_supplier', $kode_supplier)->where('status !=', 'dibatalkan')
            ->get()->getRow();
        if ($dup) {
            return $this->response->setStatusCode(422)
                ->setJSON(['message' => 'Kode supplier "' . $kode_supplier . '" sudah digunakan']);
        }

        $this->db->transBegin();
        try {
            $kode_penerimaan  = 'PG-' . date('YmdHis');
            $waktu_penerimaan = date('Y-m-d H:i:s');

            $this->db->table('penerimaan_gudang')->insert([
                'kode_penerimaan'  => $kode_penerimaan,
                'surat_jalan_id'   => $surat_jalan_id,
                'kode_supplier'    => $kode_supplier,
                'gudang_id'        => $gudang_id,
                'waktu_penerimaan' => $waktu_penerimaan,
                'operator_id'      => $operator_id,
                'status'           => 'digudang',
                'created_at'       => $waktu_penerimaan,
            ]);

            $penerimaan_id = $this->db->insertID();

            // Ambil semua sj_item sekaligus untuk lookup satuan_id yang akurat
            // — tidak bergantung pada frontend yang mungkin tidak mengirim satuan_id
            $sjItems = $this->db->table('surat_jalan_item')
                ->where('surat_jalan_id', $surat_jalan_id)
                ->get()->getResultArray();
            $satuanMap = [];
            foreach ($sjItems as $sji) {
                $satuanMap[(int)$sji['barang_id']] = (int)$sji['satuan_id'];
            }

            foreach ($items as $item) {
                $qty_diterima = (int) $item['qty_diterima'];
                $qty_dipesan  = (int)($item['qty_dipesan'] ?? 1);
                $barang_id    = (int) $item['barang_id'];

                $this->db->table('penerimaan_gudang_item')->insert([
                    'penerimaan_gudang_id' => $penerimaan_id,
                    'barang_id'            => $barang_id,
                    'qty_dipesan'          => $qty_dipesan,
                    'qty_diterima'         => $qty_diterima,
                ]);

                $stok = $this->db->table('stok_gudang')
                    ->where('gudang_id', $gudang_id)
                    ->where('barang_id', $barang_id)
                    ->get()->getRow();

                if ($stok) {
                    $this->db->table('stok_gudang')
                        ->where('id', $stok->id)
                        ->update(['stock' => $stok->stock + $qty_diterima]);
                } else {
                    // satuan_id diambil dari surat_jalan_item di DB (bukan dari payload frontend)
                    $satuan_id = $satuanMap[$barang_id] ?? null;

                    $this->db->table('stok_gudang')->insert([
                        'gudang_id'  => $gudang_id,
                        'barang_id'  => $barang_id,
                        'satuan_id'  => $satuan_id,
                        'stock'      => $qty_diterima,
                    ]);
                }
            }

            $this->db->table('surat_jalan')->where('id', $surat_jalan_id)->update(['status' => 'selesai']);
            $this->db->transCommit();
            session()->remove('pg_scan');

            return $this->response->setJSON([
                'status'          => true,
                'kode_penerimaan' => $kode_penerimaan,
                'penerimaan_id'   => $penerimaan_id,
            ]);
        } catch (\Throwable $e) {
            $this->db->transRollback();
            return $this->response->setStatusCode(500)->setJSON(['message' => $e->getMessage()]);
        }
    }

    /* GET /penerimaan-gudang/detail/:id */
    public function detail($penerimaan_id)
    {
        $penerimaan = $this->db->table('penerimaan_gudang')->where('id', $penerimaan_id)->get()->getRow();
        if (!$penerimaan) {
            return $this->response->setStatusCode(404)->setJSON(['message' => 'Data tidak ditemukan']);
        }

        $items = $this->db->table('penerimaan_gudang_item pgi')
            ->select('pgi.id, pgi.barang_id, pgi.qty_dipesan, pgi.qty_diterima, b.nama AS nama_barang')
            ->join('barang b', 'b.id = pgi.barang_id')
            ->where('pgi.penerimaan_gudang_id', $penerimaan_id)
            ->get()->getResultArray();

        return $this->response->setJSON(['status' => true, 'items' => $items]);
    }

    /* POST /penerimaan-gudang/update-item/:id */
    public function updateItem($item_id)
    {
        $data         = $this->request->getJSON(true);
        $qty_diterima = (int)($data['qty_diterima'] ?? 0);

        if ($qty_diterima < 1) {
            return $this->response->setStatusCode(400)->setJSON(['message' => 'Qty diterima minimal 1']);
        }

        $item = $this->db->table('penerimaan_gudang_item pgi')
            ->select('pgi.*, pg.gudang_id')
            ->join('penerimaan_gudang pg', 'pg.id = pgi.penerimaan_gudang_id')
            ->where('pgi.id', $item_id)->get()->getRow();

        if (!$item) {
            return $this->response->setStatusCode(404)->setJSON(['message' => 'Item tidak ditemukan']);
        }
        if ($qty_diterima > $item->qty_dipesan) {
            return $this->response->setStatusCode(422)
                ->setJSON(['message' => 'Qty diterima tidak boleh melebihi qty dipesan (' . $item->qty_dipesan . ')']);
        }

        $this->db->transBegin();
        try {
            $selisih = $qty_diterima - (int)$item->qty_diterima;
            $this->db->table('penerimaan_gudang_item')->where('id', $item_id)
                ->update(['qty_diterima' => $qty_diterima]);

            if ($selisih !== 0) {
                $stok = $this->db->table('stok_gudang')
                    ->where('gudang_id', $item->gudang_id)->where('barang_id', $item->barang_id)
                    ->get()->getRow();
                if ($stok) {
                    $this->db->table('stok_gudang')->where('id', $stok->id)
                        ->update(['stock' => max(0, $stok->stock + $selisih)]);
                }
            }

            $this->db->transCommit();
            return $this->response->setJSON(['status' => true, 'message' => 'Qty diperbarui']);
        } catch (\Throwable $e) {
            $this->db->transRollback();
            return $this->response->setStatusCode(500)->setJSON(['message' => $e->getMessage()]);
        }
    }

    /* POST /penerimaan-gudang/batalkan/:id */
    public function batalkan($penerimaan_id)
    {
        $penerimaan = $this->db->table('penerimaan_gudang')->where('id', $penerimaan_id)->get()->getRow();

        if (!$penerimaan) {
            return $this->response->setStatusCode(404)->setJSON(['message' => 'Data penerimaan tidak ditemukan']);
        }
        if ($penerimaan->status === 'dibatalkan') {
            return $this->response->setStatusCode(422)->setJSON(['message' => 'Penerimaan ini sudah dibatalkan sebelumnya']);
        }

        $items = $this->db->table('penerimaan_gudang_item')
            ->where('penerimaan_gudang_id', $penerimaan_id)->get()->getResultArray();

        $this->db->transBegin();
        try {
            foreach ($items as $item) {
                $stok = $this->db->table('stok_gudang')
                    ->where('gudang_id', $penerimaan->gudang_id)->where('barang_id', $item['barang_id'])
                    ->get()->getRow();
                if ($stok) {
                    $this->db->table('stok_gudang')->where('id', $stok->id)
                        ->update(['stock' => max(0, $stok->stock - (int)$item['qty_diterima'])]);
                }
            }

            $this->db->table('penerimaan_gudang')->where('id', $penerimaan_id)->update(['status' => 'dibatalkan']);
            $this->db->table('surat_jalan')->where('id', $penerimaan->surat_jalan_id)->update(['status' => 'order']);

            $this->db->transCommit();
            return $this->response->setJSON(['status' => true, 'message' => 'Penerimaan berhasil dibatalkan.']);
        } catch (\Throwable $e) {
            $this->db->transRollback();
            return $this->response->setStatusCode(500)->setJSON(['message' => $e->getMessage()]);
        }
    }
}
