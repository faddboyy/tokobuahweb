-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Mar 04, 2026 at 07:01 PM
-- Server version: 8.4.3
-- PHP Version: 8.3.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tokobuah`
--

-- --------------------------------------------------------

--
-- Table structure for table `barang`
--

CREATE TABLE `barang` (
  `id` int NOT NULL,
  `barcode` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `nama` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `jenis_id` int NOT NULL,
  `satuan_id` int NOT NULL,
  `harga_pokok` decimal(15,2) NOT NULL,
  `harga_jual` decimal(15,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `barang`
--

INSERT INTO `barang` (`id`, `barcode`, `nama`, `jenis_id`, `satuan_id`, `harga_pokok`, `harga_jual`) VALUES
(182, '1010101010', 'Semangka Merah', 20, 14, 24000.00, 28000.00),
(183, '2020202020', 'Semangka Kuning', 20, 14, 32000.00, 37000.00),
(184, '3030303030', 'Salak Madu ', 22, 14, 10000.00, 16000.00),
(185, '4040404040', 'Jeruk Pontianak', 21, 14, 7500.00, 11500.00);

-- --------------------------------------------------------

--
-- Table structure for table `barang_masuk`
--

CREATE TABLE `barang_masuk` (
  `id` int NOT NULL,
  `kode_masuk` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `pengiriman_gudang_id` int UNSIGNED DEFAULT NULL,
  `waktu_masuk` timestamp NOT NULL,
  `operator_id` int NOT NULL,
  `cabang_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `barang_masuk`
--

INSERT INTO `barang_masuk` (`id`, `kode_masuk`, `pengiriman_gudang_id`, `waktu_masuk`, `operator_id`, `cabang_id`) VALUES
(11, 'BM-20260302004050', 1, '2026-03-01 17:40:50', 17, 10),
(12, 'BM-20260302145031', 3, '2026-03-02 07:50:31', 17, 10);

-- --------------------------------------------------------

--
-- Table structure for table `barang_masuk_item`
--

CREATE TABLE `barang_masuk_item` (
  `id` int NOT NULL,
  `barang_masuk_id` int NOT NULL,
  `barang_id` int NOT NULL,
  `qty_kiriman` int NOT NULL,
  `qty_aktual` decimal(15,2) NOT NULL,
  `selisih` decimal(15,2) NOT NULL,
  `satuan_kirim` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `satuan_simpan` varchar(10) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `barang_masuk_item`
--

INSERT INTO `barang_masuk_item` (`id`, `barang_masuk_id`, `barang_id`, `qty_kiriman`, `qty_aktual`, `selisih`, `satuan_kirim`, `satuan_simpan`) VALUES
(14, 11, 182, 8, 16.00, 0.00, 'Dus', 'Kg'),
(15, 11, 183, 5, 9.00, -1.00, 'Dus', 'Kg'),
(16, 12, 182, 10, 18.00, -2.00, 'Dus', 'Kg');

-- --------------------------------------------------------

--
-- Table structure for table `cabang`
--

CREATE TABLE `cabang` (
  `id` int NOT NULL,
  `nama` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cabang`
--

INSERT INTO `cabang` (`id`, `nama`) VALUES
(10, 'Toko Satu'),
(11, 'Toko Dua');

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE `customer` (
  `id` int NOT NULL,
  `nama` varchar(150) NOT NULL,
  `alamat` text NOT NULL,
  `telepon` varchar(20) NOT NULL,
  `cabang_id` int DEFAULT NULL,
  `added_by` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`id`, `nama`, `alamat`, `telepon`, `cabang_id`, `added_by`) VALUES
(10, 'Tn Van Den Wijk', 'Pantai Indah Kapuk Golf Island Blok Emerald No. 12, Jakarta Utara', '081234567890', NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `diskon_terbatas`
--

CREATE TABLE `diskon_terbatas` (
  `id` int UNSIGNED NOT NULL,
  `nama` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `cabang_id` int UNSIGNED NOT NULL,
  `tgl_mulai` date NOT NULL,
  `tgl_selesai` date NOT NULL,
  `status` enum('aktif','nonaktif') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'aktif',
  `created_by` int UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `diskon_terbatas`
--

INSERT INTO `diskon_terbatas` (`id`, `nama`, `cabang_id`, `tgl_mulai`, `tgl_selesai`, `status`, `created_by`, `created_at`) VALUES
(2, 'Promo Ramadhan 2026', 10, '2026-03-02', '2026-03-09', 'aktif', 1, '2026-03-03 07:17:37');

-- --------------------------------------------------------

--
-- Table structure for table `diskon_terbatas_item`
--

CREATE TABLE `diskon_terbatas_item` (
  `id` int UNSIGNED NOT NULL,
  `diskon_terbatas_id` int UNSIGNED NOT NULL,
  `barang_id` int UNSIGNED NOT NULL,
  `nominal_diskon` decimal(15,2) NOT NULL DEFAULT '0.00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `diskon_terbatas_item`
--

INSERT INTO `diskon_terbatas_item` (`id`, `diskon_terbatas_id`, `barang_id`, `nominal_diskon`) VALUES
(6, 2, 182, 2500.00);

-- --------------------------------------------------------

--
-- Table structure for table `gudang_utama`
--

CREATE TABLE `gudang_utama` (
  `id` int NOT NULL,
  `nama` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `mandor_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gudang_utama`
--

INSERT INTO `gudang_utama` (`id`, `nama`, `mandor_id`) VALUES
(3, 'Gudang Utama', 2);

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE `inventory` (
  `id` int NOT NULL,
  `barang_id` int NOT NULL,
  `cabang_id` int NOT NULL,
  `stock` decimal(15,2) NOT NULL DEFAULT '0.00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory`
--

INSERT INTO `inventory` (`id`, `barang_id`, `cabang_id`, `stock`) VALUES
(91, 185, 10, 494.00),
(92, 182, 10, 423.00),
(93, 183, 10, 171.00),
(94, 184, 10, 279.50),
(95, 182, 11, 0.00),
(96, 183, 11, 0.00),
(97, 185, 11, 0.00),
(98, 184, 11, 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `jenis`
--

CREATE TABLE `jenis` (
  `id` int NOT NULL,
  `nama` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jenis`
--

INSERT INTO `jenis` (`id`, `nama`) VALUES
(20, 'Semangka'),
(21, 'Jeruk'),
(22, 'Salak');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` bigint UNSIGNED NOT NULL,
  `version` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `class` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `group` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `namespace` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `time` int NOT NULL,
  `batch` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `version`, `class`, `group`, `namespace`, `time`, `batch`) VALUES
(1, '2026-02-12-141352', 'App\\Database\\Migrations\\InitSchema', 'default', 'App', 1770905700, 1),
(2, '2026-02-23-105755', 'App\\Database\\Migrations\\CreatePurchasingSchema', 'default', 'App', 1771844743, 2),
(3, '2026-02-23-200038', 'App\\Database\\Migrations\\AlterGudangUtamaTable', 'default', 'App', 1771877022, 3),
(4, '2026-02-23-201138', 'App\\Database\\Migrations\\RenamePreOrderToSuratJalan', 'default', 'App', 1771877518, 4),
(5, '2026-02-25-160718', 'App\\Database\\Migrations\\RefactorPenerimaanGudang', 'default', 'App', 1772035756, 5),
(6, '2026-02-26-181251', 'App\\Database\\Migrations\\CreateStokGudang', 'default', 'App', 1772129594, 6),
(7, '2026-02-26-182231', 'App\\Database\\Migrations\\AddKodeSuratJalanToPenerimaanGudang', 'default', 'App', 1772130172, 7),
(8, '2026-03-01-161406', 'App\\Database\\Migrations\\CreatePengirimanGudang', 'default', 'App', 1772381659, 8),
(9, '2026-03-01-172710', 'App\\Database\\Migrations\\RefactorBarangMasuk', 'default', 'App', 1772386067, 9);

-- --------------------------------------------------------

--
-- Table structure for table `pembayaran`
--

CREATE TABLE `pembayaran` (
  `id` int NOT NULL,
  `jenis_pembayaran` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `diskon_persen` decimal(5,2) DEFAULT NULL,
  `diskon_nominal` decimal(15,2) DEFAULT NULL,
  `nominal_bayar` decimal(15,2) NOT NULL,
  `kembalian` decimal(15,2) NOT NULL DEFAULT '0.00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pembayaran`
--

INSERT INTO `pembayaran` (`id`, `jenis_pembayaran`, `diskon_persen`, `diskon_nominal`, `nominal_bayar`, `kembalian`) VALUES
(30, 'tunai', NULL, 0.00, 1600000.00, 79000.00),
(31, 'qris', NULL, 0.00, 1204000.00, 0.00),
(32, 'qris', NULL, 0.00, 616000.00, 0.00),
(33, 'qris', NULL, 0.00, 588000.00, 0.00),
(34, 'qris', NULL, 0.00, 160000.00, 0.00),
(35, 'tunai', NULL, 0.00, 161000.00, 500.00),
(36, 'qris', NULL, 0.00, 370000.00, 0.00),
(37, 'transfer', NULL, 10000.00, 450000.00, 16000.00),
(38, 'transfer', NULL, 99000.00, 1230000.00, 100000.00),
(39, 'qris', NULL, 0.00, 140000.00, 0.00),
(40, 'qris', NULL, 0.00, 56000.00, 0.00),
(41, 'qris', NULL, 0.00, 28000.00, 0.00),
(42, 'qris', NULL, 0.00, 28000.00, 0.00),
(43, 'qris', NULL, 10000.00, 316500.00, 10000.00),
(44, 'qris', NULL, 0.00, 46000.00, 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `penerimaan_gudang`
--

CREATE TABLE `penerimaan_gudang` (
  `id` int NOT NULL,
  `kode_penerimaan` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `surat_jalan_id` int NOT NULL,
  `kode_supplier` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `gudang_id` int NOT NULL,
  `waktu_penerimaan` timestamp NOT NULL,
  `operator_id` int NOT NULL,
  `status` enum('ditoko','digudang','dibatalkan') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `penerimaan_gudang`
--

INSERT INTO `penerimaan_gudang` (`id`, `kode_penerimaan`, `surat_jalan_id`, `kode_supplier`, `gudang_id`, `waktu_penerimaan`, `operator_id`, `status`, `created_at`) VALUES
(1, 'PG-20260227020833', 11, 'SJ-2022270111298', 3, '2026-02-26 19:08:33', 2, 'digudang', '2026-02-26 19:08:33'),
(2, 'PG-20260227032155', 13, 'SJ-1212130099', 3, '2026-02-26 20:21:55', 2, 'digudang', '2026-02-26 20:21:55'),
(3, 'PG-20260227035510', 14, 'SJ-12818318318031', 3, '2026-02-26 20:55:10', 2, 'digudang', '2026-02-26 20:55:10'),
(4, 'PG-20260227231459', 15, 'SJ-10920910229', 3, '2026-02-27 16:14:59', 2, 'digudang', '2026-02-27 16:14:59'),
(5, 'PG-20260227232806', 16, 'SJ-989829812109', 3, '2026-02-27 16:28:06', 2, 'digudang', '2026-02-27 16:28:06'),
(6, 'PG-20260227233517', 17, 'SJ-12019021209', 3, '2026-02-27 16:35:17', 2, 'digudang', '2026-02-27 16:35:17'),
(7, 'PG-20260302144208', 18, 'Sj-61', 3, '2026-03-02 07:42:08', 2, 'digudang', '2026-03-02 07:42:08'),
(8, 'PG-20260302144405', 19, 'Sj-004', 3, '2026-03-02 07:44:05', 2, 'digudang', '2026-03-02 07:44:05');

-- --------------------------------------------------------

--
-- Table structure for table `penerimaan_gudang_item`
--

CREATE TABLE `penerimaan_gudang_item` (
  `id` int NOT NULL,
  `penerimaan_gudang_id` int NOT NULL,
  `barang_id` int NOT NULL,
  `qty_dipesan` int NOT NULL,
  `qty_diterima` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `penerimaan_gudang_item`
--

INSERT INTO `penerimaan_gudang_item` (`id`, `penerimaan_gudang_id`, `barang_id`, `qty_dipesan`, `qty_diterima`) VALUES
(1, 1, 183, 20, 18),
(2, 1, 182, 10, 9),
(3, 2, 184, 50, 48),
(4, 2, 185, 40, 39),
(5, 3, 184, 10, 10),
(6, 4, 182, 20, 19),
(7, 5, 184, 30, 29),
(8, 6, 185, 10, 9),
(9, 7, 182, 20, 15),
(10, 8, 182, 5, 2);

-- --------------------------------------------------------

--
-- Table structure for table `pengiriman_gudang`
--

CREATE TABLE `pengiriman_gudang` (
  `id` int UNSIGNED NOT NULL,
  `kode_pengiriman` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `gudang_id` int UNSIGNED NOT NULL,
  `cabang_id` int UNSIGNED NOT NULL,
  `operator_id` int UNSIGNED NOT NULL,
  `waktu_pengiriman` timestamp NOT NULL,
  `status` enum('dikirim','diterima','dibatalkan') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'dikirim',
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pengiriman_gudang`
--

INSERT INTO `pengiriman_gudang` (`id`, `kode_pengiriman`, `gudang_id`, `cabang_id`, `operator_id`, `waktu_pengiriman`, `status`, `created_at`) VALUES
(1, 'KRG-20260301234345', 3, 10, 2, '2026-03-01 16:43:45', 'diterima', '2026-03-01 16:43:45'),
(2, 'KRG-20260302000503', 3, 11, 2, '2026-03-01 17:05:03', 'dikirim', '2026-03-01 17:05:03'),
(3, 'KRG-20260302144818', 3, 10, 2, '2026-03-02 07:48:18', 'diterima', '2026-03-02 07:48:18');

-- --------------------------------------------------------

--
-- Table structure for table `pengiriman_gudang_item`
--

CREATE TABLE `pengiriman_gudang_item` (
  `id` int UNSIGNED NOT NULL,
  `pengiriman_gudang_id` int UNSIGNED NOT NULL,
  `barang_id` int UNSIGNED NOT NULL,
  `qty` decimal(15,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pengiriman_gudang_item`
--

INSERT INTO `pengiriman_gudang_item` (`id`, `pengiriman_gudang_id`, `barang_id`, `qty`) VALUES
(1, 1, 182, 8.00),
(2, 1, 183, 5.00),
(3, 2, 184, 20.00),
(4, 2, 185, 18.00),
(5, 3, 182, 10.00);

-- --------------------------------------------------------

--
-- Table structure for table `penjualan`
--

CREATE TABLE `penjualan` (
  `id` int NOT NULL,
  `faktur` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `pembayaran_id` int DEFAULT NULL,
  `nominal_penjualan` decimal(15,2) NOT NULL,
  `operator_id` int NOT NULL,
  `cabang_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `customer_id` int DEFAULT NULL,
  `print_out` tinyint NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `penjualan`
--

INSERT INTO `penjualan` (`id`, `faktur`, `pembayaran_id`, `nominal_penjualan`, `operator_id`, `cabang_id`, `created_at`, `customer_id`, `print_out`) VALUES
(17, 'PJ-20260225032553', 30, 1521000.00, 17, 10, '2026-02-24 20:25:53', 10, 0),
(18, 'PJ-20260225162657', 31, 1204000.00, 17, 10, '2026-02-25 09:26:57', NULL, 0),
(19, 'DRAFT-20260225162727', NULL, 28000.00, 17, 10, NULL, NULL, 0),
(20, 'PJ-20260225214303', 32, 616000.00, 17, 10, '2026-02-25 14:43:03', NULL, 0),
(21, 'PJ-20260225214902', 33, 588000.00, 17, 10, '2026-02-25 14:49:02', NULL, 0),
(22, 'PJ-20260225220114', 34, 160000.00, 17, 10, '2026-02-25 15:01:14', NULL, 0),
(23, 'PJ-20260225220333', 35, 160500.00, 17, 10, '2026-02-25 15:03:33', NULL, 0),
(24, 'PJ-20260225220358', 36, 370000.00, 17, 10, '2026-02-25 15:03:58', NULL, 0),
(25, 'PJ-20260225224416', 37, 444000.00, 17, 10, '2026-02-25 15:44:16', NULL, 0),
(26, 'PJ-20260225224912', 38, 1229000.00, 17, 10, '2026-02-25 15:49:12', 10, 0),
(27, 'DRAFT-20260225224912', NULL, 0.00, 17, 10, NULL, NULL, 0),
(28, 'DRAFT-20260227105453', NULL, 0.00, 17, 10, NULL, NULL, 0),
(29, 'PJ-20260302145244', 39, 140000.00, 17, 10, '2026-03-02 07:52:44', NULL, 0),
(30, 'PJ-20260302145304', 40, 56000.00, 17, 10, '2026-03-02 07:53:04', NULL, 0),
(31, 'PJ-20260302145740', 41, 28000.00, 17, 10, '2026-03-02 07:57:40', NULL, 0),
(32, 'PJ-20260302150219', 42, 28000.00, 17, 10, '2026-03-02 08:02:19', NULL, 0),
(33, 'DRAFT-20260302150219', NULL, 28000.00, 17, 10, NULL, NULL, 0),
(34, 'PJ-20260303015151', 43, 316500.00, 17, 10, '2026-03-02 18:51:51', NULL, 0),
(35, 'DRAFT-20260303015151', NULL, 0.00, 17, 10, NULL, NULL, 0),
(36, 'PJ-20260303141913', 44, 46000.00, 17, 10, '2026-03-03 07:19:13', NULL, 0),
(37, 'DRAFT-20260303141913', NULL, 0.00, 17, 10, NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `penjualan_item`
--

CREATE TABLE `penjualan_item` (
  `id` int NOT NULL,
  `penjualan_id` int DEFAULT NULL,
  `inventory_id` int NOT NULL,
  `harga_satuan` decimal(15,2) NOT NULL,
  `nominal_diskon` decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT 'Nominal diskon per satuan dari diskon_terbatas (0 = tidak ada diskon)',
  `harga_setelah_diskon` decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT 'harga_satuan - nominal_diskon (harga efektif yang dipakai untuk subtotal)',
  `qty` int NOT NULL,
  `subtotal` decimal(15,2) NOT NULL,
  `keterangan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `penjualan_item`
--

INSERT INTO `penjualan_item` (`id`, `penjualan_id`, `inventory_id`, `harga_satuan`, `nominal_diskon`, `harga_setelah_diskon`, `qty`, `subtotal`, `keterangan`) VALUES
(1, 17, 91, 11500.00, 0.00, 11500.00, 30, 345000.00, NULL),
(2, 17, 92, 28000.00, 0.00, 28000.00, 42, 1176000.00, NULL),
(3, 18, 92, 28000.00, 0.00, 28000.00, 43, 1204000.00, NULL),
(4, 19, 92, 28000.00, 0.00, 28000.00, 1, 28000.00, NULL),
(5, 20, 92, 28000.00, 0.00, 28000.00, 22, 616000.00, NULL),
(7, 21, 92, 28000.00, 0.00, 28000.00, 21, 588000.00, NULL),
(10, 22, 94, 16000.00, 0.00, 16000.00, 10, 160000.00, NULL),
(11, 23, 91, 11500.00, 0.00, 11500.00, 7, 80500.00, NULL),
(12, 23, 94, 16000.00, 0.00, 16000.00, 5, 80000.00, NULL),
(13, 24, 93, 37000.00, 0.00, 37000.00, 10, 370000.00, NULL),
(14, 25, 93, 37000.00, 0.00, 37000.00, 12, 444000.00, NULL),
(15, 26, 92, 28000.00, 0.00, 28000.00, 15, 420000.00, NULL),
(16, 26, 93, 37000.00, 0.00, 37000.00, 14, 518000.00, NULL),
(17, 26, 94, 16000.00, 0.00, 16000.00, 11, 176000.00, NULL),
(18, 26, 91, 11500.00, 0.00, 11500.00, 10, 115000.00, NULL),
(19, 29, 92, 28000.00, 0.00, 28000.00, 5, 140000.00, NULL),
(20, 30, 92, 28000.00, 0.00, 28000.00, 2, 56000.00, NULL),
(21, 31, 92, 28000.00, 0.00, 28000.00, 1, 28000.00, NULL),
(22, 32, 92, 28000.00, 0.00, 28000.00, 1, 28000.00, NULL),
(23, 33, 92, 28000.00, 0.00, 28000.00, 1, 28000.00, NULL),
(24, 34, 92, 28000.00, 3500.00, 24500.00, 5, 122500.00, NULL),
(25, 34, 91, 11500.00, 1500.00, 10000.00, 4, 40000.00, NULL),
(26, 34, 94, 16000.00, 0.00, 16000.00, 5, 80000.00, NULL),
(27, 34, 93, 37000.00, 0.00, 37000.00, 2, 74000.00, NULL),
(28, 36, 92, 28000.00, 5000.00, 23000.00, 2, 46000.00, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `satuan`
--

CREATE TABLE `satuan` (
  `id` int NOT NULL,
  `nama` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `satuan`
--

INSERT INTO `satuan` (`id`, `nama`) VALUES
(14, 'Kg'),
(15, 'Kwintal'),
(16, 'Dus');

-- --------------------------------------------------------

--
-- Table structure for table `stok_gudang`
--

CREATE TABLE `stok_gudang` (
  `id` int NOT NULL,
  `gudang_id` int NOT NULL,
  `barang_id` int NOT NULL,
  `satuan_id` int NOT NULL,
  `stock` decimal(15,2) NOT NULL DEFAULT '0.00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stok_gudang`
--

INSERT INTO `stok_gudang` (`id`, `gudang_id`, `barang_id`, `satuan_id`, `stock`) VALUES
(1, 3, 183, 16, 13.00),
(2, 3, 182, 16, 27.00),
(3, 3, 184, 16, 67.00),
(4, 3, 185, 16, 30.00);

-- --------------------------------------------------------

--
-- Table structure for table `suplier`
--

CREATE TABLE `suplier` (
  `id` int NOT NULL,
  `nama` varchar(150) NOT NULL,
  `alamat` text NOT NULL,
  `telepon` varchar(20) NOT NULL,
  `email` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `suplier`
--

INSERT INTO `suplier` (`id`, `nama`, `alamat`, `telepon`, `email`) VALUES
(6, 'PT Sumber Buah Nusantara', 'Jl Jend Achmad Yani, Cimahi, Jawa Barat\n', '082276543210', 'marketing@sumberbuah.co.id');

-- --------------------------------------------------------

--
-- Table structure for table `surat_jalan`
--

CREATE TABLE `surat_jalan` (
  `id` int NOT NULL,
  `kode_po` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `suplier_id` int NOT NULL,
  `gudang_id` int NOT NULL,
  `waktu_po` timestamp NOT NULL,
  `status` enum('order','selesai','dibatalkan') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'order',
  `total_nominal` decimal(15,2) NOT NULL DEFAULT '0.00',
  `operator_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `surat_jalan`
--

INSERT INTO `surat_jalan` (`id`, `kode_po`, `suplier_id`, `gudang_id`, `waktu_po`, `status`, `total_nominal`, `operator_id`) VALUES
(11, 'PO-20260227011855', 6, 3, '2026-02-26 18:16:55', 'selesai', 1760000.00, 2),
(12, 'PO-20260227014053', 6, 3, '2026-02-26 18:40:53', 'dibatalkan', 0.00, 2),
(13, 'PO-20260227023054', 6, 3, '2026-02-26 19:09:37', 'selesai', 800000.00, 2),
(14, 'PO-20260227034515', 6, 3, '2026-02-26 20:44:50', 'selesai', 200000.00, 2),
(15, 'PO-20260227223747', 6, 3, '2026-02-27 15:37:10', 'selesai', 840000.00, 2),
(16, 'PO-20260227230023', 6, 3, '2026-02-27 15:45:26', 'selesai', 600000.00, 2),
(17, 'PO-20260227233233', 6, 3, '2026-02-27 16:31:50', 'selesai', 150000.00, 2),
(18, 'PO-20260302144050', 6, 3, '2026-03-02 07:40:20', 'selesai', 2000000.00, 2),
(19, 'PO-20260302144318', 6, 3, '2026-03-02 07:42:58', 'selesai', 500000.00, 2),
(20, 'PO-20260303020503', 6, 3, '2026-03-02 19:03:53', 'order', 98000.00, 2);

-- --------------------------------------------------------

--
-- Table structure for table `surat_jalan_item`
--

CREATE TABLE `surat_jalan_item` (
  `id` int NOT NULL,
  `surat_jalan_id` int NOT NULL,
  `barang_id` int NOT NULL,
  `satuan_id` int NOT NULL,
  `harga_beli` decimal(15,2) NOT NULL,
  `qty` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `surat_jalan_item`
--

INSERT INTO `surat_jalan_item` (`id`, `surat_jalan_id`, `barang_id`, `satuan_id`, `harga_beli`, `qty`) VALUES
(20, 11, 182, 16, 480000.00, 10),
(21, 11, 183, 16, 1280000.00, 20),
(23, 13, 185, 16, 300000.00, 40),
(24, 13, 184, 16, 500000.00, 50),
(26, 14, 184, 16, 200000.00, 10),
(27, 15, 182, 16, 840000.00, 20),
(28, 16, 184, 16, 600000.00, 30),
(29, 17, 185, 16, 150000.00, 10),
(30, 18, 182, 16, 2000000.00, 20),
(31, 19, 182, 16, 500000.00, 5),
(32, 20, 184, 16, 20000.00, 1),
(33, 20, 185, 16, 30000.00, 2),
(34, 20, 182, 16, 48000.00, 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `nama` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `cabang_id` int DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `role` enum('owner','admin','petugas') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'petugas'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `nama`, `password`, `cabang_id`, `is_active`, `role`) VALUES
(1, 'owner', 'Pak Bos', '$2y$12$RUAubh5i/CDNzS4S2SseSOKJKe/NlmwGQJzFIv8sTAdoaBYMF2wvW', NULL, 1, 'owner'),
(2, 'admin', 'Admin', '$2y$12$LMb6P2McsrqnrlRr1J8GWuLQ9GGkbEwct2uO9ss8tLwaJrpbdJDzW', NULL, 1, 'admin'),
(17, 'p0101', 'Petugas Toko Satu (1)', '$2y$12$e05pw.0XppmUJV66QP2AzeOZmD58UfhiKXXJQLRTkORjTzDopnTQO', 10, 1, 'petugas'),
(18, 'p0201', 'Petugas Toko Dua (1)', '$2y$12$132.h4TdSWyeaUKbnFoXF.gUAIOz4nMpYpXFRmZTe18/7Dn/cUSlO', 11, 1, 'petugas'),
(19, 'p0102', 'Petugas Toko Satu (2)', '$2y$12$ybQU0hxnxwR1/b46p80jVeFcuC3AkvZdvXqU5jiPQRv77NquTtfDm', 10, 1, 'petugas'),
(20, 'p0202', 'Petugas Toko Dua (2)', '$2y$12$NhHA4UX.sZCPKjYU0xSe0.1drOpK8JUF.sc.rPxzHPdK0sIvzd0gq', 11, 1, 'petugas');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `barang`
--
ALTER TABLE `barang`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `barcode` (`barcode`),
  ADD KEY `barang_jenis_id_foreign` (`jenis_id`),
  ADD KEY `barang_satuan_id_foreign` (`satuan_id`);

--
-- Indexes for table `barang_masuk`
--
ALTER TABLE `barang_masuk`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode_masuk` (`kode_masuk`),
  ADD KEY `barang_masuk_operator_id_foreign` (`operator_id`),
  ADD KEY `cabang_id` (`cabang_id`),
  ADD KEY `barang_masuk_pengiriman_gudang_id_index` (`pengiriman_gudang_id`);

--
-- Indexes for table `barang_masuk_item`
--
ALTER TABLE `barang_masuk_item`
  ADD PRIMARY KEY (`id`),
  ADD KEY `barang_masuk_item_barang_masuk_id_foreign` (`barang_masuk_id`),
  ADD KEY `barang_masuk_item_barang_id_foreign` (`barang_id`);

--
-- Indexes for table `cabang`
--
ALTER TABLE `cabang`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cabang_id` (`cabang_id`),
  ADD KEY `added_by` (`added_by`);

--
-- Indexes for table `diskon_terbatas`
--
ALTER TABLE `diskon_terbatas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cabang_id` (`cabang_id`),
  ADD KEY `tgl_mulai_tgl_selesai` (`tgl_mulai`,`tgl_selesai`);

--
-- Indexes for table `diskon_terbatas_item`
--
ALTER TABLE `diskon_terbatas_item`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `diskon_terbatas_id_barang_id` (`diskon_terbatas_id`,`barang_id`),
  ADD KEY `diskon_terbatas_id` (`diskon_terbatas_id`),
  ADD KEY `barang_id` (`barang_id`);

--
-- Indexes for table `gudang_utama`
--
ALTER TABLE `gudang_utama`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`id`),
  ADD KEY `inventory_barang_id_foreign` (`barang_id`),
  ADD KEY `inventory_cabang_id_foreign` (`cabang_id`);

--
-- Indexes for table `jenis`
--
ALTER TABLE `jenis`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `penerimaan_gudang`
--
ALTER TABLE `penerimaan_gudang`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode_penerimaan` (`kode_penerimaan`),
  ADD UNIQUE KEY `kode_surat_jalan` (`kode_supplier`),
  ADD KEY `penerimaan_gudang_surat_jalan_id_foreign` (`surat_jalan_id`),
  ADD KEY `penerimaan_gudang_gudang_id_foreign` (`gudang_id`),
  ADD KEY `penerimaan_gudang_operator_id_foreign` (`operator_id`);

--
-- Indexes for table `penerimaan_gudang_item`
--
ALTER TABLE `penerimaan_gudang_item`
  ADD PRIMARY KEY (`id`),
  ADD KEY `penerimaan_gudang_item_penerimaan_gudang_id_foreign` (`penerimaan_gudang_id`),
  ADD KEY `penerimaan_gudang_item_barang_id_foreign` (`barang_id`);

--
-- Indexes for table `pengiriman_gudang`
--
ALTER TABLE `pengiriman_gudang`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode_pengiriman` (`kode_pengiriman`),
  ADD KEY `gudang_id` (`gudang_id`),
  ADD KEY `cabang_id` (`cabang_id`),
  ADD KEY `operator_id` (`operator_id`);

--
-- Indexes for table `pengiriman_gudang_item`
--
ALTER TABLE `pengiriman_gudang_item`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pengiriman_gudang_id` (`pengiriman_gudang_id`),
  ADD KEY `barang_id` (`barang_id`);

--
-- Indexes for table `penjualan`
--
ALTER TABLE `penjualan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `faktur` (`faktur`),
  ADD KEY `penjualan_pembayaran_id_foreign` (`pembayaran_id`),
  ADD KEY `penjualan_operator_id_foreign` (`operator_id`),
  ADD KEY `penjualan_cabang_id_foreign` (`cabang_id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `penjualan_item`
--
ALTER TABLE `penjualan_item`
  ADD PRIMARY KEY (`id`),
  ADD KEY `penjualan_item_penjualan_id_foreign` (`penjualan_id`),
  ADD KEY `penjualan_item_inventory_id_foreign` (`inventory_id`);

--
-- Indexes for table `satuan`
--
ALTER TABLE `satuan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `stok_gudang`
--
ALTER TABLE `stok_gudang`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_stok_gudang` (`gudang_id`,`barang_id`,`satuan_id`),
  ADD KEY `stok_gudang_barang_id_foreign` (`barang_id`),
  ADD KEY `stok_gudang_satuan_id_foreign` (`satuan_id`);

--
-- Indexes for table `suplier`
--
ALTER TABLE `suplier`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `surat_jalan`
--
ALTER TABLE `surat_jalan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode_po` (`kode_po`),
  ADD KEY `pre_order_suplier_id_foreign` (`suplier_id`),
  ADD KEY `pre_order_operator_id_foreign` (`operator_id`),
  ADD KEY `gudang_id` (`gudang_id`);

--
-- Indexes for table `surat_jalan_item`
--
ALTER TABLE `surat_jalan_item`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pre_order_item_pre_order_id_foreign` (`surat_jalan_id`),
  ADD KEY `pre_order_item_barang_id_foreign` (`barang_id`),
  ADD KEY `satuan_id` (`satuan_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `users_cabang_id_foreign` (`cabang_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `barang`
--
ALTER TABLE `barang`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=186;

--
-- AUTO_INCREMENT for table `barang_masuk`
--
ALTER TABLE `barang_masuk`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `barang_masuk_item`
--
ALTER TABLE `barang_masuk_item`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `cabang`
--
ALTER TABLE `cabang`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `diskon_terbatas`
--
ALTER TABLE `diskon_terbatas`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `diskon_terbatas_item`
--
ALTER TABLE `diskon_terbatas_item`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `gudang_utama`
--
ALTER TABLE `gudang_utama`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `inventory`
--
ALTER TABLE `inventory`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=99;

--
-- AUTO_INCREMENT for table `jenis`
--
ALTER TABLE `jenis`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `pembayaran`
--
ALTER TABLE `pembayaran`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `penerimaan_gudang`
--
ALTER TABLE `penerimaan_gudang`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `penerimaan_gudang_item`
--
ALTER TABLE `penerimaan_gudang_item`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `pengiriman_gudang`
--
ALTER TABLE `pengiriman_gudang`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `pengiriman_gudang_item`
--
ALTER TABLE `pengiriman_gudang_item`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `penjualan`
--
ALTER TABLE `penjualan`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `penjualan_item`
--
ALTER TABLE `penjualan_item`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `satuan`
--
ALTER TABLE `satuan`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `stok_gudang`
--
ALTER TABLE `stok_gudang`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `suplier`
--
ALTER TABLE `suplier`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `surat_jalan`
--
ALTER TABLE `surat_jalan`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `surat_jalan_item`
--
ALTER TABLE `surat_jalan_item`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `barang`
--
ALTER TABLE `barang`
  ADD CONSTRAINT `barang_jenis_id_foreign` FOREIGN KEY (`jenis_id`) REFERENCES `jenis` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `barang_satuan_id_foreign` FOREIGN KEY (`satuan_id`) REFERENCES `satuan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `barang_masuk`
--
ALTER TABLE `barang_masuk`
  ADD CONSTRAINT `barang_masuk_ibfk_1` FOREIGN KEY (`cabang_id`) REFERENCES `cabang` (`id`),
  ADD CONSTRAINT `barang_masuk_operator_id_foreign` FOREIGN KEY (`operator_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `barang_masuk_item`
--
ALTER TABLE `barang_masuk_item`
  ADD CONSTRAINT `barang_masuk_item_barang_id_foreign` FOREIGN KEY (`barang_id`) REFERENCES `barang` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `barang_masuk_item_barang_masuk_id_foreign` FOREIGN KEY (`barang_masuk_id`) REFERENCES `barang_masuk` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `customer`
--
ALTER TABLE `customer`
  ADD CONSTRAINT `customer_ibfk_1` FOREIGN KEY (`cabang_id`) REFERENCES `cabang` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `customer_ibfk_2` FOREIGN KEY (`added_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `diskon_terbatas_item`
--
ALTER TABLE `diskon_terbatas_item`
  ADD CONSTRAINT `fk_dti_diskon_terbatas` FOREIGN KEY (`diskon_terbatas_id`) REFERENCES `diskon_terbatas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `inventory`
--
ALTER TABLE `inventory`
  ADD CONSTRAINT `inventory_barang_id_foreign` FOREIGN KEY (`barang_id`) REFERENCES `barang` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `inventory_cabang_id_foreign` FOREIGN KEY (`cabang_id`) REFERENCES `cabang` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `penerimaan_gudang`
--
ALTER TABLE `penerimaan_gudang`
  ADD CONSTRAINT `penerimaan_gudang_gudang_id_foreign` FOREIGN KEY (`gudang_id`) REFERENCES `gudang_utama` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  ADD CONSTRAINT `penerimaan_gudang_operator_id_foreign` FOREIGN KEY (`operator_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `penerimaan_gudang_surat_jalan_id_foreign` FOREIGN KEY (`surat_jalan_id`) REFERENCES `surat_jalan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `penerimaan_gudang_item`
--
ALTER TABLE `penerimaan_gudang_item`
  ADD CONSTRAINT `penerimaan_gudang_item_barang_id_foreign` FOREIGN KEY (`barang_id`) REFERENCES `barang` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `penerimaan_gudang_item_penerimaan_gudang_id_foreign` FOREIGN KEY (`penerimaan_gudang_id`) REFERENCES `penerimaan_gudang` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `pengiriman_gudang_item`
--
ALTER TABLE `pengiriman_gudang_item`
  ADD CONSTRAINT `pengiriman_gudang_item_pengiriman_gudang_id_foreign` FOREIGN KEY (`pengiriman_gudang_id`) REFERENCES `pengiriman_gudang` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `penjualan`
--
ALTER TABLE `penjualan`
  ADD CONSTRAINT `penjualan_cabang_id_foreign` FOREIGN KEY (`cabang_id`) REFERENCES `cabang` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `penjualan_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`id`),
  ADD CONSTRAINT `penjualan_operator_id_foreign` FOREIGN KEY (`operator_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `penjualan_pembayaran_id_foreign` FOREIGN KEY (`pembayaran_id`) REFERENCES `pembayaran` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `penjualan_item`
--
ALTER TABLE `penjualan_item`
  ADD CONSTRAINT `penjualan_item_inventory_id_foreign` FOREIGN KEY (`inventory_id`) REFERENCES `inventory` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `penjualan_item_penjualan_id_foreign` FOREIGN KEY (`penjualan_id`) REFERENCES `penjualan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `stok_gudang`
--
ALTER TABLE `stok_gudang`
  ADD CONSTRAINT `stok_gudang_barang_id_foreign` FOREIGN KEY (`barang_id`) REFERENCES `barang` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `stok_gudang_gudang_id_foreign` FOREIGN KEY (`gudang_id`) REFERENCES `gudang_utama` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `stok_gudang_satuan_id_foreign` FOREIGN KEY (`satuan_id`) REFERENCES `satuan` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT;

--
-- Constraints for table `surat_jalan`
--
ALTER TABLE `surat_jalan`
  ADD CONSTRAINT `fk_surat_jalan_gudang` FOREIGN KEY (`gudang_id`) REFERENCES `gudang_utama` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `pre_order_operator_id_foreign` FOREIGN KEY (`operator_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `pre_order_suplier_id_foreign` FOREIGN KEY (`suplier_id`) REFERENCES `suplier` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `surat_jalan_item`
--
ALTER TABLE `surat_jalan_item`
  ADD CONSTRAINT `pre_order_item_barang_id_foreign` FOREIGN KEY (`barang_id`) REFERENCES `barang` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `pre_order_item_pre_order_id_foreign` FOREIGN KEY (`surat_jalan_id`) REFERENCES `surat_jalan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `surat_jalan_item_ibfk_1` FOREIGN KEY (`satuan_id`) REFERENCES `satuan` (`id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_cabang_id_foreign` FOREIGN KEY (`cabang_id`) REFERENCES `cabang` (`id`) ON DELETE CASCADE ON UPDATE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
