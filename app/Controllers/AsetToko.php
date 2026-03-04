<?php

namespace App\Controllers;

use App\Models\InventoryModel;
use CodeIgniter\API\ResponseTrait;

class AsetToko extends BaseController
{
    use ResponseTrait;

    protected $model;

    public function __construct()
    {
        $this->model = new InventoryModel();
    }

    public function index()
    {
        $db   = \Config\Database::connect();
        $role = session()->get('role');

        if ($role === 'petugas') {
            // Petugas hanya bisa lihat cabangnya sendiri
            $data['cabang'] = $db->table('cabang')
                ->where('id', session()->get('cabang_id'))
                ->get()->getResultArray();
        } else {
            // Owner & admin bisa filter semua cabang
            $data['cabang'] = $db->table('cabang')->get()->getResultArray();
        }

        $data['title']          = 'Inventory Aset';
        $data['role']           = $role;
        $data['user_cabang_id'] = session()->get('cabang_id');

        return view('inventory/aset_toko', $data);
    }

    public function list($cabang_id)
    {
        $role       = session()->get('role');
        $userCabang = (int) session()->get('cabang_id');

        // Guard: petugas tidak boleh akses cabang lain
        if ($role === 'petugas' && (int) $cabang_id !== $userCabang) {
            return $this->failForbidden('Akses ditolak');
        }

        $db    = \Config\Database::connect();
        $today = (new \DateTime('now', new \DateTimeZone('Asia/Jakarta')))->format('Y-m-d');

        $data = $db->query("
            SELECT
                inv.id,
                inv.barang_id,
                inv.stock,
                b.nama        AS nama_barang,
                b.harga_pokok,
                b.harga_jual,
                j.nama        AS nama_jenis,
                s.nama        AS nama_satuan,
                COALESCE(dti.nominal_diskon, 0)                AS nominal_diskon,
                CASE WHEN dti.id IS NOT NULL THEN 1 ELSE 0 END AS ada_promo
            FROM inventory inv
            JOIN barang b ON b.id = inv.barang_id
            JOIN jenis  j ON j.id = b.jenis_id
            JOIN satuan s ON s.id = b.satuan_id
            LEFT JOIN diskon_terbatas_item dti
                ON  dti.barang_id = inv.barang_id
                AND EXISTS (
                    SELECT 1 FROM diskon_terbatas dt
                    WHERE dt.id           = dti.diskon_terbatas_id
                      AND dt.cabang_id    = inv.cabang_id
                      AND dt.status       = 'aktif'
                      AND dt.tgl_mulai   <= ?
                      AND dt.tgl_selesai >= ?
                )
            WHERE inv.cabang_id = ?
            ORDER BY inv.stock DESC, j.nama ASC
        ", [$today, $today, $cabang_id])->getResultArray();

        return $this->respond(['data' => $data]);
    }

    public function getAvailableBarang($cabang_id)
    {
        // Hanya owner yang boleh import
        if (session()->get('role') !== 'owner') {
            return $this->failForbidden('Akses ditolak');
        }

        $db          = \Config\Database::connect();
        $existingIds = $this->model->where('cabang_id', $cabang_id)
            ->findColumn('barang_id') ?: [0];

        $data = $db->table('barang b')
            ->select('b.id, b.nama, j.nama as nama_jenis')
            ->join('jenis j', 'j.id = b.jenis_id')
            ->whereNotIn('b.id', $existingIds)
            ->get()->getResultArray();

        return $this->respond(['data' => $data]);
    }

    public function import()
    {
        // Hanya owner yang boleh import
        if (session()->get('role') !== 'owner') {
            return $this->failForbidden('Akses ditolak');
        }

        $rules = [
            'cabang_id'  => 'required',
            'barang_ids' => 'required',
        ];

        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $cabang_id  = $this->request->getVar('cabang_id');
        $barang_ids = $this->request->getVar('barang_ids');

        foreach ($barang_ids as $id) {
            $this->model->insert([
                'cabang_id' => $cabang_id,
                'barang_id' => $id,
                'stock'     => 0,
            ]);
        }

        return $this->respondCreated([
            'message' => count($barang_ids) . ' produk berhasil diimport',
        ]);
    }

    public function remove()
    {
        // Hanya owner yang boleh remove
        if (session()->get('role') !== 'owner') {
            return $this->failForbidden('Akses ditolak');
        }

        $id = $this->request->getPost('id');
        $this->model->delete($id);
        return $this->respond(['message' => 'Barang dihapus dari cabang']);
    }

    public function printPdf($cabang_id, string $type = 'semua')
    {
        // Hanya owner yang boleh print
        if (session()->get('role') !== 'owner') {
            return $this->failForbidden('Akses ditolak');
        }

        $db    = \Config\Database::connect();
        $today = (new \DateTime('now', new \DateTimeZone('Asia/Jakarta')))->format('Y-m-d');

        // Info cabang
        $cabang = $db->table('cabang')->where('id', $cabang_id)->get()->getRowArray();
        if (!$cabang) {
            return $this->failNotFound('Cabang tidak ditemukan');
        }

        // Query dasar sama seperti list(), + filter promo jika type=promo
        $sql = "
            SELECT
                inv.stock,
                b.nama        AS nama_barang,
                b.harga_pokok,
                b.harga_jual,
                j.nama        AS nama_jenis,
                s.nama        AS nama_satuan,
                COALESCE(dti.nominal_diskon, 0)                AS nominal_diskon,
                CASE WHEN dti.id IS NOT NULL THEN 1 ELSE 0 END AS ada_promo
            FROM inventory inv
            JOIN barang b ON b.id = inv.barang_id
            JOIN jenis  j ON j.id = b.jenis_id
            JOIN satuan s ON s.id = b.satuan_id
            LEFT JOIN diskon_terbatas_item dti
                ON  dti.barang_id = inv.barang_id
                AND EXISTS (
                    SELECT 1 FROM diskon_terbatas dt
                    WHERE dt.id           = dti.diskon_terbatas_id
                      AND dt.cabang_id    = inv.cabang_id
                      AND dt.status       = 'aktif'
                      AND dt.tgl_mulai   <= ?
                      AND dt.tgl_selesai >= ?
                )
            WHERE inv.cabang_id = ?
        ";

        $params = [$today, $today, $cabang_id];

        if ($type === 'promo') {
            $sql .= " AND dti.id IS NOT NULL";
        }

        $sql .= " ORDER BY j.nama ASC, b.nama ASC";

        $items = $db->query($sql, $params)->getResultArray();

        // Hitung total
        $totalAset   = array_sum(array_map(fn($i) => $i['stock'] * $i['harga_pokok'], $items));
        $totalMargin = array_sum(array_map(function ($i) {
            $efektif = $i['harga_jual'] - ($i['ada_promo'] ? $i['nominal_diskon'] : 0);
            return $i['stock'] * ($efektif - $i['harga_pokok']);
        }, $items));

        // Render view HTML khusus DomPDF
        $html = view('inventory/pdf_aset_toko', [
            'cabang'      => $cabang,
            'items'       => $items,
            'type'        => $type,
            'today'       => $today,
            'totalAset'   => $totalAset,
            'totalMargin' => $totalMargin,
        ]);

        // Generate PDF dengan DomPDF
        $options = new \Dompdf\Options();
        $options->set('defaultFont', 'DejaVu Sans');
        $options->set('isHtml5ParserEnabled', true);

        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        $slug     = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $cabang['nama']));
        $filename = 'aset-toko_' . ($type === 'promo' ? 'promo_' : 'semua_') . $slug . '_' . $today . '.pdf';

        // Stream langsung ke browser (false = inline/preview, true = download)
        $dompdf->stream($filename, ['Attachment' => false]);
        exit;
    }
}
