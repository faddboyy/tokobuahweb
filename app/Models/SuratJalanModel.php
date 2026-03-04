<?php

namespace App\Models;

use CodeIgniter\Model;

class SuratJalanModel extends Model
{
    protected $table      = 'surat_jalan';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'code_po',
        'waktu_po',
        'gudang_id',
        'total_nominal',
        'operator_id',
        'status',
    ];

    protected $validationRules = [
        'kode_po'      => 'required',
        'waktu_po'    => 'required|valid_date',
        'gudang_id'  => 'required|integer',
    ];

    protected $validationMessages = [
        'kode_po' => [
            'required' => 'Nomor surat jalan wajib diisi'
        ],
        'waktu_po' => [
            'required' => 'Tanggal wajib diisi'
        ]
    ];

    /*
    |--------------------------------------------------------------------------
    | Custom Query - With Relations
    |--------------------------------------------------------------------------
    */

    public function getWithRelation($id = null)
    {
        $builder = $this->db->table('surat_jalan sj')
            ->select('
                sj.*,
                g.nama as gudang_nama,
                u.nama as mandor_nama
            ')
            ->join('gudang_utama g', 'g.id = sj.gudang_id', 'left')
            ->join('users u', 'u.id = sj.mandor_id', 'left')
            ->orderBy('sj.id', 'DESC');

        if ($id) {
            return $builder->where('sj.id', $id)->get()->getRowArray();
        }

        return $builder->get()->getResultArray();
    }

    /*
    |--------------------------------------------------------------------------
    | Generate Nomor Otomatis
    |--------------------------------------------------------------------------
    */

    public function generateNomor()
    {
        $tanggal = date('Ymd');

        $last = $this->like('kode_po', 'SJ-' . $tanggal, 'after')
                     ->orderBy('id', 'DESC')
                     ->first();

        $urutan = 1;

        if ($last) {
            $explode = explode('-', $last['kode_po']);
            $urutan = intval(end($explode)) + 1;
        }

        return 'SJ-' . $tanggal . '-' . str_pad($urutan, 3, '0', STR_PAD_LEFT);
    }
}