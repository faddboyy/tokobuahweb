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
                        <i data-lucide="truck" style="width: 32px; height: 32px;"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-0 text-dark">Laporan Pre Order</h4>
                        <p class="text-muted small mb-0">Riwayat pembelian barang dari supplier</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- FILTER PANEL -->
    <div class="glass-panel p-3 mb-4 border-0 shadow-sm bg-white" style="border-radius: 16px;">
        <div class="row g-3 align-items-end">

            <div class="col-lg-2 col-md-6">
                <label class="small fw-bold text-muted mb-2">Supplier</label>
                <select class="form-select form-select-sm rounded-3 border fw-medium"
                    v-model="filter.suplier_id" @change="load">
                    <option value="">Semua Supplier</option>
                    <option v-for="s in suplierList" :key="s.id" :value="s.id">{{ s.nama }}</option>
                </select>
            </div>

            <div class="col-lg-2 col-md-4">
                <label class="small fw-bold text-muted mb-2">Gudang</label>
                <select class="form-select form-select-sm rounded-3 border fw-medium"
                    v-model="filter.gudang_id" @change="load">
                    <option value="">Semua Gudang</option>
                    <option v-for="g in gudangList" :key="g.id" :value="g.id">{{ g.nama }}</option>
                </select>
            </div>

            <div class="col-lg-1 col-md-4">
                <label class="small fw-bold text-muted mb-2">Status</label>
                <select class="form-select form-select-sm rounded-3 border fw-medium"
                    v-model="filter.status" @change="load">
                    <option value="">Semua</option>
                    <option value="order">Pesanan</option>
                    <option value="selesai">Selesai</option>
                    <option value="dibatalkan">Dibatalkan</option>
                </select>
            </div>

            <div class="col-lg-5 col-md-9">
                <label class="small fw-bold text-muted mb-2">Rentang Tanggal</label>
                <div class="d-flex align-items-center gap-2">
                    <input type="date" class="form-control form-control-sm rounded-3 border"
                        v-model="filter.tgl_awal">
                    <span class="text-muted small">s/d</span>
                    <input type="date" class="form-control form-control-sm rounded-3 border"
                        v-model="filter.tgl_akhir">
                </div>
            </div>

            <div class="col-lg-1 col-md-3 d-flex gap-2">
                <button class="btn btn-primary btn-sm w-100 fw-bold rounded-3 shadow-sm d-flex align-items-center justify-content-center gap-1"
                    @click="load" :disabled="loading">
                    <i data-lucide="filter" style="width: 14px;"></i>
                    <span v-if="!loading">Terapkan</span>
                    <span v-else class="spinner-border spinner-border-sm"></span>
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
                        <th class="ps-4 py-3 border-0 small text-uppercase fw-bolder">Kode PO</th>
                        <th class="py-3 border-0 small text-uppercase fw-bolder">Supplier</th>
                        <th class="py-3 border-0 small text-uppercase fw-bolder text-center">Gudang &amp; Operator</th>
                        <th class="py-3 border-0 small text-uppercase fw-bolder text-center">Item</th>
                        <th class="py-3 border-0 small text-uppercase fw-bolder">Total Nominal</th>
                        <th class="py-3 border-0 small text-uppercase fw-bolder">Tanggal</th>
                        <th class="py-3 border-0 small text-uppercase fw-bolder text-center">Status</th>
                        <th class="pe-4 py-3 border-0 small text-uppercase fw-bolder text-end">Opsi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="loading">
                        <td colspan="8" class="text-center py-5">
                            <div class="spinner-border text-primary mb-2" role="status"></div>
                            <p class="text-muted small mb-0">Memuat data...</p>
                        </td>
                    </tr>
                    <tr v-else-if="filteredData.length === 0">
                        <td colspan="8" class="text-center py-5">
                            <div class="d-flex flex-column align-items-center opacity-50">
                                <i data-lucide="database-zap" style="width: 48px; height: 48px;" class="mb-2"></i>
                                <p class="fw-bold">Tidak ada data ditemukan</p>
                            </div>
                        </td>
                    </tr>
                    <tr v-else v-for="row in paginatedData" :key="row.id"
                        class="border-bottom border-light transition-all">
                        <td class="ps-4">
                            <span class="badge bg-light text-dark fw-bolder border px-2 py-1 font-mono">
                                {{ row.kode_po }}
                            </span>
                        </td>
                        <td>
                            <div class="fw-bold text-dark small">{{ row.nama_suplier || '-' }}</div>
                        </td>
                        <td class="text-center">
                            <span class="small fw-bold text-secondary">{{ row.nama_gudang || '-' }}</span><br>
                            <span class="text-muted small" style="font-size: 11px;">{{ row.nama_operator || '-' }}</span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-light text-dark border fw-bold">{{ row.jumlah_item }}</span>
                        </td>
                        <td>
                            <div class="fw-bold text-primary">Rp {{ formatNumber(row.total_nominal) }}</div>
                        </td>
                        <td>
                            <span class="fw-bold text-dark" style="font-size: 12px;">{{ formatDate(row.waktu_po) }}</span>
                        </td>
                        <td class="text-center">
                            <span class="badge rounded-pill px-3 fw-bold" style="font-size: 10px;"
                                :class="statusClass(row.status)">
                                {{ labelStatus(row.status) }}
                            </span>
                        </td>
                        <td class="text-end pe-4">
                            <a :href="'<?= base_url('laporan/surat-jalan-detail') ?>/' + row.id"
                                class="btn btn-white border btn-sm shadow-sm rounded-3" title="Lihat Detail">
                                <i data-lucide="eye" class="text-primary" style="width: 16px;"></i>
                            </a>
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

    .font-mono {
        font-family: 'Courier New', monospace;
        font-size: .8rem;
    }

    /* Badge status Pesanan (order) -> indigo */
    .badge-status-order {
        background: rgba(99, 102, 241, 0.12) !important;
        color: #4f46e5 !important;
        border: 1px solid rgba(99, 102, 241, 0.3) !important;
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
                loading: false,
                data: [],
                suplierList: [],
                gudangList: [],
                search: '',
                page: 1,
                perPage: 10,
                filter: {
                    tgl_awal: '<?= date('Y-m-01') ?>',
                    tgl_akhir: '<?= date('Y-m-d') ?>',
                    suplier_id: '',
                    gudang_id: '',
                    status: '',
                },
            };
        },

        computed: {
            filteredData() {
                if (!this.search) return this.data;
                const s = this.search.toLowerCase();
                return this.data.filter(r =>
                    (r.kode_po || '').toLowerCase().includes(s) ||
                    (r.nama_suplier || '').toLowerCase().includes(s) ||
                    (r.nama_operator || '').toLowerCase().includes(s)
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
                return this.filteredData.reduce((s, r) => s + parseFloat(r.total_nominal || 0), 0);
            },
        },

        methods: {
            async load() {
                this.loading = true;
                try {
                    const res = await axios.get('<?= base_url('laporan/list-surat-jalan') ?>', {
                        params: this.filter
                    });
                    this.data = res.data.data;
                    this.suplierList = res.data.suplier;
                    this.gudangList = res.data.gudang;
                    this.page = 1;
                    this.$nextTick(() => lucide.createIcons());
                } catch (e) {
                    console.error(e);
                } finally {
                    this.loading = false;
                }
            },

            resetFilter() {
                this.filter = {
                    tgl_awal: '<?= date('Y-m-01') ?>',
                    tgl_akhir: '<?= date('Y-m-d') ?>',
                    suplier_id: '',
                    gudang_id: '',
                    status: '',
                };
                this.search = '';
                this.load();
            },

            labelStatus(s) {
                return {
                    order: 'Pesanan',
                    diterima: 'Diterima',
                    selesai: 'Selesai',
                    dibatalkan: 'Dibatalkan',
                } [s] || s;
            },

            statusClass(s) {
                return {

                    // SESUDAH
                    order: 'bg-info bg-opacity-10 text-info border border-info border-opacity-25',
                    diterima: 'bg-success  bg-opacity-10 text-success  border border-success  border-opacity-25',
                    selesai: 'bg-primary  bg-opacity-10 text-primary  border border-primary  border-opacity-25',
                    dibatalkan: 'bg-danger   bg-opacity-10 text-danger   border border-danger   border-opacity-25',
                } [s] || 'bg-secondary bg-opacity-10 text-secondary';
            },

            formatNumber(n) {
                return new Intl.NumberFormat('id-ID').format(n ?? 0);
            },

            formatDate(d) {
                if (!d) return '-';
                return new Date(d).toLocaleDateString('id-ID', {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                });
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