<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            color: #1e293b;
            background: #fff;
            width: 100%;
        }

        /* ══ HEADER ═══════════════════════════════════════════════════ */
        .header {
            padding: 18px 24px 16px;
            margin-bottom: 16px;
            color: #fff;
            width: 100%;
        }

        .header-wrap {
            display: table;
            width: 100%;
        }

        .header-left {
            display: table-cell;
            vertical-align: middle;
            width: 60%;
        }

        .header-right {
            display: table-cell;
            vertical-align: top;
            width: 40%;
            text-align: right;
        }

        .badge-type {
            display: inline-block;
            font-size: 9px;
            font-weight: 700;
            padding: 3px 12px;
            border-radius: 20px;
            letter-spacing: 1px;
            margin-bottom: 7px;
            text-transform: uppercase;
            color: #fff;
        }

        .header h1 {
            font-size: 22px;
            font-weight: 700;
            letter-spacing: 0.3px;
            margin-bottom: 4px;
        }

        .header .sub {
            font-size: 11px;
            opacity: 0.80;
        }

        .meta-box {
            display: inline-block;
            background: rgba(255, 255, 255, 0.13);
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 10px;
            line-height: 1.9;
            text-align: right;
        }

        .meta-label {
            opacity: 0.70;
        }

        .meta-val {
            font-weight: 700;
        }

        /* ══ SUMMARY CARDS ════════════════════════════════════════════ */
        .cards {
            display: table;
            width: 100%;
            margin-bottom: 16px;
        }

        .card-cell {
            display: table-cell;
            padding-right: 10px;
        }

        .card-cell:last-child {
            padding-right: 0;
        }

        .card {
            border-radius: 8px;
            padding: 12px 16px;
        }

        .card-label {
            font-size: 8.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 5px;
        }

        .card-value {
            font-size: 17px;
            font-weight: 700;
        }

        /* ══ TABLE — FULL WIDTH ═══════════════════════════════════════ */
        .tbl-wrap {
            width: 100%;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            /* kunci agar kolom tidak overflow */
        }

        thead th {
            padding: 9px 8px;
            font-size: 8.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-align: left;
            color: #fff;
            overflow: hidden;
            word-wrap: break-word;
        }

        th.r,
        td.r {
            text-align: right;
        }

        th.c,
        td.c {
            text-align: center;
        }

        tbody tr {
            border-bottom: 1px solid #f1f5f9;
        }

        tbody td {
            padding: 9px 8px;
            font-size: 10.5px;
            vertical-align: middle;
            overflow: hidden;
            word-wrap: break-word;
        }

        tbody tr.even {
            background: #f8fafc;
        }

        tbody tr.odd {
            background: #ffffff;
        }

        .nama-prod {
            font-size: 11px;
            font-weight: 700;
            color: #0f172a;
        }

        .nama-jenis {
            font-size: 9px;
            color: #94a3b8;
            margin-top: 2px;
        }

        .badge-promo {
            display: inline-block;
            background: #fef9c3;
            color: #a16207;
            border: 1px solid #fde047;
            font-size: 7px;
            font-weight: 700;
            padding: 1px 5px;
            border-radius: 20px;
            vertical-align: middle;
        }

        .stok-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 700;
        }

        .stok-ok {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .stok-nol {
            background: #fee2e2;
            color: #b91c1c;
        }

        .coret {
            text-decoration: line-through;
            color: #cbd5e1;
            font-size: 9px;
        }

        .harga-prom {
            color: #059669;
            font-size: 11px;
            font-weight: 700;
        }

        .disc-note {
            font-size: 8px;
            color: #d97706;
            margin-top: 1px;
        }

        .pos {
            color: #059669;
            font-weight: 700;
        }

        .neg {
            color: #dc2626;
            font-weight: 700;
        }

        .no-col {
            color: #cbd5e1;
            font-size: 10px;
        }

        /* ══ TOTAL ROW ════════════════════════════════════════════════ */
        .total-row td {
            padding: 10px 8px;
            font-size: 11px;
            font-weight: 700;
            color: #fff;
            border: none;
        }

        /* ══ PAGE FOOTER ══════════════════════════════════════════════ */
        .pfooter {
            margin-top: 14px;
            padding-top: 8px;
            border-top: 1.5px solid #e2e8f0;
            display: table;
            width: 100%;
        }

        .pfooter .l {
            display: table-cell;
            font-size: 8.5px;
            color: #94a3b8;
            vertical-align: middle;
        }

        .pfooter .r2 {
            display: table-cell;
            text-align: right;
            font-size: 8.5px;
            color: #94a3b8;
            vertical-align: middle;
        }
    </style>
</head>

<body>

    <?php
    $isSemua = ($type === 'semua');

    $hdrBg      = $isSemua ? '#1e3a5f' : '#7c2d12';
    $badgeBg    = $isSemua ? '#10b981' : '#f59e0b';
    $tblHdrBg   = $isSemua ? '#1e3a5f' : '#7c2d12';
    $tblFtBg    = $isSemua ? '#1e3a5f' : '#7c2d12';
    $tblRowEven = $isSemua ? '#f8fafc' : '#fff9f5';

    $cardBorder = $isSemua ? '#e2e8f0'  : '#fed7aa';
    $cardBg1    = $isSemua ? '#f0f9ff'  : '#fff7ed';
    $cardBg2    = $isSemua ? '#f0fdf4'  : '#fff7ed';
    $cardLbl1   = $isSemua ? '#0369a1'  : '#9a3412';
    $cardLbl2   = $isSemua ? '#166534'  : '#9a3412';
    $cardVal1   = $isSemua ? '#0c4a6e'  : '#7c2d12';
    $cardVal2   = $isSemua ? '#14532d'  : '#7c2d12';

    $badgeLabel = $isSemua ? 'Semua Stok Terkini'    : 'Barang Promo Aktif';
    $judulH1    = $isSemua ? 'Laporan Aset Toko'      : 'Laporan Barang Promo';
    $tipeTeks   = $isSemua ? 'Seluruh Stok Terkini'   : 'Barang Promo Aktif';

    $totalItems = count($items);
    $totalPromo = count(array_filter($items, fn($i) => $i['ada_promo'] == 1));

    /* colspan total row:
   semua  = 9 kolom total, span = 7  (sisakan 2 kolom: sub aset, sub margin)
   promo  = 11 kolom total, span = 9 (sisakan 2 kolom: sub aset, sub margin) */
    $colSpanFt = $isSemua ? 7 : 9;
    ?>

    <!-- HEADER -->
    <div class="header" style="background:<?= $hdrBg ?>;">
        <div class="header-wrap">
            <div class="header-left">
                <div class="badge-type" style="background:<?= $badgeBg ?>;"><?= $badgeLabel ?></div>
                <h1><?= $judulH1 ?></h1>
                <div class="sub"><?= esc($cabang['nama']) ?> &mdash; <?= date('d F Y', strtotime($today)) ?></div>
            </div>
         
        </div>
    </div>

    <!-- SUMMARY CARDS -->
    <div class="cards">
        <div class="card-cell" style="width:28%;">
            <div class="card" style="border:1.5px solid <?= $cardBorder ?>;background:<?= $cardBg1 ?>;">
                <div class="card-label" style="color:<?= $cardLbl1 ?>;">Total Produk</div>
                <div class="card-value" style="color:<?= $cardVal1 ?>;">
                    <?= $totalItems ?> item<?php if ($isSemua && $totalPromo > 0): ?> <span style="font-size:12px;color:#d97706;">(<?= $totalPromo ?> promo)</span><?php endif; ?>
                </div>
            </div>
        </div>
        <div class="card-cell" style="width:36%;">
            <div class="card" style="border:1.5px solid <?= $cardBorder ?>;background:<?= $cardBg2 ?>;">
                <div class="card-label" style="color:<?= $cardLbl2 ?>;">Estimasi Nilai Aset</div>
                <div class="card-value" style="color:<?= $cardVal2 ?>;">Rp <?= number_format($totalAset, 0, ',', '.') ?></div>
            </div>
        </div>
        <div class="card-cell" style="width:36%;">
            <div class="card" style="border:1.5px solid <?= $cardBorder ?>;background:<?= $cardBg2 ?>;">
                <div class="card-label" style="color:<?= $cardLbl2 ?>;">Estimasi Total Margin</div>
                <div class="card-value pos">Rp <?= number_format($totalMargin, 0, ',', '.') ?></div>
            </div>
        </div>
    </div>

    <!-- TABLE -->
    <div class="tbl-wrap">
        <table>
            <thead>
                <tr style="background:<?= $tblHdrBg ?>;">
                    <?php if ($isSemua): ?>
                        <!-- 9 kolom = 3+20+9+9+12+12+10+12+13 = 100% -->
                        <th style="width:3%;">#</th>
                        <th style="width:20%;">Nama Produk</th>
                        <th style="width:9%;">Jenis</th>
                        <th class="c" style="width:9%;">Stok</th>
                        <th class="r" style="width:12%;">Harga Pokok</th>
                        <th class="r" style="width:12%;">Harga Jual</th>
                        <th class="r" style="width:10%;">Margin/Unit</th>
                        <th class="r" style="width:12%;">Sub Aset</th>
                        <th class="r" style="width:13%;">Sub Margin</th>
                    <?php else: ?>
                        <!-- 11 kolom = 3+16+7+8+9+9+8+9+9+11+11 = 100% -->
                        <th style="width:3%;">#</th>
                        <th style="width:16%;">Nama Produk</th>
                        <th style="width:7%;">Jenis</th>
                        <th class="c" style="width:8%;">Stok</th>
                        <th class="r" style="width:9%;">Harga Pokok</th>
                        <th class="r" style="width:9%;">Harga Normal</th>
                        <th class="r" style="width:8%;">Diskon</th>
                        <th class="r" style="width:9%;">Harga Efektif</th>
                        <th class="r" style="width:9%;">Margin/Unit</th>
                        <th class="r" style="width:11%;">Sub Aset</th>
                        <th class="r" style="width:11%;">Sub Margin</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($items)): ?>
                    <tr class="odd">
                        <td colspan="<?= $isSemua ? 9 : 11 ?>"
                            style="text-align:center;padding:28px;color:#94a3b8;">
                            <?= $isSemua ? 'Belum ada data stok untuk cabang ini.' : 'Tidak ada barang promo aktif untuk cabang ini.' ?>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($items as $no => $item):
                        $pokok   = (int) $item['harga_pokok'];
                        $jual    = (int) $item['harga_jual'];
                        $diskon  = $item['ada_promo'] ? (int) $item['nominal_diskon'] : 0;
                        $efektif = $jual - $diskon;
                        $margin  = $efektif - $pokok;
                        $stock   = (int) $item['stock'];
                        $subAset = $stock * $pokok;
                        $subMgn  = $stock * $margin;
                        $rc      = ($no % 2 === 0) ? 'odd' : 'even';
                        $rbg     = ($no % 2 === 0) ? '#ffffff' : $tblRowEven;
                    ?>
                        <tr class="<?= $rc ?>" style="background:<?= $rbg ?>;">
                            <td class="no-col"><?= $no + 1 ?></td>
                            <td>
                                <div class="nama-prod">
                                    <?= esc($item['nama_barang']) ?>
                                    <?php if ($item['ada_promo']): ?>&nbsp;<span class="badge-promo">PROMO</span><?php endif; ?>
                                </div>
                            </td>
                            <td><span class="nama-jenis"><?= esc($item['nama_jenis']) ?></span></td>
                            <td class="c">
                                <span class="stok-badge <?= $stock > 0 ? 'stok-ok' : 'stok-nol' ?>">
                                    <?= $stock ?> <?= esc($item['nama_satuan']) ?>
                                </span>
                            </td>
                            <td class="r">Rp <?= number_format($pokok, 0, ',', '.') ?></td>
                            <td class="r">
                                <?php if ($item['ada_promo'] && $isSemua): ?>
                                    <div class="coret">Rp <?= number_format($jual, 0, ',', '.') ?></div>
                                    <div class="harga-prom">Rp <?= number_format($efektif, 0, ',', '.') ?></div>
                                    <div class="disc-note">diskon Rp <?= number_format($diskon, 0, ',', '.') ?></div>
                                <?php elseif (!$isSemua): ?>
                                    <span class="coret">Rp <?= number_format($jual, 0, ',', '.') ?></span>
                                <?php else: ?>
                                    Rp <?= number_format($jual, 0, ',', '.') ?>
                                <?php endif; ?>
                            </td>
                            <?php if (!$isSemua): ?>
                                <td class="r" style="color:#d97706;font-weight:700;">&minus; Rp <?= number_format($diskon, 0, ',', '.') ?></td>
                                <td class="r harga-prom">Rp <?= number_format($efektif, 0, ',', '.') ?></td>
                            <?php endif; ?>
                            <td class="r <?= $margin >= 0 ? 'pos' : 'neg' ?>">Rp <?= number_format($margin, 0, ',', '.') ?></td>
                            <td class="r">Rp <?= number_format($subAset, 0, ',', '.') ?></td>
                            <td class="r <?= $subMgn >= 0 ? 'pos' : 'neg' ?>">Rp <?= number_format($subMgn, 0, ',', '.') ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>

            <!-- TOTAL ROW -->
            <tr class="total-row" style="background:<?= $tblFtBg ?>;">
                <td colspan="<?= $colSpanFt ?>" class="r"
                    style="font-size:9px;text-transform:uppercase;letter-spacing:0.8px;opacity:0.80;">
                    Total Keseluruhan
                </td>
                <td class="r" style="font-size:13px;">Rp <?= number_format($totalAset, 0, ',', '.') ?></td>
                <td class="r" style="font-size:13px;color:#4ade80;">Rp <?= number_format($totalMargin, 0, ',', '.') ?></td>
            </tr>
        </table>
    </div>

    <!-- PAGE FOOTER -->
    <div class="pfooter">
        <div class="l">
            <?= $isSemua
                ? '* Harga efektif sudah memperhitungkan diskon promo yang sedang aktif pada tanggal cetak.'
                : '* Hanya menampilkan barang dengan diskon terbatas yang statusnya aktif pada tanggal cetak.' ?>
        </div>
        <div class="r2">Dokumen dibuat otomatis oleh sistem &mdash; <?= date('d/m/Y H:i') ?> WIB</div>
    </div>

</body>

</html>