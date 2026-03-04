<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Print Barcode</title>
    <style>
        @page {
            margin: 0;
            size: 10cm 5cm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, Helvetica, sans-serif;
            background: white;
            /* Pastikan tidak ada whitespace dari body */
            font-size: 0;
            line-height: 0;
        }

        /* ── Satu label = satu halaman cetak ── */
        .barcode-label {
            width: 10cm;
            height: 5cm;
            /* Tidak ada margin/padding di luar — gap = 0 */
            margin: 0;
            padding: 0;
            padding-top: 0.3cm;
            padding-bottom: 0.2cm;
            padding-left: 0.3cm;
            padding-right: 0.3cm;
            position: relative;
            /* overflow:hidden agar badge promo tidak keluar batas */
            overflow: hidden;
            display: block;
            text-align: center;
            /* page-break-after memastikan tiap label = 1 halaman, tanpa gap */
            page-break-after: always;
            page-break-inside: avoid;
            border: 0.5pt dashed #bbb;
            /* Reset font-size yang di-nol-kan dari body */
            font-size: 10pt;
            line-height: 1.3;
        }

        .barcode-label:last-child {
            page-break-after: avoid;
        }

        @media print {
            .barcode-label {
                border: none;
            }
        }

        /* ── Badge PROMO: sudut kanan atas, di dalam overflow:hidden ── */
        .promo-badge {
            position: absolute;
            top: 0;
            right: 0;
            background: #000;
            color: #fff;
            font-size: 6.5pt;
            font-weight: bold;
            /* Ribbon miring sudut kanan atas */
            padding: 3px 8px;
            border-bottom-left-radius: 4px;
            letter-spacing: 0.3px;
            line-height: 1.4;
            white-space: nowrap;
        }

        /* ── Nama produk ── */
        .product-name {
            font-size: 11pt;
            font-weight: bold;
            color: #111;
            line-height: 1.2;
            margin-bottom: 3px;
            /* Maks 1 baris, potong jika terlalu panjang */
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* ── Barcode ── */
        .barcode-img {
            display: block;
            margin: 3px auto;
            max-width: 7.8cm;
            height: 1.3cm;
            object-fit: contain;
        }

        /* ── Nomor barcode ── */
        .barcode-num {
            font-family: 'Courier New', monospace;
            font-size: 7.5pt;
            font-weight: 600;
            color: #333;
            letter-spacing: 1.5px;
            margin-bottom: 3px;
        }

        /* ── Harga normal (tidak ada diskon) ── */
        .price-normal {
            font-size: 13pt;
            font-weight: bold;
            color: #111;
            margin-top: 2px;
        }

        /* ── Blok harga dengan diskon ── */
        .price-row {
            display: block;
            margin-top: 2px;
            line-height: 1.3;
        }

        /* Harga asli dicoret, miring, kecil */
        .price-asli {
            font-size: 8pt;
            color: #666;
            text-decoration: line-through;
            font-style: italic;
            display: inline;
            margin-right: 4px;
        }

        /* Hemat berapa — inline di samping harga asli */
        .hemat-label {
            font-size: 7pt;
            color: #333;
            font-style: italic;
            display: inline;
        }

        /* Harga setelah diskon — besar & bold */
        .price-diskon {
            font-size: 14pt;
            font-weight: bold;
            color: #111;
            display: block;
            line-height: 1.2;
        }

        .price-satuan {
            font-size: 7pt;
            color: #555;
            display: block;
        }

        /* ── Watermark pojok kiri bawah ── */
        .watermark {
            position: absolute;
            bottom: 2px;
            left: 4px;
            font-size: 5.5pt;
            color: #bbb;
        }
    </style>
</head>

<body><?php
        // Tidak ada whitespace antara </body> dan tag PHP — penting agar tidak ada gap
        foreach ($items as $item):
            $adaDiskon   = !empty($item['nominal_diskon']) && (float)$item['nominal_diskon'] > 0;
            $hargaDiskon = $adaDiskon ? (float)$item['harga'] - (float)$item['nominal_diskon'] : (float)$item['harga'];
            $hemat       = $adaDiskon ? (float)$item['nominal_diskon'] : 0;
        ?><div class="barcode-label"><?php if ($adaDiskon): ?><div class="promo-badge">PROMO -Rp <?= number_format($hemat, 0, ',', '.') ?></div><?php endif; ?><div class="product-name"><?= esc($item['nama']) ?></div><img class="barcode-img" src="<?= $item['barcode_image'] ?>" alt="barcode">
            <div class="barcode-num"><?= esc($item['barcode']) ?></div><?php if ($adaDiskon): ?><div class="price-row"><span class="price-asli">Rp <?= number_format($item['harga'], 0, ',', '.') ?> / <?= esc($item['nama_satuan']) ?></span><span class="hemat-label">(hemat Rp <?= number_format($hemat, 0, ',', '.') ?>)</span><span class="price-diskon">Rp <?= number_format($hargaDiskon, 0, ',', '.') ?></span><span class="price-satuan">/ <?= esc($item['nama_satuan']) ?></span></div><?php else: ?><div class="price-normal">Rp <?= number_format($item['harga'], 0, ',', '.') ?> / <?= esc($item['nama_satuan']) ?></div><?php endif; ?><div class="watermark">POS System</div>
        </div><?php endforeach; ?></body>

</html>