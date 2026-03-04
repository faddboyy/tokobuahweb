<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Surat PO - <?= esc($surat['kode_po']) ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10.5px;
            color: #111;
            background: #fff;
            line-height: 1.5;
        }

        .page {
            width: 190mm;
            margin: 0 auto;
            padding: 10mm 0 8mm 0;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 2.5px solid #111;
            padding-bottom: 8px;
            margin-bottom: 10px;
        }

        .header-table td {
            vertical-align: middle;
            padding-bottom: 8px;
        }

        .company-name {
            font-size: 17px;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .company-sub {
            font-size: 9px;
            color: #555;
            margin-top: 1px;
        }

        .doc-title {
            font-size: 16px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
            text-align: right;
        }

        .doc-kode {
            font-size: 9.5px;
            color: #555;
            text-align: right;
            margin-top: 2px;
        }

        .status-row {
            text-align: right;
            margin-bottom: 10px;
        }

        .status-badge {
            display: inline-block;
            padding: 2px 11px;
            border-radius: 20px;
            font-size: 9px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .s-order {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffc107;
        }

        .s-diterima {
            background: #d1e7dd;
            color: #0a3622;
            border: 1px solid #198754;
        }

        .s-selesai {
            background: #cfe2ff;
            color: #084298;
            border: 1px solid #0d6efd;
        }

        .s-dibatalkan {
            background: #f8d7da;
            color: #58151c;
            border: 1px solid #dc3545;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }

        .info-table td {
            width: 33.33%;
            vertical-align: top;
            padding: 7px 10px;
            background: #f7f7f7;
            border-left: 3px solid #111;
            border-right: 4px solid #fff;
        }

        .info-label {
            font-size: 7.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #888;
            margin-bottom: 3px;
        }

        .info-value {
            font-size: 10.5px;
            font-weight: 700;
            color: #111;
        }

        .info-sub {
            font-size: 9px;
            color: #555;
            margin-top: 2px;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }

        .items-table thead tr {
            background: #111;
            color: #fff;
        }

        .items-table thead th {
            padding: 6px 8px;
            font-size: 8.5px;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .items-table thead th.r {
            text-align: right;
        }

        .items-table thead th.c {
            text-align: center;
        }

        .items-table tbody tr.odd {
            background: #fff;
        }

        .items-table tbody tr.even {
            background: #f5f5f5;
        }

        .items-table tbody td {
            padding: 7px 8px;
            font-size: 10px;
            border-bottom: 1px solid #e8e8e8;
            vertical-align: middle;
        }

        .items-table tbody td.r {
            text-align: right;
        }

        .items-table tbody td.c {
            text-align: center;
        }

        .items-table tfoot td {
            padding: 7px 8px;
            font-size: 10.5px;
            font-weight: 700;
            border-top: 2px solid #111;
            background: #ececec;
        }

        .items-table tfoot td.r {
            text-align: right;
        }

        .satuan-pill {
            background: #e2e2e2;
            padding: 1px 7px;
            border-radius: 10px;
            font-size: 9px;
        }

        .bottom-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }

        .bottom-table td {
            vertical-align: top;
        }

        .bottom-left {
            width: 56%;
            padding-right: 10px;
        }

        .bottom-right {
            width: 44%;
        }

        .terbilang-box {
            background: #f7f7f7;
            border-left: 3px solid #111;
            padding: 7px 10px;
            margin-bottom: 7px;
        }

        .tb-label {
            font-size: 7.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #888;
            margin-bottom: 2px;
        }

        .tb-value {
            font-size: 10px;
            color: #333;
            font-style: italic;
        }

        .note-box {
            border: 1px dashed #bbb;
            border-radius: 3px;
            padding: 7px 9px;
        }

        .note-label {
            font-size: 7.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #888;
            margin-bottom: 3px;
        }

        .note-text {
            font-size: 9.5px;
            color: #555;
            line-height: 1.6;
        }

        .total-box {
            background: #111;
            color: #fff;
            padding: 10px 12px;
            border-radius: 4px;
            text-align: right;
            margin-bottom: 8px;
        }

        .total-box-label {
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: 1px;
            opacity: 0.65;
        }

        .total-box-value {
            font-size: 16px;
            font-weight: 700;
            margin-top: 2px;
        }

        .barcode-wrap {
            text-align: center;
        }

        .barcode-wrap img {
            height: 38px;
            max-width: 100%;
        }

        .barcode-text {
            font-size: 8px;
            color: #888;
            margin-top: 2px;
            letter-spacing: 1.5px;
        }

        .ttd-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }

        .ttd-table td {
            width: 25%;
            text-align: center;
            padding: 0 5px;
            vertical-align: top;
        }

        .ttd-title {
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #444;
            margin-bottom: 4px;
        }

        .ttd-inner {
            border: 1px solid #ccc;
            border-radius: 3px;
            padding: 3px 6px;
        }

        .ttd-space {
            height: 48px;
            border-bottom: 1px solid #555;
            margin-bottom: 3px;
        }

        .ttd-name {
            font-size: 9px;
            color: #666;
            min-height: 13px;
        }

        .footer-table {
            width: 100%;
            border-collapse: collapse;
            border-top: 1px solid #ddd;
        }

        .footer-table td {
            padding-top: 5px;
            font-size: 8px;
            color: #aaa;
        }

        .footer-table td.r {
            text-align: right;
        }
    </style>
</head>

<body>
    <?php
    function formatRp($n)
    {
        return 'Rp ' . number_format((float)$n, 0, ',', '.');
    }

    function terbilang($angka)
    {
        $angka = (int) abs($angka);
        if ($angka === 0) return 'nol rupiah';

        $satuan = [
            '',
            'satu',
            'dua',
            'tiga',
            'empat',
            'lima',
            'enam',
            'tujuh',
            'delapan',
            'sembilan',
            'sepuluh',
            'sebelas',
            'dua belas',
            'tiga belas',
            'empat belas',
            'lima belas',
            'enam belas',
            'tujuh belas',
            'delapan belas',
            'sembilan belas'
        ];

        function _tb($n, $s)
        {
            if ($n === 0)  return '';
            if ($n < 20)   return $s[$n] . ' ';
            if ($n < 100)  return $s[(int)($n / 10)] . ' puluh ' . ($n % 10 ? $s[$n % 10] . ' ' : '');
            $r = (int)($n / 100);
            return ($r === 1 ? 'seratus ' : $s[$r] . ' ratus ') . _tb($n % 100, $s);
        }

        $ribuan = ['', 'ribu ', 'juta ', 'miliar ', 'triliun '];
        $result = '';
        $i = 0;
        while ($angka > 0) {
            $mod = $angka % 1000;
            if ($mod > 0) {
                $prefix = ($i === 1 && $mod === 1) ? 'seribu ' : _tb($mod, $satuan) . $ribuan[$i];
                $result = $prefix . $result;
            }
            $angka = (int)($angka / 1000);
            $i++;
        }
        return ucfirst(trim($result)) . ' rupiah';
    }

    $statusMap = [
        'order'      => ['label' => 'Pesanan',   'cls' => 's-order'],
        'diterima'   => ['label' => 'Diterima',  'cls' => 's-diterima'],
        'selesai'    => ['label' => 'Selesai',   'cls' => 's-selesai'],
        'dibatalkan' => ['label' => 'Dibatalkan', 'cls' => 's-dibatalkan'],
    ];
    $st    = $statusMap[$surat['status']] ?? ['label' => $surat['status'], 'cls' => 's-order'];
    $waktu = date('d F Y, H:i', strtotime($surat['waktu_po']));
    $total = (float) $surat['total_nominal'];
    ?>

    <div class="page">

        <!-- HEADER -->
        <table class="header-table">
            <tr>
                <td style="width:55%">
                    <div class="company-name">Toko Buah Segar</div>
                    <div class="company-sub">Grosir &amp; Eceran Buah Pilihan &middot; Bandung, Jawa Barat</div>
                </td>
                <td style="width:45%">
                    <div class="doc-title">Surat Pre Order</div>
                    <div class="doc-kode"><?= esc($surat['kode_po']) ?></div>
                </td>
            </tr>
        </table>

        <!-- STATUS -->
        <div class="status-row">
            <span class="status-badge <?= $st['cls'] ?>">&#9679; <?= $st['label'] ?></span>
        </div>

        <!-- INFO 3 KOLOM -->
        <table class="info-table">
            <tr>
                <td>
                    <div class="info-label">Supplier</div>
                    <div class="info-value"><?= esc($surat['nama_suplier'] ?? '-') ?></div>
                    <?php if (!empty($surat['alamat_suplier'])): ?>
                        <div class="info-sub"><?= nl2br(esc(trim($surat['alamat_suplier']))) ?></div>
                    <?php endif; ?>
                    <?php if (!empty($surat['telepon_suplier'])): ?>
                        <div class="info-sub"><?= esc($surat['telepon_suplier']) ?></div>
                    <?php endif; ?>
                </td>
                <td>
                    <div class="info-label">Gudang Tujuan</div>
                    <div class="info-value"><?= esc($surat['nama_gudang'] ?? '-') ?></div>
                    <div class="info-sub">Toko Buah Segar, Bandung</div>
                </td>
                <td style="border-right:none">
                    <div class="info-label">Tanggal &amp; Operator</div>
                    <div class="info-value"><?= $waktu ?></div>
                    <div class="info-sub">Operator: <?= esc($surat['nama_operator'] ?? '-') ?></div>
                </td>
            </tr>
        </table>

        <!-- ITEMS TABLE -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width:22px" class="c">#</th>
                    <th style="text-align: start;">Nama Barang</th>
                    <th style="width:120px" class="c">Qty</th>
                    <th style="width:118px" class="r">Total Harga Beli</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($items)): ?>
                    <tr class="odd">
                        <td colspan="3" style="text-align:center;padding:18px;color:#aaa;font-style:italic;">Tidak ada item</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($items as $i => $item): ?>
                        <tr class="<?= $i % 2 === 0 ? 'odd' : 'even' ?>">
                            <td class="c" style="color:#999"><?= $i + 1 ?></td>
                            <td style="font-weight:600; text-align:center"><?= esc($item['nama']) ?></td>
                            <td class="c"><?= number_format((float)$item['qty'], 0, ',', '.') ?><span class="satuan-pill"><?= esc($item['satuan']) ?></span></td>
                            <td class="r" style="font-weight:600"><?= formatRp($item['harga_beli']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" style="text-align:right;font-size:9px;letter-spacing:0.5px">TOTAL NOMINAL</td>
                    <td class="r" style="font-size:12px"><?= formatRp($total) ?></td>
                </tr>
            </tfoot>
        </table>

        <!-- BOTTOM -->
        <table class="bottom-table">
            <tr>
                <td class="bottom-left">
                    <div class="terbilang-box">
                        <div class="tb-label">Terbilang</div>
                        <div class="tb-value"><?= terbilang($total) ?></div>
                    </div>
                    <div class="note-box">
                        <div class="note-label">Catatan</div>
                        <div class="note-text">
                            Barang yang diterima harap diperiksa terlebih dahulu sebelum ditandatangani.<br>
                            Kekurangan / kerusakan setelah surat jalan ditandatangani bukan tanggung jawab pengirim.
                        </div>
                    </div>
                </td>
                <td class="bottom-right">
                    <div class="total-box">
                        <div class="total-box-label">Total Keseluruhan</div>
                        <div class="total-box-value"><?= formatRp($total) ?></div>
                    </div>
                    <div class="barcode-wrap">
                        <img src="data:image/png;base64,<?= $barcode ?>" alt="barcode">
                        <div class="barcode-text"><?= esc($surat['kode_po']) ?></div>
                    </div>
                </td>
            </tr>
        </table>

        <!-- TTD -->
        <table class="ttd-table">
            <tr>
                <td>
                    <div class="ttd-title">Dibuat Oleh</div>
                    <div class="ttd-inner">
                        <div class="ttd-space"></div>
                        <div class="ttd-name"><?= esc($surat['nama_operator'] ?? '') ?></div>
                    </div>
                </td>
                <td>
                    <div class="ttd-title">Pengirim</div>
                    <div class="ttd-inner">
                        <div class="ttd-space"></div>
                        <div class="ttd-name">( ................... )</div>
                    </div>
                </td>
                <td>
                    <div class="ttd-title">Penerima</div>
                    <div class="ttd-inner">
                        <div class="ttd-space"></div>
                        <div class="ttd-name">( ................... )</div>
                    </div>
                </td>
                <td>
                    <div class="ttd-title">Mengetahui</div>
                    <div class="ttd-inner">
                        <div class="ttd-space"></div>
                        <div class="ttd-name">( ................... )</div>
                    </div>
                </td>
            </tr>
        </table>

        <!-- FOOTER -->
        <table class="footer-table">
            <tr>
                <td>Dicetak pada <?= date('d/m/Y H:i') ?> &middot; Sistem Manajemen Toko Buah</td>
                <td class="r"><?= esc($surat['kode_po']) ?> &middot; Hal. 1/1</td>
            </tr>
        </table>

    </div>
</body>

</html>