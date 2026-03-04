<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class DiskonTerbatas extends BaseController
{
    private function ownerOnly(): bool
    {
        return session()->get('role') === 'owner';
    }

    public function index()
    {
        if (!$this->ownerOnly()) {
            return redirect()->to(base_url('dashboard'))
                ->with('error', 'Akses ditolak. Halaman ini hanya untuk Owner.');
        }

        $db = \Config\Database::connect();

        return view('transaksi/diskon_terbatas', [
            'cabang_list' => $db->table('cabang')->select('id, nama')->orderBy('nama')->get()->getResultArray(),
            'barang_list' => $db->table('barang')->select('id, nama, harga_jual')->orderBy('nama')->get()->getResultArray(),
            'title' => 'Diskon Terbatas',
        ]);
    }

    public function list(): ResponseInterface
    {
        if (!$this->ownerOnly()) {
            return $this->response->setStatusCode(403)->setJSON(['message' => 'Forbidden']);
        }

        $db       = \Config\Database::connect();
        $cabangId = $this->request->getGet('cabang_id');
        $status   = $this->request->getGet('status');
        $search   = trim($this->request->getGet('search') ?? '');

        $builder = $db->table('diskon_terbatas dt')
            ->select([
                'dt.id',
                'dt.nama',
                'dt.cabang_id',
                'c.nama   AS nama_cabang',
                'dt.tgl_mulai',
                'dt.tgl_selesai',
                'dt.status',
                'dt.created_at',
                'u.nama AS created_by_nama',
                '(SELECT COUNT(*) FROM diskon_terbatas_item dti WHERE dti.diskon_terbatas_id = dt.id) AS jumlah_item',
            ])
            ->join('cabang c', 'c.id = dt.cabang_id', 'left')
            ->join('users u',  'u.id = dt.created_by', 'left')
            ->orderBy('dt.created_at', 'DESC');

        if ($cabangId) $builder->where('dt.cabang_id', $cabangId);
        if ($status)   $builder->where('dt.status', $status);
        if ($search)   $builder->like('dt.nama', $search);

        return $this->response->setJSON(['data' => $builder->get()->getResultArray()]);
    }

    public function detail(int $id): ResponseInterface
    {
        if (!$this->ownerOnly()) {
            return $this->response->setStatusCode(403)->setJSON(['message' => 'Forbidden']);
        }

        $db = \Config\Database::connect();

        $header = $db->table('diskon_terbatas dt')
            ->select([
                'dt.id',
                'dt.nama',
                'dt.cabang_id',
                'c.nama AS nama_cabang',
                'dt.tgl_mulai',
                'dt.tgl_selesai',
                'dt.status',
                'dt.created_at',
                'u.nama AS created_by_nama'
            ])
            ->join('cabang c', 'c.id = dt.cabang_id', 'left')
            ->join('users u',  'u.id = dt.created_by', 'left')
            ->where('dt.id', $id)
            ->get()->getRowArray();

        if (!$header) {
            return $this->response->setStatusCode(404)->setJSON(['message' => 'Not found']);
        }

        $items = $db->table('diskon_terbatas_item dti')
            ->select([
                'dti.id',
                'dti.barang_id',
                'b.nama AS nama_barang',
                'b.harga_jual',
                'dti.nominal_diskon',
                '(b.harga_jual - dti.nominal_diskon) AS harga_diskon'
            ])
            ->join('barang b', 'b.id = dti.barang_id', 'left')
            ->where('dti.diskon_terbatas_id', $id)
            ->orderBy('b.nama')
            ->get()->getResultArray();

        foreach ($items as &$item) {
            $item['harga_jual']     = (float) $item['harga_jual'];
            $item['nominal_diskon'] = (float) $item['nominal_diskon'];
            $item['harga_diskon']   = (float) $item['harga_diskon'];
        }
        unset($item);

        return $this->response->setJSON(['header' => $header, 'items' => $items]);
    }

    public function store(): ResponseInterface
    {
        if (!$this->ownerOnly()) {
            return $this->response->setStatusCode(403)->setJSON(['message' => 'Forbidden']);
        }

        $db    = \Config\Database::connect();
        $input = $this->request->getJSON(true);

        $nama       = trim($input['nama'] ?? '');
        $cabangId   = (int)   ($input['cabang_id']   ?? 0);
        $tglMulai   = $input['tgl_mulai']   ?? '';
        $tglSelesai = $input['tgl_selesai'] ?? '';
        $items      = $input['items'] ?? [];

        if (!$nama || !$cabangId || !$tglMulai || !$tglSelesai || empty($items)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Semua field wajib diisi dan minimal satu barang.']);
        }
        if ($tglMulai > $tglSelesai) {
            return $this->response->setJSON(['success' => false, 'message' => 'Tanggal mulai tidak boleh lebih besar dari tanggal selesai.']);
        }

        $db->transStart();

        $db->table('diskon_terbatas')->insert([
            'nama'        => $nama,
            'cabang_id'   => $cabangId,
            'tgl_mulai'   => $tglMulai,
            'tgl_selesai' => $tglSelesai,
            'status'      => 'aktif',
            'created_by'  => session()->get('user_id'),
            'created_at'  => date('Y-m-d H:i:s'),
        ]);
        $diskonId = $db->insertID();

        foreach ($items as $item) {
            $barangId      = (int)   ($item['barang_id']      ?? 0);
            $nominalDiskon = (float) ($item['nominal_diskon'] ?? 0);
            if (!$barangId || $nominalDiskon < 0) continue;
            $db->table('diskon_terbatas_item')->insert([
                'diskon_terbatas_id' => $diskonId,
                'barang_id'          => $barangId,
                'nominal_diskon'     => $nominalDiskon,
            ]);
        }

        $db->transComplete();

        if (!$db->transStatus()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Gagal menyimpan.']);
        }
        return $this->response->setJSON(['success' => true, 'message' => 'Periode diskon berhasil disimpan.', 'id' => $diskonId]);
    }

    public function update(int $id): ResponseInterface
    {
        if (!$this->ownerOnly()) {
            return $this->response->setStatusCode(403)->setJSON(['message' => 'Forbidden']);
        }

        $db    = \Config\Database::connect();
        $input = $this->request->getJSON(true);

        $nama       = trim($input['nama'] ?? '');
        $cabangId   = (int) ($input['cabang_id'] ?? 0);
        $tglMulai   = $input['tgl_mulai']   ?? '';
        $tglSelesai = $input['tgl_selesai'] ?? '';
        $items      = $input['items'] ?? [];
        $status     = in_array($input['status'] ?? '', ['aktif', 'nonaktif']) ? $input['status'] : 'aktif';

        if (!$nama || !$cabangId || !$tglMulai || !$tglSelesai || empty($items)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Semua field wajib diisi dan minimal satu barang.']);
        }
        if ($tglMulai > $tglSelesai) {
            return $this->response->setJSON(['success' => false, 'message' => 'Tanggal mulai tidak boleh lebih besar dari tanggal selesai.']);
        }

        $db->transStart();

        $db->table('diskon_terbatas')->where('id', $id)->update([
            'nama'        => $nama,
            'cabang_id'   => $cabangId,
            'tgl_mulai'   => $tglMulai,
            'tgl_selesai' => $tglSelesai,
            'status'      => $status,
        ]);

        $db->table('diskon_terbatas_item')->where('diskon_terbatas_id', $id)->delete();

        foreach ($items as $item) {
            $barangId      = (int)   ($item['barang_id']      ?? 0);
            $nominalDiskon = (float) ($item['nominal_diskon'] ?? 0);
            if (!$barangId || $nominalDiskon < 0) continue;
            $db->table('diskon_terbatas_item')->insert([
                'diskon_terbatas_id' => $id,
                'barang_id'          => $barangId,
                'nominal_diskon'     => $nominalDiskon,
            ]);
        }

        $db->transComplete();

        if (!$db->transStatus()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Gagal memperbarui.']);
        }
        return $this->response->setJSON(['success' => true, 'message' => 'Periode diskon berhasil diperbarui.']);
    }

    public function delete(int $id): ResponseInterface
    {
        if (!$this->ownerOnly()) {
            return $this->response->setStatusCode(403)->setJSON(['message' => 'Forbidden']);
        }
        \Config\Database::connect()->table('diskon_terbatas')->where('id', $id)->delete();
        return $this->response->setJSON(['success' => true, 'message' => 'Periode diskon berhasil dihapus.']);
    }

    public function toggle(int $id): ResponseInterface
    {
        if (!$this->ownerOnly()) {
            return $this->response->setStatusCode(403)->setJSON(['message' => 'Forbidden']);
        }
        $db  = \Config\Database::connect();
        $row = $db->table('diskon_terbatas')->where('id', $id)->get()->getRowArray();
        if (!$row) {
            return $this->response->setStatusCode(404)->setJSON(['message' => 'Not found']);
        }
        $newStatus = $row['status'] === 'aktif' ? 'nonaktif' : 'aktif';
        $db->table('diskon_terbatas')->where('id', $id)->update(['status' => $newStatus]);
        return $this->response->setJSON(['success' => true, 'status' => $newStatus]);
    }
}
