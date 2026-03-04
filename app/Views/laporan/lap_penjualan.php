<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<div id="app" v-cloak class="container-fluid py-4">

    <!-- HEADER PANEL -->
    <div class="glass-panel p-4 mb-4 border-0 shadow-sm position-relative overflow-hidden"
        style="border-radius: 20px; background: linear-gradient(145deg, #ffffff, #f8f9fa);">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-primary bg-opacity-10 p-3 rounded-4 text-primary">
                        <i data-lucide="file-spreadsheet" style="width: 32px; height: 32px;"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-0 text-dark">Laporan Penjualan</h4>
                        <p class="text-muted small mb-0">Monitor dan kelola riwayat transaksi</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 text-md-end mt-3 mt-md-0">
                <!-- <button class="btn btn-outline-dark btn-sm rounded-3 px-3 fw-bold"
                    @click="exportCSV" :disabled="!penjualan.length">
                    <i data-lucide="download" class="me-1" style="width: 16px;"></i> Export CSV
                </button> -->
            </div>
        </div>
    </div>

    <!-- FILTER PANEL -->
    <div class="glass-panel p-3 mb-4 border-0 shadow-sm bg-white" style="border-radius: 16px;">
        <div class="row g-3 align-items-end">
            <div class="col-lg-3 col-md-6">
                <label class="small fw-bold text-muted mb-2">Cari Transaksi</label>
                <div class="input-group input-group-sm rounded-3 overflow-hidden border">
                    <span class="input-group-text bg-light border-0">
                        <i data-lucide="search" style="width: 16px;"></i>
                    </span>
                    <input type="text" class="form-control border-0 shadow-none"
                        placeholder="No. Faktur / Operator..."
                        v-model="search" @input="page = 1">
                </div>
            </div>

            <?php if (in_array(session()->get('role'), ['admin', 'owner'])): ?>
                <div class="col-lg-3 col-md-6">
                    <label class="small fw-bold text-muted mb-2">Unit Cabang</label>
                    <select class="form-select form-select-sm rounded-3 border fw-medium"
                        v-model="filter.cabang_id" @change="load">
                        <option value="">Semua Cabang</option>
                        <option v-for="c in listCabang" :key="c.id" :value="c.id">{{ c.nama }}</option>
                    </select>
                </div>
            <?php endif; ?>

            <div class="col-lg-4 col-md-9">
                <label class="small fw-bold text-muted mb-2">Rentang Tanggal</label>
                <div class="d-flex align-items-center gap-2">
                    <input type="date" class="form-control form-control-sm rounded-3 border"
                        v-model="filter.tgl_awal">
                    <span class="text-muted small">s/d</span>
                    <input type="date" class="form-control form-control-sm rounded-3 border"
                        v-model="filter.tgl_akhir">
                </div>
            </div>

            <div class="col-lg-2 col-md-3 d-flex gap-2">
                <button class="btn btn-primary btn-sm w-100 fw-bold rounded-3 shadow-sm d-flex align-items-center justify-content-center gap-1"
                    @click="load">
                    <i data-lucide="filter" style="width: 14px;"></i> Terapkan
                </button>
                <button class="btn btn-light btn-sm border rounded-3" @click="resetFilter" title="Reset">
                    <i data-lucide="refresh-cw" style="width: 14px;"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- TABLE PANEL -->
    <div class="glass-panel border-0 shadow-sm bg-white overflow-hidden" style="border-radius: 20px;">
        <div class="table-responsive custom-scroll">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted">
                    <tr>
                        <th class="ps-4 py-3 border-0 small text-uppercase fw-bolder">Faktur</th>
                        <th class="py-3 border-0 small text-uppercase fw-bolder text-center">Operator & Cabang</th>
                        <th class="py-3 border-0 small text-uppercase fw-bolder">Nominal Akhir</th>
                        <th class="py-3 border-0 small text-uppercase fw-bolder">Metode</th>
                        <th class="py-3 border-0 small text-uppercase fw-bolder">Waktu & Customer</th>
                        <th class="pe-4 py-3 border-0 small text-uppercase fw-bolder text-end">Opsi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="p in paginatedData" :key="p.id"
                        class="border-bottom border-light transition-all">
                        <td class="ps-4">
                            <span class="badge bg-light text-dark fw-bolder border px-2 py-1">{{ p.faktur }}</span>
                        </td>
                        <td class="text-center">
                            <span class="small fw-bold text-secondary">{{ p.nama_cabang }}</span><br>
                            <span class="text-muted small" style="font-size: 11px;">{{ p.nama_operator }}</span>
                        </td>
                        <td>
                            <div class="fw-bold text-primary">Rp {{ formatNumber(p.nominal_penjualan) }}</div>
                        </td>
                        <td>
                            <span class="rounded-pill px-3 fw-bold" style="font-size: 10px;">
                                {{ p.jenis_pembayaran }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex flex-column">
                                <span class="fw-bold text-dark" style="font-size: 12px;">{{ formatDate(p.created_at) }}</span>
                                <span class="text-muted small">{{ p.nama_customer || 'Customer Umum' }}</span>
                            </div>
                        </td>
                        <td class="text-end pe-4">
                            <div class="btn-group btn-group-sm shadow-sm rounded-3 overflow-hidden">
                                <a :href="'<?= base_url('laporan/penjualan_detail') ?>/' + p.id"
                                    class="btn btn-white border-end" title="Detail">
                                    <i data-lucide="eye" class="text-primary" style="width: 16px;"></i>
                                </a>
                                <button @click="cetak(p.id)" class="btn btn-white" title="Cetak Nota">
                                    <i data-lucide="printer" class="text-dark" style="width: 16px;"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="filteredData.length === 0">
                        <td colspan="6" class="text-center py-5">
                            <div class="d-flex flex-column align-items-center opacity-50">
                                <i data-lucide="database-zap" style="width: 48px; height: 48px;" class="mb-2"></i>
                                <p class="fw-bold">Tidak ada data transaksi ditemukan</p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- FOOTER -->
        <div class="p-4 bg-light d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div class="d-flex gap-4 align-items-center">
                <p class="small text-muted mb-0 fw-medium">
                    Menampilkan <b>{{ paginatedData.length }}</b> dari <b>{{ filteredData.length }}</b> data
                </p>
                <p class="small mb-0 fw-bold text-primary" v-if="filteredData.length">
                    Total: Rp {{ formatNumber(grandTotal) }}
                </p>
            </div>
            <nav>
                <ul class="pagination pagination-sm mb-0 gap-1">
                    <li class="page-item" :class="{disabled: page == 1}">
                        <button class="page-link border-0 rounded-3 shadow-sm" @click="page--">
                            <i data-lucide="chevron-left" style="width: 14px;"></i>
                        </button>
                    </li>
                    <li class="page-item active">
                        <span class="page-link border-0 rounded-3 shadow-sm fw-bold px-3">
                            {{ page }} / {{ totalPage || 1 }}
                        </span>
                    </li>
                    <li class="page-item" :class="{disabled: page >= totalPage}">
                        <button class="page-link border-0 rounded-3 shadow-sm" @click="page++">
                            <i data-lucide="chevron-right" style="width: 14px;"></i>
                        </button>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</div>

<style>
    body {
        background-color: #f4f7fa;
    }

    [v-cloak] {
        display: none;
    }

    .bg-soft-info {
        background-color: #e0f2fe;
        color: #0369a1;
    }

    .transition-all {
        transition: all 0.2s ease-in-out;
    }

    .transition-all:hover {
        background-color: #f8fafc !important;
    }

    .custom-scroll {
        max-height: 60vh;
        overflow-y: auto;
    }

    .btn-white {
        background-color: white;
        color: #334155;
    }

    .btn-white:hover {
        background-color: #f1f5f9;
    }

    .page-link {
        color: #334155;
    }

    .page-item.active .page-link {
        background-color: #0d6efd;
        color: white;
    }
</style>

<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script>
    const {
        createApp
    } = Vue;
    createApp({
        data() {
            return {
                penjualan: [],
                listCabang: [],
                search: '',
                page: 1,
                perPage: 10,
                filter: {
                    tgl_awal: '<?= date('Y-m-01') ?>',
                    tgl_akhir: '<?= date('Y-m-d') ?>',
                    cabang_id: '',
                },
            };
        },

        computed: {
            filteredData() {
                if (!this.search) return this.penjualan;
                const s = this.search.toLowerCase();
                return this.penjualan.filter(p =>
                    (p.faktur || '').toLowerCase().includes(s) ||
                    (p.nama_operator || '').toLowerCase().includes(s)
                );
            },
            totalPage() {
                return Math.ceil(this.filteredData.length / this.perPage);
            },
            paginatedData() {
                const start = (this.page - 1) * this.perPage;
                return this.filteredData.slice(start, start + this.perPage);
            },
            grandTotal() {
                return this.filteredData.reduce((s, p) => s + parseFloat(p.nominal_penjualan || 0), 0);
            },
        },

        methods: {
            load() {
                axios.get('<?= base_url('laporan/list_penjualan') ?>', {
                        params: this.filter
                    })
                    .then(res => {
                        this.penjualan = res.data.data;
                        if (res.data.cabang) this.listCabang = res.data.cabang;
                        this.page = 1;
                        this.$nextTick(() => lucide.createIcons());
                    })
                    .catch(err => console.error('Gagal memuat data', err));
            },

            resetFilter() {
                this.filter = {
                    tgl_awal: '<?= date('Y-m-01') ?>',
                    tgl_akhir: '<?= date('Y-m-d') ?>',
                    cabang_id: '',
                };
                this.load();
            },

            formatNumber(num) {
                return new Intl.NumberFormat('id-ID').format(num ?? 0);
            },

            formatDate(dateString) {
                if (!dateString) return '-';
                return new Date(dateString).toLocaleDateString('id-ID', {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                });
            },

            cetak(id) {
                window.open('<?= base_url('penjualan/cetak') ?>/' + id, '_blank');
            },

            exportCSV() {
                if (!this.filteredData.length) return;
                const headers = [
                    'No', 'Faktur', 'Cabang', 'Operator', 'Customer',
                    'Metode Bayar', 'Nominal', 'Diskon', 'Tanggal',
                ];
                const rows = this.filteredData.map((p, i) => [
                    i + 1,
                    p.faktur,
                    p.nama_cabang,
                    p.nama_operator,
                    p.nama_customer || 'Customer Umum',
                    p.jenis_pembayaran,
                    p.nominal_penjualan,
                    p.diskon_nominal || 0,
                    p.created_at,
                ]);
                const csv = [headers, ...rows]
                    .map(r => r.map(v => `"${v ?? ''}"`).join(','))
                    .join('\n');
                const a = document.createElement('a');
                a.href = 'data:text/csv;charset=utf-8,' + encodeURIComponent('\uFEFF' + csv);
                a.download = `laporan_penjualan_${new Date().toISOString().slice(0, 10)}.csv`;
                a.click();
            },
        },

        mounted() {
            this.load();
            lucide.createIcons();
        },
        updated() {
            lucide.createIcons();
        },
    }).mount('#app');
</script>
<?= $this->endSection() ?>