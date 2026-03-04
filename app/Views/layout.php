<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>TokoBuah - <?= $title ?? 'Dashboard'; ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qs/6.11.0/qs.min.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --win-accent: #0067c0;
            --sidebar-width: 260px;
        }

        body {
            background: linear-gradient(135deg, #c3d8e6 0%, #ecd6e3 100%);
            font-family: 'Inter', sans-serif;
            color: #1a1a1a;
            height: 100vh;
            margin: 0;
            display: flex;
            /* Mengaktifkan layout samping */
            overflow: hidden;
        }

        /* SIDEBAR STYLE */
        .sidebar {
            width: var(--sidebar-width);
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(20px) saturate(180%);
            border-right: 1px solid rgba(255, 255, 255, 0.4);
            display: flex;
            flex-direction: column;
            height: 100vh;
            z-index: 10;
        }

        .sidebar-header {
            padding: 2rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 12px;
            color: var(--win-accent);
            font-weight: 800;
            font-size: 1.2rem;
        }

        .sidebar-content {
            flex: 1;
            overflow-y: auto;
            padding: 0 0.8rem;
        }

        .nav-group-label {
            font-size: 0.7rem;
            font-weight: 700;
            color: #777;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 1.5rem 1rem 0.5rem;
        }

        .nav-link-custom {
            color: #444;
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            padding: 0.7rem 1rem;
            border-radius: 10px;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: all 0.2s;
            margin-bottom: 2px;
        }

        .nav-link-custom:hover {
            background: rgba(255, 255, 255, 0.5);
            color: var(--win-accent);
        }

        .nav-link-custom.active {
            background: var(--win-accent);
            color: white;
            box-shadow: 0 4px 12px rgba(0, 103, 192, 0.2);
        }

        /* CONTENT AREA */
        main {
            flex: 1;
            display: flex;
            flex-direction: column;
            padding: 1.25rem;
            min-width: 0;
            overflow-y: auto;
            /* Penting: JANGAN tambahkan filter blur di sini */
        }

        .glass-panel {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(20px) saturate(180%);
            border: 1px solid rgba(255, 255, 255, 0.4);
            border-radius: 1rem;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.05);
        }

        /* PROFILE SECTION AT BOTTOM */
        .sidebar-footer {
            padding: 1rem;
            border-top: 1px solid rgba(0, 0, 0, 0.05);
        }

        .profile-card {
            background: rgba(255, 255, 255, 0.5);
            padding: 0.75rem;
            border-radius: 12px;
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
        }

        /* GLOBAL MODAL FIX */
        /* Menjamin modal selalu muncul di depan apapun strukturnya */
        .modal {
            background: rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(4px);
        }

        .modal-backdrop {
            display: none !important;
            /* Kita gunakan background modal saja agar tidak konflik z-index */
        }

        [v-cloak] {
            display: none;
        }
    </style>
</head>

<body>
    <aside class="sidebar">
        <div class="sidebar-header">
            <div class="bg-primary rounded-2 d-flex align-items-center justify-content-center shadow-sm" style="width: 32px; height: 32px;">
                <i data-lucide="citrus" class="text-white" style="width: 20px;"></i>
            </div>
            TokoBuah
        </div>

        <div class="sidebar-content custom-scroll">
            <?php $role = session()->get('role'); ?>

            <!-- DASHBOARD (SEMUA ROLE) -->
            <a href="<?= base_url('dashboard') ?>" class="nav-link-custom <?= url_is('dashboard*') ? 'active' : '' ?>">
                <i data-lucide="layout-grid" style="width:18px"></i> Dashboard
            </a>

            <!-- ===================================================== -->
            <!-- ======================= OWNER ======================== -->
            <!-- ===================================================== -->
            <?php if ($role === 'owner'): ?>

                <div class="nav-group-label">Master Data</div>

                <a href="<?= base_url('user') ?>" class="nav-link-custom <?= url_is('user*') ? 'active' : '' ?>">
                    <i data-lucide="users" style="width:18px"></i> Manajemen User
                </a>

                <a href="<?= base_url('barang') ?>" class="nav-link-custom <?= url_is('barang*') ? 'active' : '' ?>">
                    <i data-lucide="package" style="width:18px"></i> Data Barang
                </a>

                <a href="<?= base_url('gudangutama') ?>" class="nav-link-custom <?= url_is('gudangutama*') ? 'active' : '' ?>">
                    <i data-lucide="warehouse" style="width:18px"></i> Data Gudang
                </a>

                <a href="<?= base_url('cabang') ?>" class="nav-link-custom <?= url_is('cabang*') ? 'active' : '' ?>">
                    <i data-lucide="map-pin" style="width:18px"></i> Cabang Toko
                </a>

                <a href="<?= base_url('suplier') ?>" class="nav-link-custom <?= url_is('suplier*') ? 'active' : '' ?>">
                    <i data-lucide="truck" style="width:18px"></i> Data Supplier
                </a>

                <div class="nav-group-label">Inventory</div>

                <a href="<?= base_url('aset-gudang') ?>" class="nav-link-custom <?= url_is('aset-gudang*') ? 'active' : '' ?>">
                    <i data-lucide="warehouse" style="width:18px"></i> Aset Gudang
                </a>

                <a href="<?= base_url('aset-toko') ?>" class="nav-link-custom <?= url_is('aset-toko*') ? 'active' : '' ?>">
                    <i data-lucide="store" style="width:18px"></i> Aset Toko
                </a>


                <div class="nav-group-label">Pelanggan</div>

                <a href="<?= base_url('customer') ?>" class="nav-link-custom <?= url_is('customer*') ? 'active' : '' ?>">
                    <i data-lucide="user-check" style="width:18px"></i> Data Customer
                </a>

                <!-- MENU BARU UNTUK OWNER -->
                <div class="nav-group-label">Promo</div>

                <a href="<?= base_url('diskon-terbatas') ?>" class="nav-link-custom <?= url_is('diskon*') ? 'active' : '' ?>">
                    <i data-lucide="percent" style="width:18px"></i> Buat Diskon
                </a>

                <div class="nav-group-label">Laporan</div>

                <a href="<?= base_url('laporan/surat-jalan') ?>" class="nav-link-custom <?= url_is('laporan/surat-jalan*') ? 'active' : '' ?>">
                    <i data-lucide="file-text" style="width:18px"></i> Laporan Pre Order
                </a>

                <a href="<?= base_url('laporan/penerimaan-gudang') ?>" class="nav-link-custom <?= url_is('laporan/penerimaan-gudang*') ? 'active' : '' ?>">
                    <i data-lucide="clipboard-list" style="width:18px"></i> Laporan Penerimaan Gudang
                </a>

                <a href="<?= base_url('laporan/pengiriman-gudang') ?>" class="nav-link-custom <?= url_is('laporan/pengiriman-gudang*') ? 'active' : '' ?>">
                    <i data-lucide="truck" style="width:18px"></i> Laporan Pengiriman Gudang
                </a>

                <a href="<?= base_url('laporan/barang-masuk') ?>" class="nav-link-custom <?= url_is('laporan/barang-masuk*') ? 'active' : '' ?>">
                    <i data-lucide="inbox" style="width:18px"></i> Laporan Barang Masuk
                </a>

                <a href="<?= base_url('laporan/penjualan') ?>" class="nav-link-custom <?= url_is('laporan/penjualan*') ? 'active' : '' ?>">
                    <i data-lucide="bar-chart-3" style="width:18px"></i> Laporan Penjualan
                </a>

            <?php endif; ?>

            <!-- ===================================================== -->
            <!-- ======================= ADMIN ======================== -->
            <!-- ===================================================== -->
            <?php if ($role === 'admin'): ?>

                <div class="nav-group-label">Master Data</div>

                <!-- ADMIN TIDAK BOLEH CRUD USER -->

                <a href="<?= base_url('barang') ?>" class="nav-link-custom <?= url_is('barang*') ? 'active' : '' ?>">
                    <i data-lucide="package" style="width:18px"></i> Data Barang
                </a>

                <a href="<?= base_url('gudangutama') ?>" class="nav-link-custom <?= url_is('gudangutama*') ? 'active' : '' ?>">
                    <i data-lucide="warehouse" style="width:18px"></i> Data Gudang
                </a>

                <a href="<?= base_url('cabang') ?>" class="nav-link-custom <?= url_is('cabang*') ? 'active' : '' ?>">
                    <i data-lucide="map-pin" style="width:18px"></i> Cabang Toko
                </a>

                <a href="<?= base_url('suplier') ?>" class="nav-link-custom <?= url_is('suplier*') ? 'active' : '' ?>">
                    <i data-lucide="truck" style="width:18px"></i> Data Supplier
                </a>

                <div class="nav-group-label">Transaksi Gudang</div>

                <!-- ADMIN MASIH BOLEH -->
                <a href="<?= base_url('surat-jalan') ?>" class="nav-link-custom <?= url_is('surat-jalan*') ? 'active' : '' ?>">
                    <i data-lucide="file-text" style="width:18px"></i> Pre Order
                </a>

                <a href="<?= base_url('penerimaan-gudang') ?>" class="nav-link-custom <?= url_is('penerimaan-gudang*') ? 'active' : '' ?>">
                    <i data-lucide="package-plus" style="width:18px"></i> Penerimaan Gudang
                </a>

                <a href="<?= base_url('pengiriman-gudang') ?>" class="nav-link-custom <?= url_is('pengiriman-gudang*') ? 'active' : '' ?>">
                    <i data-lucide="truck" style="width:18px"></i> Pengiriman Gudang
                </a>

                <!-- ADMIN TIDAK BOLEH BARANG MASUK & PENJUALAN -->

                <div class="nav-group-label">Laporan</div>

                <a href="<?= base_url('laporan/surat-jalan') ?>" class="nav-link-custom <?= url_is('laporan/surat-jalan*') ? 'active' : '' ?>">
                    <i data-lucide="file-text" style="width:18px"></i> Laporan Pre Order
                </a>

                <a href="<?= base_url('laporan/penerimaan-gudang') ?>" class="nav-link-custom <?= url_is('laporan/penerimaan-gudang*') ? 'active' : '' ?>">
                    <i data-lucide="clipboard-list" style="width:18px"></i> Laporan Penerimaan Gudang
                </a>

                <a href="<?= base_url('laporan/pengiriman-gudang') ?>" class="nav-link-custom <?= url_is('laporan/pengiriman-gudang*') ? 'active' : '' ?>">
                    <i data-lucide="truck" style="width:18px"></i> Laporan Pengiriman Gudang
                </a>

                <a href="<?= base_url('laporan/barang-masuk') ?>" class="nav-link-custom <?= url_is('laporan/barang-masuk*') ? 'active' : '' ?>">
                    <i data-lucide="inbox" style="width:18px"></i> Laporan Barang Masuk
                </a>

                <a href="<?= base_url('laporan/penjualan') ?>" class="nav-link-custom <?= url_is('laporan/penjualan*') ? 'active' : '' ?>">
                    <i data-lucide="bar-chart-3" style="width:18px"></i> Laporan Penjualan
                </a>

            <?php endif; ?>


            <!-- ===================================================== -->
            <!-- ====================== PETUGAS ======================= -->
            <!-- ===================================================== -->
            <?php if ($role === 'petugas'): ?>
                <div class="nav-group-label">Data</div>

                <!-- ADMIN TIDAK BOLEH CRUD USER -->

                <a href="<?= base_url('barang') ?>" class="nav-link-custom <?= url_is('barang*') ? 'active' : '' ?>">
                    <i data-lucide="package" style="width:18px"></i> Data Barang
                </a>

                <a href="<?= base_url('aset-toko') ?>" class="nav-link-custom <?= url_is('aset-toko*') ? 'active' : '' ?>">
                    <i data-lucide="store" style="width:18px"></i> Aset Toko
                </a>

                <div class="nav-group-label">Pelanggan</div>

                <a href="<?= base_url('customer') ?>" class="nav-link-custom <?= url_is('customer*') ? 'active' : '' ?>">
                    <i data-lucide="user-check" style="width:18px"></i> Data Customer
                </a>

                <div class="nav-group-label">Transaksi</div>

                <a href="<?= base_url('barang-masuk') ?>" class="nav-link-custom <?= url_is('barang-masuk*') ? 'active' : '' ?>">
                    <i data-lucide="inbox" style="width:18px"></i> Barang Masuk
                </a>

                <a href="<?= base_url('penjualan') ?>" class="nav-link-custom <?= url_is('penjualan*') ? 'active' : '' ?>">
                    <i data-lucide="shopping-cart" style="width:18px"></i> Penjualan
                </a>

                <div class="nav-group-label">Laporan</div>

                <a href="<?= base_url('laporan/pengiriman-gudang') ?>" class="nav-link-custom <?= url_is('laporan/pengiriman-gudang*') ? 'active' : '' ?>">
                    <i data-lucide="truck" style="width:18px"></i> Laporan Pengiriman Gudang
                </a>

            <?php endif; ?>

        </div>

        <div class="sidebar-footer">
            <div class="profile-card">
                <div class="bg-white rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 35px; height: 35px;">
                    <i data-lucide="user" class="text-primary" style="width: 18px;"></i>
                </div>
                <div class="flex-grow-1 overflow-hidden">
                    <div class="fw-bold small text-dark text-truncate"><?= session()->get('nama') ?></div>
                    <div class="text-muted" style="font-size: 0.7rem;"><?= strtoupper($role) ?></div>
                </div>
                <i data-lucide="log-out" class="text-danger" style="width: 14px;" onclick="window.location.href='<?= base_url('logout') ?>'"></i>
            </div>
        </div>
    </aside>

    <main id="app">
        <?= $this->renderSection('content') ?>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();

            // Auto active link berdasarkan URL
            const currentUrl = window.location.href;
            document.querySelectorAll('.nav-link-custom').forEach(link => {
                if (currentUrl.includes(link.getAttribute('href'))) {
                    link.classList.add('active');
                }
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();

            const sidebarContent = document.querySelector('.sidebar-content');
            const activeLink = document.querySelector('.nav-link-custom.active');

            // ==============================
            // 1. Restore scroll saat reload
            // ==============================
            const savedScroll = localStorage.getItem('sidebarScrollTop');

            if (activeLink) {
                // Jika ada menu aktif → fokus ke menu aktif
                activeLink.scrollIntoView({
                    behavior: 'auto',
                    block: 'center'
                });
            } else if (savedScroll) {
                // Kalau tidak ada active → pakai posisi terakhir
                sidebarContent.scrollTop = parseInt(savedScroll);
            }

            // ==============================
            // 2. Simpan scroll saat user scroll
            // ==============================
            sidebarContent.addEventListener('scroll', () => {
                localStorage.setItem('sidebarScrollTop', sidebarContent.scrollTop);
            });
        });
    </script>
    <?= $this->renderSection('scripts') ?>
</body>

</html>