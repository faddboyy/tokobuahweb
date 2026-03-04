<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: DejaVu Sans, monospace;
            font-size: 10px;
            width: 100%;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .line {
            border-top: 1px dashed #000;
            margin: 5px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            vertical-align: top;
        }

        /* Harga asli yang kena diskon — coret + miring + kecil */
        .harga-asli {
            text-decoration: line-through;
            font-style: italic;
            font-size: 9px;
        }

        /* Baris keterangan potongan diskon — miring + kecil */
        .baris-diskon {
            font-style: italic;
            font-size: 9px;
        }

        /* Harga / subtotal setelah diskon — bold */
        .harga-diskon {
            font-weight: bold;
        }

        /* Baris ringkasan diskon di footer — bold */
        .row-diskon td {
            font-weight: bold;
        }

        /* Label [PROMO] kecil di samping nama barang — miring */
        .label-promo {
            font-size: 8px;
            font-style: italic;
        }
    </style>
</head>

<body>

    <!-- ==================== HEADER STRUK ==================== -->
    <div class="text-center">
        <strong><?= strtoupper($transaksi['nama_cabang']) ?></strong><br>
        TOKO BUAH SEGAR<br>
        Faktur: <?= $transaksi['faktur'] ?><br>
        Operator: <?= $transaksi['nama_operator'] ?>
    </div>

    <div class="line"></div>

    <!-- ==================== DAFTAR ITEM ==================== -->
    <?php
    $subtotal_asli     = 0; // total semua item pakai harga_satuan x qty (sebelum diskon apapun)
    $total_diskon_item = 0; // akumulasi potongan dari diskon promo per item
    ?>

    <table>
        <?php foreach ($items as $item):
            $adaDiskon    = !empty($item['nominal_diskon']) && (float)$item['nominal_diskon'] > 0;
            $hargaEfektif = $adaDiskon ? (float)$item['harga_setelah_diskon'] : (float)$item['harga_satuan'];
            $subtotalAsli = (float)$item['harga_satuan'] * (int)$item['qty'];

            $subtotal_asli     += $subtotalAsli;
            $total_diskon_item += $adaDiskon ? (float)$item['nominal_diskon'] * (int)$item['qty'] : 0;
        ?>
            <!-- Nama barang -->
            <tr>
                <td colspan="2">
                    <?= strtoupper($item['nama_barang']) ?>
                    <?php if ($adaDiskon): ?>
                        <span class="label-promo">[PROMO]</span>
                    <?php endif; ?>
                </td>
            </tr>

            <?php if ($adaDiskon): ?>
                <!-- Harga asli x qty (coret) -->
                <tr>
                    <td class="harga-asli">
                        <?= $item['qty'] ?> <?= $item['nama_satuan'] ?>
                        x <?= number_format($item['harga_satuan'], 0, ',', '.') ?>
                    </td>
                    <td class="text-right harga-asli">
                        <?= number_format($subtotalAsli, 0, ',', '.') ?>
                    </td>
                </tr>
                <!-- Potongan diskon per satuan -->
                <tr class="baris-diskon">
                    <td>&nbsp;&nbsp;Diskon -<?= number_format($item['nominal_diskon'], 0, ',', '.') ?>/<?= $item['nama_satuan'] ?></td>
                    <td class="text-right">-<?= number_format($item['nominal_diskon'] * $item['qty'], 0, ',', '.') ?></td>
                </tr>
                <!-- Harga efektif x qty (bold) -->
                <tr>
                    <td class="harga-diskon">
                        <?= $item['qty'] ?> <?= $item['nama_satuan'] ?>
                        x <?= number_format($hargaEfektif, 0, ',', '.') ?>
                    </td>
                    <td class="text-right harga-diskon">
                        <?= number_format($item['subtotal'], 0, ',', '.') ?>
                    </td>
                </tr>

            <?php else: ?>
                <!-- Item tanpa diskon -->
                <tr>
                    <td>
                        <?= $item['qty'] ?> <?= $item['nama_satuan'] ?>
                        x <?= number_format($item['harga_satuan'], 0, ',', '.') ?>
                    </td>
                    <td class="text-right">
                        <?= number_format($item['subtotal'], 0, ',', '.') ?>
                    </td>
                </tr>
            <?php endif; ?>

        <?php endforeach; ?>
    </table>

    <div class="line"></div>

    <!-- ==================== RINGKASAN HARGA ==================== -->
    <?php
    /*
     * LOGIKA DISKON:
     * - diskon_promo    = akumulasi (nominal_diskon x qty) dari penjualan_item  → sudah terefleksi di subtotal item
     * - diskon_tambahan = diskon_nominal dari tabel pembayaran (input kasir)    → potongan tambahan dari total
     *
     * nominal_penjualan di header penjualan = SUM(subtotal item) = sudah setelah diskon promo
     * Total akhir yang harus dibayar        = nominal_penjualan - diskon_tambahan
     *
     * Jika keduanya ada → tampilkan terpisah lalu tampilkan "TOTAL DISKON" gabungan
     * Jika hanya salah satu → tampilkan saja yang ada
     */
    $diskon_tambahan    = (float)($transaksi['diskon_nominal'] ?? 0);
    $total_semua_diskon = $total_diskon_item + $diskon_tambahan;
    $total_akhir        = (float)$transaksi['nominal_penjualan'] - $diskon_tambahan;
    ?>

    <table>

        <?php if ($total_semua_diskon > 0): ?>
            <!-- Subtotal kotor sebelum diskon apapun — hanya tampil jika ada diskon -->
            <tr>
                <td>SUBTOTAL</td>
                <td class="text-right"><?= number_format($subtotal_asli, 0, ',', '.') ?></td>
            </tr>
        <?php endif; ?>

        <?php if ($total_diskon_item > 0 && $diskon_tambahan > 0): ?>
            <!-- Kasus: ada keduanya → pisah lalu gabungkan -->
            <tr class="row-diskon">
                <td>DISKON PROMO</td>
                <td class="text-right">-<?= number_format($total_diskon_item, 0, ',', '.') ?></td>
            </tr>
            <tr class="row-diskon">
                <td>DISKON TAMBAHAN</td>
                <td class="text-right">-<?= number_format($diskon_tambahan, 0, ',', '.') ?></td>
            </tr>
            <tr class="row-diskon" style="border-top: 1px dashed #000;">
                <td>TOTAL DISKON</td>
                <td class="text-right">-<?= number_format($total_semua_diskon, 0, ',', '.') ?></td>
            </tr>

        <?php elseif ($total_diskon_item > 0): ?>
            <!-- Kasus: hanya diskon promo -->
            <tr class="row-diskon">
                <td>DISKON PROMO</td>
                <td class="text-right">-<?= number_format($total_diskon_item, 0, ',', '.') ?></td>
            </tr>

        <?php elseif ($diskon_tambahan > 0): ?>
            <!-- Kasus: hanya diskon tambahan kasir -->
            <tr class="row-diskon">
                <td>DISKON</td>
                <td class="text-right">-<?= number_format($diskon_tambahan, 0, ',', '.') ?></td>
            </tr>
        <?php endif; ?>

        <!-- Total akhir yang harus dibayar -->
        <tr style="font-weight: bold;">
            <td>TOTAL</td>
            <td class="text-right"><?= number_format($total_akhir, 0, ',', '.') ?></td>
        </tr>

        <tr>
            <td>BAYAR (<?= strtoupper($transaksi['jenis_pembayaran']) ?>)</td>
            <td class="text-right"><?= number_format($transaksi['nominal_bayar'], 0, ',', '.') ?></td>
        </tr>

        <tr>
            <td>KEMBALIAN</td>
            <td class="text-right"><?= number_format($transaksi['kembalian'], 0, ',', '.') ?></td>
        </tr>

    </table>

    <!-- Pesan hemat — muncul jika ada diskon apapun -->
    <?php if ($total_semua_diskon > 0): ?>
        <div class="line"></div>
        <div class="text-center" style="font-size: 9px; font-style: italic;">
            *** Anda hemat Rp <?= number_format($total_semua_diskon, 0, ',', '.') ?> ***
        </div>
    <?php endif; ?>

    <div class="line"></div>

    <div class="text-center">
        Terima Kasih<br>
    </div>

</body>

</html>