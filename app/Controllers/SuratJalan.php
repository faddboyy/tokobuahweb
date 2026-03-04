<?php

namespace App\Controllers;

use App\Models\SuplierModel;
use Dompdf\Dompdf;
use Dompdf\Options;
use Picqer\Barcode\BarcodeGeneratorPNG;

class SuratJalan extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        return view('transaksi/surat_jalan', [
            'title'    => 'Transaksi Pre Order',
            'supliers' => (new SuplierModel())->findAll()
        ]);
    }

    /* -------------------------------------------------------------------------
     | 1. MULAI TRANSAKSI
     * ---------------------------------------------------------------------- */
    public function mulaiTransaksi()
    {
        $operator_id = session()->get('user_id');
        $gudang_id   = session()->get('gudang_id');

        if (!$operator_id || !$gudang_id) {
            return $this->response->setStatusCode(401)
                ->setJSON(['message' => 'Session tidak valid']);
        }

        $data       = $this->request->getJSON(true);
        $suplier_id = $data['suplier_id'] ?? null;

        if (!$suplier_id) {
            return $this->response->setStatusCode(400)
                ->setJSON(['message' => 'Supplier wajib dipilih']);
        }

        // Jika masih ada transaksi aktif, kembalikan ID-nya saja
        if (session()->get('active_surat_jalan_id')) {
            return $this->response->setJSON([
                'status'        => true,
                'surat_jalan_id'=> session()->get('active_surat_jalan_id')
            ]);
        }

        $kode = 'PO-' . date('YmdHis');

        $this->db->table('surat_jalan')->insert([
            'kode_po'       => $kode,
            'suplier_id'    => $suplier_id,
            'gudang_id'     => $gudang_id,
            'waktu_po'      => date('Y-m-d H:i:s'),
            'status'        => 'order',
            'total_nominal' => 0,           // ← kolom yang benar
            'operator_id'   => $operator_id
        ]);

        $id = $this->db->insertID();

        session()->set('active_surat_jalan_id', $id);
        session()->set('active_surat_jalan_suplier_id', $suplier_id); // simpan untuk restore view

        return $this->response->setJSON([
            'status'        => true,
            'surat_jalan_id'=> $id
        ]);
    }

    /* -------------------------------------------------------------------------
     | SATUAN LIST — endpoint: GET suratjalan/satuan-list
     * ---------------------------------------------------------------------- */
    public function satuanList()
    {
        $rows = (new \App\Models\SatuanModel())
            ->orderBy('nama', 'ASC')
            ->findAll();

        return $this->response->setJSON($rows);
    }

    /* -------------------------------------------------------------------------
     | 2. SEARCH BARANG
     * ---------------------------------------------------------------------- */
    public function searchBarang()
    {
        $q = $this->request->getGet('q');

        // Barang hanya punya satu satuan_id, langsung join ke tabel satuan
        $rows = $this->db->table('barang b')
            ->select('b.id, b.nama, b.barcode, b.satuan_id, s.nama as nama_satuan')
            ->join('satuan s', 's.id = b.satuan_id', 'left')
            ->groupStart()
                ->like('b.nama', $q)
                ->orLike('b.barcode', $q)
            ->groupEnd()
            ->limit(20)
            ->get()
            ->getResultArray();

        return $this->response->setJSON($rows);
    }

    /* -------------------------------------------------------------------------
     | 3. ADD ITEM
     * ---------------------------------------------------------------------- */
    public function addItem()
    {
        $surat_jalan_id = session()->get('active_surat_jalan_id');

        if (!$surat_jalan_id) {
            return $this->response->setStatusCode(400)
                ->setJSON(['message' => 'Klik Mulai Transaksi terlebih dahulu']);
        }

        $data = $this->request->getJSON();

        if (empty($data->barang_id) || empty($data->qty) || empty($data->harga_beli) || empty($data->satuan_id)) {
            return $this->response->setStatusCode(400)
                ->setJSON(['message' => 'Data tidak lengkap']);
        }

        $barang = $this->db->table('barang')
            ->where('id', $data->barang_id)
            ->get()->getRow();

        if (!$barang) {
            return $this->response->setStatusCode(404)
                ->setJSON(['message' => 'Barang tidak ditemukan']);
        }

        // Gunakan satuan_id yang dikirim dari view (sudah pasti = barang->satuan_id)
        $qty       = (float) $data->qty;
        $harga     = (float) $data->harga_beli;
        $satuan_id = (int)   $data->satuan_id;

        // Cek item yang sama sudah ada
        $existing = $this->db->table('surat_jalan_item')
            ->where('surat_jalan_id', $surat_jalan_id)
            ->where('barang_id', $data->barang_id)
            ->where('satuan_id', $satuan_id)        // ← kolom satuan_id
            ->get()->getRow();

        if ($existing) {
            $this->db->table('surat_jalan_item')
                ->where('id', $existing->id)
                ->update([
                    'qty'        => $existing->qty + $qty,
                    'harga_beli' => $harga
                ]);
        } else {
            $this->db->table('surat_jalan_item')->insert([
                'surat_jalan_id' => $surat_jalan_id,
                'barang_id'      => $data->barang_id,
                'satuan_id'      => $satuan_id,     // ← kolom satuan_id
                'qty'            => $qty,
                'harga_beli'     => $harga
            ]);
        }

        $this->updateTotal($surat_jalan_id);

        return $this->response->setJSON(['status' => true]);
    }

    /* -------------------------------------------------------------------------
     | 4. DETAIL (load cart)
     * ---------------------------------------------------------------------- */
    public function detail()
    {
        $surat_jalan_id = session()->get('active_surat_jalan_id');

        if (!$surat_jalan_id) {
            return $this->response->setJSON([
                'status' => false,
                'items'  => [],
                'total'  => 0
            ]);
        }

        $items = $this->db->table('surat_jalan_item sji')
            ->select('sji.id, sji.qty, sji.harga_beli, b.nama, s.nama as nama_satuan')
            ->join('barang b', 'b.id = sji.barang_id')
            ->join('satuan s', 's.id = sji.satuan_id')  // ← satuan_id
            ->where('sji.surat_jalan_id', $surat_jalan_id)
            ->get()
            ->getResultArray();

        $total = $this->getTotal($surat_jalan_id);

        return $this->response->setJSON([
            'status' => true,
            'items'  => $items,
            'total'  => $total
        ]);
    }

    /* -------------------------------------------------------------------------
     | 5. UPDATE ITEM (qty & harga)
     * ---------------------------------------------------------------------- */
    public function updateItem($id)
    {
        $data  = $this->request->getJSON(true);
        $qty   = (float) ($data['qty']        ?? 0);
        $harga = (float) ($data['harga_beli'] ?? 0);

        if ($qty < 1) {
            return $this->response->setStatusCode(400)
                ->setJSON(['message' => 'Qty minimal 1']);
        }

        $item = $this->db->table('surat_jalan_item')->where('id', $id)->get()->getRow();

        if (!$item) {
            return $this->response->setStatusCode(404)
                ->setJSON(['message' => 'Item tidak ditemukan']);
        }

        $this->db->table('surat_jalan_item')
            ->where('id', $id)
            ->update(['qty' => $qty, 'harga_beli' => $harga]);

        $this->updateTotal($item->surat_jalan_id);

        return $this->response->setJSON([
            'status' => true,
            'total'  => $this->getTotal($item->surat_jalan_id)
        ]);
    }

    /* -------------------------------------------------------------------------
     | 6. DELETE ITEM
     * ---------------------------------------------------------------------- */
    public function deleteItem($id)
    {
        $item = $this->db->table('surat_jalan_item')
            ->where('id', $id)
            ->get()->getRow();

        if (!$item) {
            return $this->response->setStatusCode(404)
                ->setJSON(['message' => 'Item tidak ditemukan']);
        }

        $this->db->table('surat_jalan_item')->where('id', $id)->delete();

        $this->updateTotal($item->surat_jalan_id);

        return $this->response->setJSON([
            'status' => true,
            'total'  => $this->getTotal($item->surat_jalan_id)
        ]);
    }

    /* -------------------------------------------------------------------------
     | 7. FINALISASI
     * ---------------------------------------------------------------------- */
    public function finalisasi()
    {
        $id = session()->get('active_surat_jalan_id');

        if (!$id) {
            return $this->response->setStatusCode(400)
                ->setJSON(['message' => 'Tidak ada transaksi aktif']);
        }

        $this->db->transBegin();
        try {
            $nomor = 'PO-' . date('YmdHis');

            $this->db->table('surat_jalan')
                ->where('id', $id)
                ->update([
                    'kode_po' => $nomor,  
                ]);

            $this->db->transCommit();

            session()->remove('active_surat_jalan_id');
            session()->remove('active_surat_jalan_suplier_id');

            return $this->response->setJSON([
                'status'   => true,
                'redirect' => base_url('suratjalan/cetak/' . $id)
            ]);
        } catch (\Throwable $e) {
            $this->db->transRollback();
            return $this->response->setStatusCode(500)
                ->setJSON(['message' => $e->getMessage()]);
        }
    }

    /* -------------------------------------------------------------------------
     | 8. BATALKAN
     * ---------------------------------------------------------------------- */
    public function batalkan($id)
    {
        $surat = $this->db->table('surat_jalan')
            ->where('id', $id)
            ->get()->getRow();

        if (!$surat) {
            return $this->response->setStatusCode(404)
                ->setJSON(['message' => 'Data tidak ditemukan']);
        }

        $this->db->table('surat_jalan')
            ->where('id', $id)
            ->update(['status' => 'dibatalkan']);

        // Bersihkan session
        if (session()->get('active_surat_jalan_id') == $id) {
            session()->remove('active_surat_jalan_id');
            session()->remove('active_surat_jalan_suplier_id');
        }

        return $this->response->setJSON([
            'status'  => true,
            'message' => 'Surat jalan dibatalkan'
        ]);
    }

    /* -------------------------------------------------------------------------
     | 9. CETAK PDF
     * ---------------------------------------------------------------------- */
    public function cetak($id)
    {
        $surat = $this->db->table('surat_jalan sj')
            ->select('sj.*, sp.nama as nama_suplier, sp.alamat as alamat_suplier, sp.telepon as telepon_suplier, g.nama as nama_gudang, u.nama as nama_operator')
            ->join('suplier sp', 'sp.id = sj.suplier_id', 'left')
            ->join('gudang_utama g', 'g.id = sj.gudang_id', 'left')
            ->join('users u', 'u.id = sj.operator_id', 'left')
            ->where('sj.id', $id)
            ->get()->getRowArray();

        if (!$surat) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $items = $this->db->table('surat_jalan_item sji')
            ->select('sji.qty, sji.harga_beli, b.nama, s.nama as satuan')
            ->join('barang b', 'b.id = sji.barang_id')
            ->join('satuan s', 's.id = sji.satuan_id')  // ← satuan_id
            ->where('sji.surat_jalan_id', $id)
            ->get()->getResultArray();

        $generator = new BarcodeGeneratorPNG();
        $barcode   = base64_encode(
            $generator->getBarcode($surat['kode_po'], $generator::TYPE_CODE_128)
        );

        $html = view('transaksi/cetak_surat_jalan', [
            'surat'  => $surat,
            'items'  => $items,
            'barcode'=> $barcode
        ]);

        $options = new Options();
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $this->response
            ->setContentType('application/pdf')
            ->setBody($dompdf->output());
    }

    /* -------------------------------------------------------------------------
     | HELPER PRIVATE
     * ---------------------------------------------------------------------- */

    /**
     * Hitung total dari item-item surat jalan
     */
    private function getTotal(int $id): float
    {
        $row = $this->db->table('surat_jalan_item')
            ->select('SUM(harga_beli) as total')   // harga_beli = subtotal borongan, bukan harga/satuan
            ->where('surat_jalan_id', $id)
            ->get()
            ->getRowArray();

        return (float) ($row['total'] ?? 0);
    }

    /**
     * Update kolom total_nominal di header surat_jalan
     * Perbaikan: kolom yang benar adalah 'total_nominal', bukan 'total'
     */
    private function updateTotal(int $id): void
    {
        $total = $this->getTotal($id);

        $this->db->table('surat_jalan')
            ->where('id', $id)
            ->update(['total_nominal' => $total]);   // ← kolom yang benar sesuai DDL
    }
}