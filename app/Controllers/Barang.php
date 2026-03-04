<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;

class Barang extends ResourceController
{
    protected $modelName = 'App\Models\BarangModel';
    protected $format    = 'json';

    public function index()
    {
        return view('master/barang', ['title' => 'Manajemen Barang']);
    }

    public function list()
    {
        $db   = \Config\Database::connect();
        $data = $db->table('barang b')
            ->select('b.*, j.nama as nama_jenis, s.nama as nama_satuan')
            ->join('jenis j', 'j.id = b.jenis_id')
            ->join('satuan s', 's.id = b.satuan_id')
            ->orderBy('b.id', 'DESC')
            ->get()
            ->getResultArray();

        return $this->respond(['status' => true, 'data' => $data]);
    }

    public function store()
    {
        $rules = [
            'nama' => [
                'rules'  => 'required|min_length[3]|is_unique[barang.nama]',
                'errors' => [
                    'required'   => 'Nama barang harus diisi',
                    'min_length' => 'Nama barang minimal 3 karakter',
                    'is_unique'  => 'Nama barang sudah terdaftar',
                ],
            ],
            'barcode' => [
                'rules'  => 'permit_empty|min_length[5]|is_unique[barang.barcode]',
                'errors' => [
                    'min_length' => 'Barcode terlalu pendek',
                    'is_unique'  => 'Barcode sudah digunakan',
                ],
            ],
            'jenis_id' => [
                'rules'  => 'required|numeric',
                'errors' => [
                    'required' => 'Jenis barang harus dipilih',
                    'numeric'  => 'Jenis barang tidak valid',
                ],
            ],
            'satuan_id' => [
                'rules'  => 'required|numeric',
                'errors' => [
                    'required' => 'Satuan barang harus dipilih',
                    'numeric'  => 'Satuan barang tidak valid',
                ],
            ],
            'harga_pokok' => [
                'rules'  => 'required|numeric|greater_than_equal_to[0]',
                'errors' => [
                    'required'              => 'Harga pokok harus diisi',
                    'numeric'               => 'Harga pokok harus berupa angka',
                    'greater_than_equal_to' => 'Harga pokok tidak boleh negatif',
                ],
            ],
            'harga_jual' => [
                'rules'  => 'required|numeric|greater_than_equal_to[0]',
                'errors' => [
                    'required'              => 'Harga jual harus diisi',
                    'numeric'               => 'Harga jual harus berupa angka',
                    'greater_than_equal_to' => 'Harga jual tidak boleh negatif',
                ],
            ],
        ];

        if (!$this->validate($rules)) {
            return $this->fail($this->validator->getErrors());
        }

        $this->model->insert($this->request->getVar());

        return $this->respond(['status' => true, 'message' => 'Barang baru berhasil disimpan']);
    }

    public function update($id = null)
    {
        if (!$this->model->find($id)) {
            return $this->failNotFound('Data tidak ditemukan');
        }

        $rules = [
            'nama' => [
                'rules'  => "required|min_length[3]|is_unique[barang.nama,id,{$id}]",
                'errors' => [
                    'required'   => 'Nama barang harus diisi',
                    'min_length' => 'Nama barang minimal 3 karakter',
                    'is_unique'  => 'Nama barang sudah terdaftar',
                ],
            ],
            'barcode' => [
                'rules'  => "required|min_length[5]|is_unique[barang.barcode,id,{$id}]",
                'errors' => [
                    'required'   => 'Barcode harus diisi',
                    'min_length' => 'Barcode terlalu pendek',
                    'is_unique'  => 'Barcode sudah digunakan',
                ],
            ],
            'jenis_id' => [
                'rules'  => 'required|numeric',
                'errors' => [
                    'required' => 'Jenis barang harus dipilih',
                    'numeric'  => 'Jenis barang tidak valid',
                ],
            ],
            'satuan_id' => [
                'rules'  => 'required|numeric',
                'errors' => [
                    'required' => 'Satuan barang harus dipilih',
                    'numeric'  => 'Satuan barang tidak valid',
                ],
            ],
            'harga_pokok' => [
                'rules'  => 'required|numeric|greater_than_equal_to[0]',
                'errors' => [
                    'required'              => 'Harga pokok harus diisi',
                    'numeric'               => 'Harga pokok harus berupa angka',
                    'greater_than_equal_to' => 'Harga pokok tidak boleh negatif',
                ],
            ],
            'harga_jual' => [
                'rules'  => 'required|numeric|greater_than_equal_to[0]',
                'errors' => [
                    'required'              => 'Harga jual harus diisi',
                    'numeric'               => 'Harga jual harus berupa angka',
                    'greater_than_equal_to' => 'Harga jual tidak boleh negatif',
                ],
            ],
        ];

        if (!$this->validate($rules)) {
            return $this->fail($this->validator->getErrors());
        }

        $this->model->update($id, $this->request->getVar());

        return $this->respond(['status' => true, 'message' => 'Data barang berhasil diperbarui']);
    }

    public function delete($id = null)
    {
        if (!$this->model->find($id)) {
            return $this->failNotFound('Data tidak ditemukan');
        }

        try {
            $this->model->delete($id);
            return $this->respond(['status' => true, 'message' => 'Barang berhasil dihapus']);
        } catch (\Exception $e) {
            return $this->fail('Gagal menghapus! Barang ini mungkin sedang digunakan dalam transaksi.');
        }
    }

    // GET /barang/diskon-aktif
    // Mengembalikan daftar barang yang sedang kena diskon aktif hari ini
    // untuk cabang yang login. Dipakai Vue agar badge diskon muncul di tabel.
    public function diskonAktif()
    {
        $db        = \Config\Database::connect();
        $cabang_id = session()->get('cabang_id');
        $today     = (new \DateTime('now', new \DateTimeZone('Asia/Jakarta')))->format('Y-m-d');

        $data = $db->query("
            SELECT dti.barang_id, dti.nominal_diskon
            FROM diskon_terbatas dt
            JOIN diskon_terbatas_item dti ON dti.diskon_terbatas_id = dt.id
            WHERE dt.cabang_id    = ?
              AND dt.status       = 'aktif'
              AND dt.tgl_mulai   <= ?
              AND dt.tgl_selesai >= ?
        ", [$cabang_id, $today, $today])->getResultArray();

        return $this->respond(['status' => true, 'data' => $data]);
    }
}
