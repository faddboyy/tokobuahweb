<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Surat Pengiriman - <?= esc($pengiriman->kode_pengiriman) ?></title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10.5px;
            color: #111;
            line-height: 1.5;
        }

        .page {
            width: 190mm;
            margin: 0 auto;
            padding: 10mm 0 8mm 0;
        }

        /* ================= HEADER ================= */

        .header-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 2.5px solid #111;
            margin-bottom: 10px;
        }

        .header-table td {
            vertical-align: middle;
            padding-bottom: 8px;
        }

        .company-name {
            font-size: 17px;
            font-weight: 700;
            letter-spacing: .5px;
            text-transform: uppercase;
        }

        .company-sub {
            font-size: 9px;
            color: #555;
            margin-top: 2px;
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

        /* ================= INFO ================= */

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
        }

        /* ================= ITEMS ================= */

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
            text-transform: uppercase;
        }

        .items-table thead th.c {
            text-align: center
        }

        .items-table thead th.r {
            text-align: right
        }

        .items-table tbody td {
            padding: 7px 8px;
            font-size: 10px;
            border-bottom: 1px solid #e8e8e8;
        }

        .items-table tbody tr:nth-child(even) {
            background: #f5f5f5;
        }

        .items-table tbody td.c {
            text-align: center
        }

        .items-table tbody td.r {
            text-align: right
        }

        .items-table tfoot td {
            padding: 7px 8px;
            font-size: 10.5px;
            font-weight: 700;
            border-top: 2px solid #111;
            background: #ececec;
        }

        .satuan-pill {
            background: #e2e2e2;
            padding: 1px 7px;
            border-radius: 10px;
            font-size: 9px;
        }

        /* ================= BOTTOM ================= */

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
            padding-right: 10px
        }

        .bottom-right {
            width: 44%
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
            color: #888;
            margin-bottom: 2px;
        }

        .tb-value {
            font-size: 10px;
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
            opacity: .65;
        }

        .total-box-value {
            font-size: 16px;
            font-weight: 700;
            margin-top: 2px;
        }

        .barcode-wrap {
            text-align: center
        }

        .barcode-wrap img {
            height: 38px
        }

        .barcode-text {
            font-size: 8px;
            color: #888;
            margin-top: 2px;
            letter-spacing: 1.5px;
        }

        /* ================= TTD ================= */

        .ttd-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }

        .ttd-table td {
            width: 25%;
            text-align: center;
            padding: 0 5px;
        }

        .ttd-title {
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
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
            text-align: right
        }
    </style>
</head>

<body>

    <?php
    function terbilang($angka)
    {
        $angka = (int)$angka;
        if ($angka == 0) return 'nol barang';
        return number_format($angka, 0, ',', '.') . ' barang';
    }

    $totalQty = 0;
    foreach ($items as $i) {
        $totalQty += $i->qty;
    }

    $waktu = date('d F Y, H:i', strtotime($pengiriman->created_at));
    ?>

    <div class="page">

        <!-- HEADER -->
        <table class="header-table">
            <tr>
                <td style="width:55%">
                    <div class="company-name">Toko Buah Segar</div>
                    <div class="company-sub">Distribusi Internal Gudang & Cabang</div>
                </td>
                <td style="width:45%">
                    <div class="doc-title">Surat Pengiriman</div>
                    <div class="doc-kode"><?= esc($pengiriman->kode_pengiriman) ?></div>
                </td>
            </tr>
        </table>

        <!-- INFO -->
        <table class="info-table">
            <tr>
                <td>
                    <div class="info-label">Gudang Pengirim</div>
                    <div class="info-value"><?= esc($pengiriman->nama_gudang) ?></div>
                </td>
                <td>
                    <div class="info-label">Toko Tujuan</div>
                    <div class="info-value"><?= esc($pengiriman->nama_cabang) ?></div>
                </td>
                <td style="border-right:none">
                    <div class="info-label">Tanggal & Waktu</div>
                    <div class="info-value"><?= $waktu ?></div>
                </td>
            </tr>
        </table>

        <!-- ITEMS -->
        <table class="items-table">
            <thead>
                <tr>
                    <th class="c" style="width:22px">#</th>
                    <th>Nama Barang</th>
                    <th class="c" style="width:130px">Qty Kirim</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($items)): ?>
                    <tr>
                        <td colspan="3" style="text-align:center;padding:18px;color:#aaa;">Tidak ada item</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($items as $i => $item): ?>
                        <tr>
                            <td class="c"><?= $i + 1 ?></td>
                            <td style="font-weight:600"><?= esc($item->nama_barang) ?></td>
                            <td class="c">
                                <?= number_format($item->qty, 0, ',', '.') ?>
                                <span class="satuan-pill"><?= esc($item->nama_satuan) ?></span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2" style="text-align:right">TOTAL QTY</td>
                    <td class="c"><?= number_format($totalQty, 0, ',', '.') ?></td>
                </tr>
            </tfoot>
        </table>

        <!-- BOTTOM -->
        <table class="bottom-table">
            <tr>
                <td class="bottom-left">

                    <div class="note-box">
                        <div class="note-label">Catatan</div>
                        <div class="note-text">
                            Barang telah dikirim dari gudang dan diterima dalam kondisi baik.
                        </div>
                    </div>
                </td>

                <td class="bottom-right">
                    <div class="total-box">
                       
                        <div class="total-box-value">BARCODE PENGIRIMAN</div>
                    </div>

                    <div class="barcode-wrap">
                        <img src="data:image/png;base64,<?= $barcode_base64 ?>">
                        <div class="barcode-text"><?= esc($pengiriman->kode_pengiriman) ?></div>
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
                        <div><?= esc($pengiriman->nama_operator) ?></div>
                    </div>
                </td>
                <td>
                    <div class="ttd-title">Pengirim</div>
                    <div class="ttd-inner">
                        <div class="ttd-space"></div>
                        <div>( ................ )</div>
                    </div>
                </td>
                <td>
                    <div class="ttd-title">Penerima</div>
                    <div class="ttd-inner">
                        <div class="ttd-space"></div>
                        <div>( ................ )</div>
                    </div>
                </td>
                <td>
                    <div class="ttd-title">Mengetahui</div>
                    <div class="ttd-inner">
                        <div class="ttd-space"></div>
                        <div>( ................ )</div>
                    </div>
                </td>
            </tr>
        </table>

        <!-- FOOTER -->
        <table class="footer-table">
            <tr>
                <td>Dicetak pada <?= date('d/m/Y H:i') ?> · Sistem Manajemen Gudang</td>
                <td class="r"><?= esc($pengiriman->kode_pengiriman) ?> · Hal. 1/1</td>
            </tr>
        </table>

    </div>
</body>

</html>