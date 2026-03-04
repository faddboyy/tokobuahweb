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
            font-size: 9px;
            color: #1a1a2e;
            background: #fff;
        }

        /* HEADER */
        .header {
            background: #1e3a5f;
            color: #fff;
            padding: 14px 20px 12px;
            margin-bottom: 12px;
        }

        .header-top {
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
            vertical-align: middle;
            text-align: right;
            width: 40%;
        }

        .header h1 {
            font-size: 16px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .header .subtitle {
            font-size: 9px;
            opacity: 0.85;
            margin-top: 2px;
        }

        .badge-type {
            display: inline-block;
            background: <?= ($type === 'promo') ? '#f59e0b' : '#10b981' ?>;
            color: #fff;
            font-size: 8px;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 20px;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
            text-transform: uppercase;
        }

        .header-right .meta {
            font-size: 8px;
            opacity: 0.85;
            line-height: 1.6;
        }

        /* SUMMARY */
        .summary-row {
            display: table;
            width: 100%;
            margin: 0 0 12px;
        }

        .summary-cell {
            display: table-cell;
            width: 33.33%;
            padding-right: 6px;
        }

        .summary-cell:last-child {
            padding-right: 0;
        }

        .card {
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 8px 12px;
            background: #f8fafc;
        }

        .card-label {
            font-size: 7px;
            font-weight: 700;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 3px;
        }

        .card-value {
            font-size: 13px;
            font-weight: 700;
            color: #1e3a5f;
        }

        .card-value.green {
            color: #059669;
        }

        /* TABLE */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead tr {
            background: #1e3a5f;
            color: #fff;
        }

        thead th {
            padding: 6px 7px;
            font-size: 7.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            text-align: left;
        }

        thead th.r {
            text-align: right;
        }

        tbody tr:nth-child(even) {
            background: #f1f5f9;
        }

        tbody tr:nth-child(odd) {
            background: #fff;
        }

        tbody td {
            padding: 5px 7px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: middle;
            font-size: 8.5px;
        }

        tbody td.r {
            text-align: right;
        }

        .nama-barang {
            font-weight: 700;
            color: #1e293b;
        }

        .nama-jenis {
            font-size: 7px;
            color: #64748b;
        }

        .badge-promo {
            display: inline-block;
            background: #fef3c7;
            color: #b45309;
            border: 1px solid #fcd34d;
            font-size: 6.5px;
            font-weight: 700;
            padding: 1px 5px;
            border-radius: 10px;
        }

        .stock-badge {
            display: inline-block;
            padding: 2px 7px;
            border-radius: 10px;
            font-size: 8px;
            font-weight: 700;
        }

        .stock-ok {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .stock-empty {
            background: #fee2e2;
            color: #b91c1c;
        }

        .line-through {
            text-decoration: line-through;
            color: #94a3b8;
            font-size: 7.5px;
        }

        .price-promo {
            color: #059669;
            font-weight: 700;
        }

        .margin-pos {
            color: #059669;
            font-weight: 700;
        }

        .margin-neg {
            color: #dc2626;
            font-weight: 700;
        }

        tfoot tr {
            background: #1e3a5f;
            color: #fff;
        }

        tfoot td {
            padding: 6px 7px;
            font-size: 8.5px;
            font-weight: 700;
        }

        tfoot td.r {
            text-align: right;
        }

        /* PAGE FOOTER */
        .page-footer {
            margin-top: 10px;
            padding-top: 6px;
            border-top: 1px solid #e2e8f0;
            display: table;
            width: 100%;
        }

        .page-footer .left {
            display: table-cell;
            font-size: 7px;
            color: #94a3b8;
        }

        .page-footer .right {
            display: table-cell;
            text-align: right;
            font-size: 7px;
            color: #94a3b8;
        }
    </style>
</head>

<body>

    <!-- HEADER -->
    <div class="header">
        <div class="header-top">
            <div class="header-left">
                <div class="badge-type">
                    <?= ($type === 'promo') ? '&#9733; Barang Promo Aktif' : 'Semua Stok Terkini' ?>
                </div>
                <h1>Laporan Aset Toko</h1>
                <div class="subtitle"><?= esc($cabang['nama']) ?> &mdash; Per <?= date('d F Y', strtotime($today)) ?></div>
            </div>
            <div class="header-right">
                <div class="meta">
                    Dicetak oleh : Owner<br>
                    Tanggal&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <?= date('d/m/Y H:i') ?> WIB<br>
                    Tipe&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <?= ($type === 'promo') ? 'Barang Promo Aktif' : 'Seluruh Stok Terkini' ?>
                </div>
            </div>
        </div>
    </div>

    <?php
    $totalItems = count($items);
    $totalPromo = count(array_filter($items, fn($i) => $i['ada_promo'] == 1));
    ?>

    <!-- SUMMARY CARDS -->
    <div class="summary-row">
        <div class="summary-cell">
            <div class="card">
                <div class="card-label">Total Produk</div>
                <div class="card-value"><?= $totalItems ?> item<?= ($type === 'semua' && $totalPromo > 0) ? " ({$totalPromo} promo)" : '' ?></div>
            </div>
        </div>
        <div class="summary-cell">
            <div class="card">
                <div class="card-label">Estimasi Nilai Aset</div>
                <div class="card-value">Rp <?= number_format($totalAset, 0, ',', '.') ?></div>
            </div>
        </div>
        <div class="summary-cell">
            <div class="card">
                <div class="card-label">Estimasi Total Margin</div>
                <div class="card-value green">Rp <?= number_format($totalMargin, 0, ',', '.') ?></div>
            </div>
        </div>
    </div>

    <!-- TABLE -->
    <table>
        <thead>
            <tr>
                <th style="width:3%">#</th>
                <th style="width:22%">Nama Produk</th>
                <th style="width:8%">Jenis</th>
                <th style="width:8%" class="r">Stok</th>
                <th style="width:12%" class="r">Harga Pokok</th>
                <th style="width:14%" class="r">Harga Jual</th>
                <th style="width:10%" class="r">Margin/Unit</th>
                <th style="width:11%" class="r">Subtotal Aset</th>
                <th style="width:12%" class="r">Subtotal Margin</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($items)): ?>
                <tr>
                    <td colspan="9" style="text-align:center; padding:20px; color:#94a3b8;">
                        Tidak ada data<?= ($type === 'promo') ? ' barang promo' : '' ?> untuk cabang ini.
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
                ?>
                    <tr>
                        <td style="color:#94a3b8;"><?= $no + 1 ?></td>
                        <td>
                            <div class="nama-barang">
                                <?= esc($item['nama_barang']) ?>
                                <?php if ($item['ada_promo']): ?>
                                    <span class="badge-promo">PROMO</span>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td><span class="nama-jenis"><?= esc($item['nama_jenis']) ?></span></td>
                        <td class="r">
                            <span class="stock-badge <?= $stock > 0 ? 'stock-ok' : 'stock-empty' ?>">
                                <?= $stock ?> <?= esc($item['nama_satuan']) ?>
                            </span>
                        </td>
                        <td class="r">Rp <?= number_format($pokok, 0, ',', '.') ?></td>
                        <td class="r">
                            <?php if ($item['ada_promo']): ?>
                                <div class="line-through">Rp <?= number_format($jual, 0, ',', '.') ?></div>
                                <div class="price-promo">Rp <?= number_format($efektif, 0, ',', '.') ?></div>
                                <div style="font-size:7px;color:#94a3b8;">
                                    diskon Rp <?= number_format($diskon, 0, ',', '.') ?>
                                </div>
                            <?php else: ?>
                                Rp <?= number_format($jual, 0, ',', '.') ?>
                            <?php endif; ?>
                        </td>
                        <td class="r <?= $margin >= 0 ? 'margin-pos' : 'margin-neg' ?>">
                            Rp <?= number_format($margin, 0, ',', '.') ?>
                        </td>
                        <td class="r">Rp <?= number_format($subAset, 0, ',', '.') ?></td>
                        <td class="r <?= $subMgn >= 0 ? 'margin-pos' : 'margin-neg' ?>">
                            Rp <?= number_format($subMgn, 0, ',', '.') ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="7" style="text-align:right; font-size:7.5px; text-transform:uppercase; letter-spacing:0.5px;">
                    Total Keseluruhan
                </td>
                <td class="r">Rp <?= number_format($totalAset, 0, ',', '.') ?></td>
                <td class="r">Rp <?= number_format($totalMargin, 0, ',', '.') ?></td>
            </tr>
        </tfoot>
    </table>

    <!-- FOOTER -->
    <div class="page-footer">
        <div class="left">
            * Harga efektif memperhitungkan diskon promo yang sedang aktif pada tanggal cetak.
        </div>
        <div class="right">
            Dokumen dibuat otomatis oleh sistem &mdash; <?= date('d/m/Y H:i') ?> WIB
        </div>
    </div>

</body>

</html>