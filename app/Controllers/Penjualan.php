<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use Dompdf\Dompdf;
use Dompdf\Options;

class Penjualan extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    // ─────────────────────────────────────────────────────────
    // HELPER: Ambil diskon aktif untuk satu barang_id di cabang
    // Mengembalikan nominal_diskon atau 0 jika tidak ada
    // ─────────────────────────────────────────────────────────
    private function getDiskonAktif(int $cabang_id, int $barang_id): float
    {
        // Tanggal hari ini timezone Asia/Jakarta
        $today = (new \DateTime('now', new \DateTimeZone('Asia/Jakarta')))->format('Y-m-d');

        $row = $this->db->query("
            SELECT dti.nominal_diskon
            FROM diskon_terbatas dt
            JOIN diskon_terbatas_item dti ON dti.diskon_terbatas_id = dt.id
            WHERE dt.cabang_id  = ?
              AND dt.status     = 'aktif'
              AND dt.tgl_mulai  <= ?
              AND dt.tgl_selesai >= ?
              AND dti.barang_id  = ?
            LIMIT 1
        ", [$cabang_id, $today, $today, $barang_id])->getRow();

        return $row ? (float)$row->nominal_diskon : 0.0;
    }

    // ─────────────────────────────────────────────────────────
    // HELPER: Ambil semua diskon aktif untuk cabang (map barang_id => nominal)
    // ─────────────────────────────────────────────────────────
    private function getAllDiskonAktif(int $cabang_id): array
    {
        $today = (new \DateTime('now', new \DateTimeZone('Asia/Jakarta')))->format('Y-m-d');

        $rows = $this->db->query("
            SELECT dti.barang_id, dti.nominal_diskon
            FROM diskon_terbatas dt
            JOIN diskon_terbatas_item dti ON dti.diskon_terbatas_id = dt.id
            WHERE dt.cabang_id   = ?
              AND dt.status      = 'aktif'
              AND dt.tgl_mulai  <= ?
              AND dt.tgl_selesai >= ?
        ", [$cabang_id, $today, $today])->getResult();

        $map = [];
        foreach ($rows as $r) {
            $map[(int)$r->barang_id] = (float)$r->nominal_diskon;
        }
        return $map;
    }

    public function index()
    {
        $operator_id = session()->get('user_id');
        $cabang_id   = session()->get('cabang_id');

        if (!$operator_id || !$cabang_id) {
            return redirect()->to('/login');
        }

        $penjualan_id = session()->get('active_penjualan_id');

        if (!$penjualan_id) {
            $this->db->table('penjualan')->insert([
                'faktur'            => 'DRAFT-' . date('YmdHis'),
                'pembayaran_id'     => null,
                'nominal_penjualan' => 0,
                'operator_id'       => $operator_id,
                'cabang_id'         => $cabang_id,
                'customer_id'       => null,
                'created_at'        => null
            ]);

            $penjualan_id = $this->db->insertID();
            session()->set('active_penjualan_id', $penjualan_id);
        }

        return view('transaksi/penjualan', [
            'title'        => 'Transaksi Penjualan',
            'penjualan_id' => $penjualan_id
        ]);
    }

    public function addItem()
    {
        $penjualan_id = session()->get('active_penjualan_id');
        $cabang_id    = session()->get('cabang_id');

        if (!$penjualan_id) {
            return $this->response->setStatusCode(400)
                ->setJSON(['message' => 'Transaksi tidak aktif']);
        }

        $data         = $this->request->getJSON();
        $inventory_id = $data->inventory_id ?? null;
        $qty          = (int)($data->qty ?? 0);

        if (!$inventory_id || $qty <= 0) {
            return $this->response->setStatusCode(400)
                ->setJSON(['message' => 'Data tidak valid']);
        }

        $this->db->transBegin();

        try {
            $inv = $this->db->query("
                SELECT i.stock, b.harga_jual, b.id as barang_id
                FROM inventory i
                JOIN barang b ON b.id = i.barang_id
                WHERE i.id = ?
                FOR UPDATE
            ", [$inventory_id])->getRow();

            if (!$inv) throw new \Exception('Inventory tidak ditemukan');
            if ($inv->stock < $qty) throw new \Exception('Stok tidak cukup');

            // Cek diskon aktif untuk barang ini
            $nominal_diskon = $this->getDiskonAktif((int)$cabang_id, (int)$inv->barang_id);
            $harga_diskon   = max(0, $inv->harga_jual - $nominal_diskon);
            $subtotal       = $harga_diskon * $qty;

            $existing = $this->db->table('penjualan_item')
                ->where('penjualan_id', $penjualan_id)
                ->where('inventory_id', $inventory_id)
                ->get()->getRow();

            if ($existing) {
                $newQty      = $existing->qty + $qty;
                $newSubtotal = $newQty * $harga_diskon;

                $this->db->table('penjualan_item')
                    ->where('id', $existing->id)
                    ->update([
                        'qty'             => $newQty,
                        'subtotal'        => $newSubtotal,
                        'nominal_diskon'  => $nominal_diskon,
                        'harga_setelah_diskon' => $harga_diskon,
                    ]);
            } else {
                $this->db->table('penjualan_item')->insert([
                    'penjualan_id'         => $penjualan_id,
                    'inventory_id'         => $inventory_id,
                    'harga_satuan'         => $inv->harga_jual,
                    'nominal_diskon'       => $nominal_diskon,
                    'harga_setelah_diskon' => $harga_diskon,
                    'qty'                  => $qty,
                    'subtotal'             => $subtotal
                ]);
            }

            $this->db->table('inventory')
                ->where('id', $inventory_id)
                ->update(['stock' => $inv->stock - $qty]);

            $total = $this->db->table('penjualan_item')
                ->selectSum('subtotal')
                ->where('penjualan_id', $penjualan_id)
                ->get()->getRow()->subtotal ?? 0;

            $this->db->table('penjualan')
                ->where('id', $penjualan_id)
                ->update(['nominal_penjualan' => $total]);

            $this->db->transCommit();

            return $this->response->setJSON(['status' => true, 'total' => $total]);
        } catch (\Throwable $e) {
            $this->db->transRollback();
            return $this->response->setStatusCode(400)
                ->setJSON(['message' => $e->getMessage()]);
        }
    }

    public function updateQty($id)
    {
        $penjualan_id = session()->get('active_penjualan_id');
        $qtyBaru      = (int)($this->request->getJSON()->qty ?? 0);

        if ($qtyBaru < 1) {
            return $this->response->setStatusCode(400)
                ->setJSON(['message' => 'Qty minimal 1']);
        }

        $this->db->transBegin();

        try {
            $item = $this->db->query("
                SELECT pi.*, i.stock, b.id as barang_id
                FROM penjualan_item pi
                JOIN inventory i ON i.id = pi.inventory_id
                JOIN barang b ON b.id = i.barang_id
                WHERE pi.id = ? AND pi.penjualan_id = ?
                FOR UPDATE
            ", [$id, $penjualan_id])->getRow();

            if (!$item) throw new \Exception('Item tidak ditemukan');

            $qtyLama = $item->qty;
            $selisih = $qtyBaru - $qtyLama;

            if ($selisih > 0 && $item->stock < $selisih) {
                throw new \Exception('Stok tidak cukup');
            }

            $this->db->table('inventory')
                ->where('id', $item->inventory_id)
                ->update(['stock' => $item->stock - $selisih]);

            // Gunakan harga_setelah_diskon jika ada, fallback ke harga_satuan
            $hargaEfektif = isset($item->harga_setelah_diskon) && $item->harga_setelah_diskon > 0
                ? $item->harga_setelah_diskon
                : $item->harga_satuan;

            $subtotal = $qtyBaru * $hargaEfektif;

            $this->db->table('penjualan_item')
                ->where('id', $id)
                ->update(['qty' => $qtyBaru, 'subtotal' => $subtotal]);

            $total = $this->db->table('penjualan_item')
                ->selectSum('subtotal')
                ->where('penjualan_id', $penjualan_id)
                ->get()->getRow()->subtotal ?? 0;

            $this->db->table('penjualan')
                ->where('id', $penjualan_id)
                ->update(['nominal_penjualan' => $total]);

            $this->db->transCommit();

            return $this->response->setJSON(['status' => true, 'total' => $total]);
        } catch (\Throwable $e) {
            $this->db->transRollback();
            return $this->response->setStatusCode(400)
                ->setJSON(['message' => $e->getMessage()]);
        }
    }

    public function deleteItem($id)
    {
        $penjualan_id = session()->get('active_penjualan_id');

        $this->db->transBegin();

        try {
            $item = $this->db->query("
                SELECT pi.*, i.stock
                FROM penjualan_item pi
                JOIN inventory i ON i.id = pi.inventory_id
                WHERE pi.id = ? AND pi.penjualan_id = ?
                FOR UPDATE
            ", [$id, $penjualan_id])->getRow();

            if (!$item) throw new \Exception('Item tidak ditemukan');

            $this->db->table('inventory')
                ->where('id', $item->inventory_id)
                ->update(['stock' => $item->stock + $item->qty]);

            $this->db->table('penjualan_item')->where('id', $id)->delete();

            $total = $this->db->table('penjualan_item')
                ->selectSum('subtotal')
                ->where('penjualan_id', $penjualan_id)
                ->get()->getRow()->subtotal ?? 0;

            $this->db->table('penjualan')
                ->where('id', $penjualan_id)
                ->update(['nominal_penjualan' => $total]);

            $this->db->transCommit();

            return $this->response->setJSON(['status' => true, 'total' => $total]);
        } catch (\Throwable $e) {
            $this->db->transRollback();
            return $this->response->setStatusCode(400)
                ->setJSON(['message' => $e->getMessage()]);
        }
    }

    public function getCustomers()
    {
        $cabang_id = session()->get('cabang_id');

        $customers = $this->db->table('customer')
            ->groupStart()
            ->where('cabang_id', null)
            ->orWhere('cabang_id', $cabang_id)
            ->groupEnd()
            ->get()->getResult();

        return $this->response->setJSON($customers);
    }

    public function finalisasi()
    {
        $penjualan_id = session()->get('active_penjualan_id');

        if (!$penjualan_id) {
            return redirect()->to('penjualan')->with('error', 'Tidak ada transaksi aktif');
        }

        $data = $this->request->getPost();

        $this->db->transBegin();

        try {
            $this->db->table('pembayaran')->insert([
                'jenis_pembayaran' => $data['jenis_bayar'],
                'diskon_nominal'   => $data['diskon'] ?? 0,
                'nominal_bayar'    => $data['bayar'],
                'kembalian'        => $data['kembalian']
            ]);

            $pembayaran_id = $this->db->insertID();

            $this->db->table('penjualan')
                ->where('id', $penjualan_id)
                ->update([
                    'pembayaran_id' => $pembayaran_id,
                    'customer_id'   => $data['customer_id'] ?: null,
                    'created_at'    => date('Y-m-d H:i:s'),
                    'faktur'        => 'PJ-' . date('YmdHis')
                ]);

            $this->db->transCommit();

            session()->remove('active_penjualan_id');

            $operator_id = session()->get('user_id');
            $cabang_id   = session()->get('cabang_id');

            $this->db->table('penjualan')->insert([
                'faktur'            => 'DRAFT-' . date('YmdHis'),
                'pembayaran_id'     => null,
                'nominal_penjualan' => 0,
                'operator_id'       => $operator_id,
                'cabang_id'         => $cabang_id,
                'customer_id'       => null,
                'created_at'        => null,
                'print_out'         => 0
            ]);

            $new_penjualan_id = $this->db->insertID();
            session()->set('active_penjualan_id', $new_penjualan_id);

            return redirect()->to('penjualan')
                ->with('cetak', base_url('penjualan/cetak/' . $penjualan_id));
        } catch (\Throwable $e) {
            $this->db->transRollback();
            return redirect()->to('penjualan')->with('error', $e->getMessage());
        }
    }

    public function cetak($id)
    {
        $db = \Config\Database::connect();

        $transaksi = $db->table('penjualan p')
            ->select('
                p.id,
                p.faktur,
                p.nominal_penjualan,
                pb.jenis_pembayaran,
                pb.diskon_nominal,
                pb.nominal_bayar,
                pb.kembalian,
                u.nama as nama_operator,
                c.nama as nama_cabang
            ')
            ->join('pembayaran pb', 'pb.id = p.pembayaran_id')
            ->join('users u', 'u.id = p.operator_id')
            ->join('cabang c', 'c.id = p.cabang_id')
            ->where('p.id', $id)
            ->get()
            ->getRowArray();

        if (!$transaksi) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $items = $db->table('penjualan_item pi')
            ->select('
                pi.qty,
                pi.harga_satuan,
                pi.nominal_diskon,
                pi.harga_setelah_diskon,
                pi.subtotal,
                b.nama as nama_barang,
                s.nama as nama_satuan
            ')
            ->join('inventory i', 'i.id = pi.inventory_id')
            ->join('barang b', 'b.id = i.barang_id')
            ->join('satuan s', 's.id = b.satuan_id')
            ->where('pi.penjualan_id', $id)
            ->get()
            ->getResultArray();

        $total_gross = array_sum(array_column($items, 'subtotal'));

        // Hitung total diskon per item (selisih harga asli vs diskon * qty)
        $total_diskon_item = 0;
        foreach ($items as $item) {
            if (!empty($item['nominal_diskon']) && $item['nominal_diskon'] > 0) {
                $total_diskon_item += $item['nominal_diskon'] * $item['qty'];
            }
        }

        $html = view('transaksi/struk_penjualan', [
            'transaksi'         => $transaksi,
            'items'             => $items,
            'total_gross'       => $total_gross,
            'total_diskon_item' => $total_diskon_item,
        ]);

        $options = new Options();
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper([0, 0, 226.77, 453.44]);
        $dompdf->render();

        return $this->response
            ->setContentType('application/pdf')
            ->setBody($dompdf->output());
    }

    public function searchBarang()
    {
        $q         = $this->request->getGet('q');
        $cabang_id = session()->get('cabang_id');

        if (!$q) return $this->response->setJSON([]);

        $builder = $this->db->table('inventory i');
        $builder->select('
            i.id as inventory_id,
            i.barang_id,
            b.nama,
            b.harga_jual,
            i.stock
        ');
        $builder->join('barang b', 'b.id = i.barang_id');
        $builder->where('i.cabang_id', $cabang_id);
        $builder->where('i.stock >', 0);
        $builder->groupStart()
            ->like('b.nama', $q)
            ->orLike('b.barcode', $q)
            ->groupEnd();

        $results = $builder->get()->getResult();

        // Inject diskon aktif ke setiap item
        $diskonMap = $this->getAllDiskonAktif((int)$cabang_id);
        foreach ($results as &$r) {
            $r->nominal_diskon       = $diskonMap[(int)$r->barang_id] ?? 0;
            $r->harga_setelah_diskon = max(0, $r->harga_jual - $r->nominal_diskon);
        }

        return $this->response->setJSON($results);
    }

    public function scanBarcode()
    {
        $barcode      = $this->request->getGet('barcode');
        $cabang_id    = session()->get('cabang_id');
        $penjualan_id = session()->get('active_penjualan_id');

        if (!$penjualan_id) {
            return $this->response->setStatusCode(400)
                ->setJSON(['message' => 'Transaksi tidak aktif']);
        }

        if (!$barcode) {
            return $this->response->setStatusCode(400)
                ->setJSON(['message' => 'Barcode kosong']);
        }

        $this->db->transBegin();

        try {
            $product = $this->db->query("
                SELECT i.id as inventory_id, i.stock, b.harga_jual, b.id as barang_id
                FROM inventory i
                JOIN barang b ON b.id = i.barang_id
                WHERE i.cabang_id = ?
                AND b.barcode = ?
                FOR UPDATE
            ", [$cabang_id, $barcode])->getRow();

            if (!$product) throw new \Exception('Barang tidak ditemukan');
            if ($product->stock < 1) throw new \Exception('Stok habis');

            $nominal_diskon = $this->getDiskonAktif((int)$cabang_id, (int)$product->barang_id);
            $harga_diskon   = max(0, $product->harga_jual - $nominal_diskon);

            $existing = $this->db->table('penjualan_item')
                ->where('penjualan_id', $penjualan_id)
                ->where('inventory_id', $product->inventory_id)
                ->get()->getRow();

            if ($existing) {
                $newQty      = $existing->qty + 1;
                $newSubtotal = $newQty * $harga_diskon;

                $this->db->table('penjualan_item')
                    ->where('id', $existing->id)
                    ->update([
                        'qty'                  => $newQty,
                        'subtotal'             => $newSubtotal,
                        'nominal_diskon'       => $nominal_diskon,
                        'harga_setelah_diskon' => $harga_diskon,
                    ]);
            } else {
                $this->db->table('penjualan_item')->insert([
                    'penjualan_id'         => $penjualan_id,
                    'inventory_id'         => $product->inventory_id,
                    'harga_satuan'         => $product->harga_jual,
                    'nominal_diskon'       => $nominal_diskon,
                    'harga_setelah_diskon' => $harga_diskon,
                    'qty'                  => 1,
                    'subtotal'             => $harga_diskon
                ]);
            }

            $this->db->table('inventory')
                ->where('id', $product->inventory_id)
                ->update(['stock' => $product->stock - 1]);

            $total = $this->db->table('penjualan_item')
                ->selectSum('subtotal')
                ->where('penjualan_id', $penjualan_id)
                ->get()->getRow()->subtotal ?? 0;

            $this->db->table('penjualan')
                ->where('id', $penjualan_id)
                ->update(['nominal_penjualan' => $total]);

            $this->db->transCommit();

            return $this->response->setJSON(['status' => true, 'total' => $total]);
        } catch (\Throwable $e) {
            $this->db->transRollback();
            return $this->response->setStatusCode(400)
                ->setJSON(['message' => $e->getMessage()]);
        }
    }

    public function detail()
    {
        $penjualan_id = session()->get('active_penjualan_id');

        if (!$penjualan_id) {
            return $this->response->setJSON(['items' => [], 'total' => 0]);
        }

        $items = $this->db->table('penjualan_item pi')
            ->select('
                pi.id,
                pi.qty,
                pi.harga_satuan,
                pi.nominal_diskon,
                pi.harga_setelah_diskon,
                pi.subtotal,
                b.nama,
                s.nama as satuan,
                i.stock
            ')
            ->join('inventory i', 'i.id=pi.inventory_id')
            ->join('barang b', 'b.id=i.barang_id')
            ->join('satuan s', 's.id=b.satuan_id')
            ->where('pi.penjualan_id', $penjualan_id)
            ->get()->getResult();

        $total = $this->db->table('penjualan_item')
            ->selectSum('subtotal')
            ->where('penjualan_id', $penjualan_id)
            ->get()->getRow()->subtotal ?? 0;

        return $this->response->setJSON(['items' => $items, 'total' => $total]);
    }
}
