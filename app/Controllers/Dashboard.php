<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Dashboard extends BaseController
{
    public function index()
    {
        return view('dashboard');
    }

    public function getData()
    {
        $db    = \Config\Database::connect();
        $role  = session()->get('role');

        $month = $this->request->getGet('month') ?? date('n');
        $year  = $this->request->getGet('year')  ?? date('Y');

        if ($role === 'petugas') {
            return $this->response->setJSON([
                'stats' => [],
                'chart' => ['labels' => [], 'datasets' => [], 'pengeluaran' => []]
            ]);
        }

        /* ── 1. Total Penjualan Bersih ─────────────────────────────── */
        $totalPenjualan = $db->table('penjualan')
            ->join('pembayaran', 'pembayaran.id = penjualan.pembayaran_id')
            ->select('SUM(pembayaran.nominal_bayar - pembayaran.kembalian - IFNULL(pembayaran.diskon_nominal, 0)) as total_bersih')
            ->where('MONTH(penjualan.created_at)', $month)
            ->where('YEAR(penjualan.created_at)', $year)
            ->get()->getRow()->total_bersih ?? 0;

        /* ── 2. Total Pengeluaran (surat_jalan, exclude dibatalkan) ── */
        $totalPengeluaran = $db->table('surat_jalan')
            ->select('SUM(total_nominal) as total')
            ->where('MONTH(waktu_po)', $month)
            ->where('YEAR(waktu_po)', $year)
            ->where('status !=', 'dibatalkan')
            ->get()->getRow()->total ?? 0;

        /* ── 3. Chart harian penjualan ────────────────────────────── */
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);

        $rawPenjualan = $db->table('penjualan')
            ->join('pembayaran', 'pembayaran.id = penjualan.pembayaran_id')
            ->select('DAY(penjualan.created_at) as tgl, SUM(pembayaran.nominal_bayar - pembayaran.kembalian - IFNULL(pembayaran.diskon_nominal, 0)) as harian')
            ->where('MONTH(penjualan.created_at)', $month)
            ->where('YEAR(penjualan.created_at)', $year)
            ->groupBy('DAY(penjualan.created_at)')
            ->get()->getResultArray();

        /* ── 4. Chart harian pengeluaran (surat_jalan) ───────────── */
        $rawPengeluaran = $db->table('surat_jalan')
            ->select('DAY(waktu_po) as tgl, SUM(total_nominal) as harian')
            ->where('MONTH(waktu_po)', $month)
            ->where('YEAR(waktu_po)', $year)
            ->where('status !=', 'dibatalkan')
            ->groupBy('DAY(waktu_po)')
            ->get()->getResultArray();

        /* ── 5. Build array per hari ─────────────────────────────── */
        $labels      = [];
        $datasets    = [];   // penjualan
        $pengeluaran = [];   // pengeluaran

        for ($i = 1; $i <= $daysInMonth; $i++) {
            $labels[] = $i;

            $nilaiJual = 0;
            foreach ($rawPenjualan as $row) {
                if ((int) $row['tgl'] === $i) {
                    $nilaiJual = (float) $row['harian'];
                    break;
                }
            }
            $datasets[] = $nilaiJual;

            $nilaiKeluar = 0;
            foreach ($rawPengeluaran as $row) {
                if ((int) $row['tgl'] === $i) {
                    $nilaiKeluar = (float) $row['harian'];
                    break;
                }
            }
            $pengeluaran[] = $nilaiKeluar;
        }

        return $this->response->setJSON([
            'stats' => [
                [
                    'label' => 'Nominal Pendapatan',
                    'value' => 'Rp ' . number_format($totalPenjualan,   0, ',', '.'),
                    'icon'  => 'shopping-bag',
                    'color' => 'primary',
                ],
                [
                    'label' => 'Total Pengeluaran',
                    'value' => 'Rp ' . number_format($totalPengeluaran, 0, ',', '.'),
                    'icon'  => 'receipt',
                    'color' => 'danger',
                ],
            ],
            'chart' => [
                'labels'      => $labels,
                'datasets'    => $datasets,
                'pengeluaran' => $pengeluaran,
            ],
        ]);
    }
}
