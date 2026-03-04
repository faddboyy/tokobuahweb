<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<div id="app" v-cloak class="container-fluid py-4">

    <!-- HEADER -->
    <div class="glass-panel p-4 mb-4 border-0 shadow-sm"
        style="border-radius:20px;background:linear-gradient(145deg,#ffffff,#f8f9fa)">
        <div class="row align-items-center">
            <div class="col-md-7">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-warning bg-opacity-10 p-3 rounded-4 text-warning">
                        <i data-lucide="send" style="width:32px;height:32px"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-0 text-dark">Laporan Pengiriman Gudang</h4>
                        <p class="text-muted small mb-0">Riwayat pengiriman barang dari gudang ke cabang/toko</p>
                    </div>
                </div>
            </div>
            <div class="col-md-5 text-md-end mt-3 mt-md-0">
                <!-- <button class="btn btn-outline-dark btn-sm rounded-3 px-3 fw-bold"
                    @click="exportCSV" :disabled="!allRows.length">
                    <i data-lucide="download" class="me-1" style="width:16px"></i> Export CSV
                </button> -->
            </div>
        </div>
    </div>

    <!-- FILTER -->
    <div class="glass-panel p-3 mb-4 border-0 shadow-sm bg-white" style="border-radius:16px">
        <div class="row g-3 align-items-end">

            <div class="col-lg-3 col-md-12">
                <label class="small fw-bold text-muted mb-1">
                    <i data-lucide="search" style="width:12px" class="me-1"></i>Cari
                </label>
                <div class="input-group input-group-sm rounded-3 overflow-hidden border">
                    <span class="input-group-text bg-light border-0">
                        <i data-lucide="search" style="width:15px"></i>
                    </span>
                    <input type="text" class="form-control border-0 shadow-none"
                        placeholder="Kode, operator, cabang..."
                        v-model="search" @input="page = 1">
                </div>
            </div>

            <div class="col-lg-2 col-md-4">
                <label class="small fw-bold text-muted mb-1">
                    <i data-lucide="warehouse" style="width:12px" class="me-1"></i>Gudang
                </label>
                <select class="form-select form-select-sm rounded-3 border fw-medium"
                    v-model="filter.gudang_id" @change="load">
                    <option value="">Semua Gudang</option>
                    <option v-for="g in gudangList" :key="g.id" :value="g.id">{{ g.nama }}</option>
                </select>
            </div>

            <div class="col-lg-2 col-md-4">
                <label class="small fw-bold text-muted mb-1">
                    <i data-lucide="store" style="width:12px" class="me-1"></i>Cabang / Toko
                </label>
                <select class="form-select form-select-sm rounded-3 border fw-medium"
                    v-model="filter.cabang_id" @change="load">
                    <option value="">Semua Cabang</option>
                    <option v-for="c in cabangList" :key="c.id" :value="c.id">{{ c.nama }}</option>
                </select>
            </div>

            <div class="col-lg-3 col-md-4">
                <label class="small fw-bold text-muted mb-1">
                    <i data-lucide="calendar" style="width:12px" class="me-1"></i>Rentang Tanggal
                </label>
                <div class="d-flex align-items-center gap-2">
                    <input type="date" class="form-control form-control-sm rounded-3 border"
                        v-model="filter.tgl_awal">
                    <span class="text-muted small fw-bold">–</span>
                    <input type="date" class="form-control form-control-sm rounded-3 border"
                        v-model="filter.tgl_akhir">
                </div>
            </div>

            <div class="col-lg-2 d-flex gap-2">
                <button class="btn btn-warning btn-sm w-100 fw-bold rounded-3 shadow-sm
                               d-flex align-items-center justify-content-center gap-1 text-dark"
                    @click="load" :disabled="loading">
                    <i data-lucide="filter" style="width:14px"></i>
                    <span v-if="!loading">Terapkan</span>
                    <span v-else class="spinner-border spinner-border-sm"></span>
                </button>
                <button class="btn btn-light btn-sm border rounded-3 px-3"
                    @click="resetFilter" title="Reset">
                    <i data-lucide="refresh-cw" style="width:14px"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- LOADING -->
    <div v-if="loading" class="text-center py-5">
        <div class="spinner-border text-warning mb-2" role="status"></div>
        <p class="text-muted small mb-0">Memuat data...</p>
    </div>

    <!-- EMPTY -->
    <div v-else-if="filteredGroups.length === 0"
        class="glass-panel border-0 shadow-sm bg-white p-5 text-center"
        style="border-radius:20px">
        <div class="d-flex flex-column align-items-center" style="opacity:.35">
            <i data-lucide="inbox" style="width:56px;height:56px" class="mb-3"></i>
            <p class="fw-bold mb-1">Tidak ada data ditemukan</p>
            <p class="text-muted small mb-0">Coba ubah filter atau rentang tanggal</p>
        </div>
    </div>

    <!-- CARD PER GUDANG -->
    <div v-else v-for="group in paginatedGroups" :key="group.gudang_id" class="mb-4">
        <div class="glass-panel border-0 shadow-sm bg-white overflow-hidden" style="border-radius:20px">

            <!-- Gudang Header Bar -->
            <div class="gudang-header d-flex align-items-center justify-content-between flex-wrap gap-2 px-4 py-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="gudang-header-icon">
                        <i data-lucide="warehouse" style="width:18px"></i>
                    </div>
                    <div>
                        <div class="fw-bold text-white" style="font-size:1rem">{{ group.nama_gudang }}</div>
                        <div class="text-white-50 small">
                            {{ group.rows.length }} pengiriman
                            &bull; {{ uniqueCabang(group.rows) }} cabang/toko
                        </div>
                    </div>
                </div>
                <button class="btn btn-sm btn-gudang-toggle rounded-3 fw-semibold px-3"
                    @click="toggleGudang(group.gudang_id)">
                    <i :data-lucide="collapsed.includes(group.gudang_id) ? 'chevron-down' : 'chevron-up'"
                        style="width:15px" class="me-1"></i>
                    {{ collapsed.includes(group.gudang_id) ? 'Tampilkan' : 'Sembunyikan' }}
                </button>
            </div>

            <!-- Tabel rows -->
            <div v-show="!collapsed.includes(group.gudang_id)">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead style="background:#f8fafc">
                            <tr class="text-muted">
                                <th class="ps-4 py-3 border-0 small text-uppercase fw-bolder">Kode Pengiriman</th>
                                <th class="py-3 border-0 small text-uppercase fw-bolder">Gudang / Operator</th>
                                <th class="py-3 border-0 small text-uppercase fw-bolder">Cabang / Toko Tujuan</th>
                                <th class="py-3 border-0 small text-uppercase fw-bolder">Waktu Pengiriman</th>
                                <th class="py-3 border-0 small text-uppercase fw-bolder text-center">Status</th>
                                <th class="pe-4 py-3 border-0 small text-uppercase fw-bolder text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="group.rows.length === 0">
                                <td colspan="6" class="text-center py-4 text-muted small">Tidak ada data</td>
                            </tr>
                            <tr v-else v-for="row in group.rows" :key="row.id"
                                class="border-bottom border-light row-item">

                                <!-- Kode Pengiriman -->
                                <td class="ps-4">
                                    <span class="badge bg-light text-dark fw-bolder border px-2 py-1 font-mono"
                                        style="font-size:.75rem">
                                        {{ row.kode_pengiriman }}
                                    </span>
                                </td>

                                <!-- Gudang + Operator -->
                                <td>
                                    <div class="fw-semibold text-dark small">{{ row.nama_gudang || '-' }}</div>
                                    <div class="d-flex align-items-center gap-1 mt-1">
                                        <div class="op-avatar">
                                            <i data-lucide="user" style="width:11px"></i>
                                        </div>
                                        <span class="text-muted" style="font-size:.75rem">{{ row.nama_operator || '-' }}</span>
                                    </div>
                                </td>

                                <!-- Cabang / Toko Tujuan -->
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="store-avatar">
                                            <i data-lucide="store" style="width:11px"></i>
                                        </div>
                                        <div>
                                            <div class="fw-semibold text-dark small">{{ row.nama_cabang || '-' }}</div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Waktu -->
                                <td>
                                    <div class="fw-semibold text-dark" style="font-size:12px">
                                        {{ formatDate(row.waktu_pengiriman) }}
                                    </div>
                                </td>

                                <!-- Status -->
                                <td class="text-center">
                                    <span class="badge rounded-pill px-2 py-1 fw-semibold"
                                        style="font-size:.68rem; letter-spacing:.3px"
                                        :class="statusBadgeClass(row.status)">
                                        {{ statusLabel(row.status) }}
                                    </span>
                                </td>

                                <!-- Aksi -->
                                <td class="text-center pe-4">
                                    <a :href="detailUrl(row.id)"
                                        class="btn btn-sm btn-outline-warning rounded-3 px-3 py-1 fw-semibold"
                                        style="font-size:.75rem;white-space:nowrap">
                                        <i data-lucide="eye" style="width:13px" class="me-1"></i> Detail
                                    </a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <!-- FOOTER: pagination -->
    <div class="glass-panel border-0 shadow-sm bg-white p-4
                d-flex justify-content-between align-items-center flex-wrap gap-3"
        style="border-radius:16px">
        <div class="d-flex gap-4 align-items-center flex-wrap">
            <p class="small text-muted mb-0 fw-medium">
                Menampilkan <b>{{ paginatedGroups.length }}</b> dari
                <b>{{ filteredGroups.length }}</b> gudang
                (<b>{{ totalTransaksi }}</b> pengiriman)
            </p>
        </div>
        <nav>
            <ul class="pagination pagination-sm mb-0 gap-1">
                <li class="page-item" :class="{ disabled: page === 1 }">
                    <button class="page-link border-0 rounded-3 shadow-sm" @click="page--">
                        <i data-lucide="chevron-left" style="width:14px"></i>
                    </button>
                </li>
                <li class="page-item active">
                    <span class="page-link border-0 rounded-3 shadow-sm fw-bold px-3">
                        {{ page }} / {{ totalPage || 1 }}
                    </span>
                </li>
                <li class="page-item" :class="{ disabled: page >= totalPage }">
                    <button class="page-link border-0 rounded-3 shadow-sm" @click="page++">
                        <i data-lucide="chevron-right" style="width:14px"></i>
                    </button>
                </li>
            </ul>
        </nav>
    </div>

</div>

<style>
    [v-cloak] { display: none; }

    .font-mono { font-family: 'Courier New', monospace; }

    .gudang-header {
        background: linear-gradient(135deg, #78350f 0%, #d97706 100%);
    }

    .gudang-header-icon {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        flex-shrink: 0;
        background: rgba(255, 255, 255, .18);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
    }

    .btn-gudang-toggle {
        background: rgba(255, 255, 255, .15);
        color: #fff;
        border: 1px solid rgba(255, 255, 255, .3);
        font-size: .78rem;
    }

    .btn-gudang-toggle:hover {
        background: rgba(255, 255, 255, .28);
        color: #fff;
    }

    .row-item { transition: background .12s; }
    .row-item:hover { background: #fffbeb !important; }

    .op-avatar {
        width: 18px;
        height: 18px;
        border-radius: 50%;
        background: #fef3c7;
        color: #d97706;
        display: grid;
        place-items: center;
        flex-shrink: 0;
    }

    .store-avatar {
        width: 22px;
        height: 22px;
        border-radius: 6px;
        background: #ecfdf5;
        color: #059669;
        display: grid;
        place-items: center;
        flex-shrink: 0;
    }

    .page-link { color: #334155; }
    .page-item.active .page-link { background-color: #d97706; color: #fff; border-color: #d97706; }
</style>

<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script>
    const { createApp } = Vue;
    createApp({
        data() {
            return {
                loading: false,
                allRows: [],
                gudangList: [],
                cabangList: [],
                search: '',
                page: 1,
                perPage: 5,
                collapsed: [],
                filter: {
                    tgl_awal:  '<?= date('Y-m-01') ?>',
                    tgl_akhir: '<?= date('Y-m-d') ?>',
                    gudang_id: '',
                    cabang_id: '',
                },
            };
        },

        computed: {
            groupedByGudang() {
                const map = new Map();
                this.allRows.forEach(row => {
                    const key = row.gudang_id || '__unknown__';
                    if (!map.has(key)) {
                        map.set(key, {
                            gudang_id:  key,
                            nama_gudang: row.nama_gudang || '(Tidak Diketahui)',
                            rows: [],
                        });
                    }
                    map.get(key).rows.push(row);
                });
                return Array.from(map.values());
            },

            filteredGroups() {
                if (!this.search) return this.groupedByGudang;
                const s = this.search.toLowerCase();
                return this.groupedByGudang.map(group => {
                    const rows = group.rows.filter(r =>
                        (r.kode_pengiriman || '').toLowerCase().includes(s) ||
                        (r.nama_gudang    || '').toLowerCase().includes(s) ||
                        (r.nama_cabang    || '').toLowerCase().includes(s) ||
                        (r.nama_operator  || '').toLowerCase().includes(s)
                    );
                    return rows.length ? { ...group, rows } : null;
                }).filter(Boolean);
            },

            totalPage()       { return Math.ceil(this.filteredGroups.length / this.perPage); },
            paginatedGroups() {
                const s = (this.page - 1) * this.perPage;
                return this.filteredGroups.slice(s, s + this.perPage);
            },
            totalTransaksi()  { return this.filteredGroups.reduce((s, g) => s + g.rows.length, 0); },
        },

        methods: {
            async load() {
                this.loading = true;
                try {
                    const res = await axios.get('<?= base_url('laporan/pengiriman-gudang/list') ?>', {
                        params: this.filter,
                    });
                    this.allRows    = res.data.data;
                    this.gudangList = res.data.gudang_list;
                    this.cabangList = res.data.cabang_list;
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
                    tgl_awal:  '<?= date('Y-m-01') ?>',
                    tgl_akhir: '<?= date('Y-m-d') ?>',
                    gudang_id: '',
                    cabang_id: '',
                };
                this.search = '';
                this.load();
            },

            toggleGudang(id) {
                const idx = this.collapsed.indexOf(id);
                if (idx === -1) this.collapsed.push(id);
                else this.collapsed.splice(idx, 1);
                this.$nextTick(() => lucide.createIcons());
            },

            detailUrl(id) {
                return `<?= base_url('laporan/pengiriman-gudang/detail') ?>/${id}`;
            },

            uniqueCabang(rows) {
                return new Set(rows.map(r => r.cabang_id).filter(Boolean)).size;
            },

            statusLabel(s) {
                return { dikirim: 'Dikirim', diterima: 'Diterima', dibatalkan: 'Dibatalkan' }[s] || s;
            },

            statusBadgeClass(s) {
                return {
                    dikirim:    'bg-warning  bg-opacity-10 text-warning  border border-warning  border-opacity-25',
                    diterima:   'bg-success  bg-opacity-10 text-success  border border-success  border-opacity-25',
                    dibatalkan: 'bg-danger   bg-opacity-10 text-danger   border border-danger   border-opacity-25',
                }[s] || 'bg-secondary bg-opacity-10 text-secondary';
            },

            formatDate(d) {
                if (!d) return '-';
                return new Date(d).toLocaleDateString('id-ID', {
                    day: '2-digit', month: 'short', year: 'numeric',
                    hour: '2-digit', minute: '2-digit',
                });
            },

            exportCSV() {
                if (!this.filteredGroups.length) return;
                const headers = ['Gudang', 'Kode Pengiriman', 'Operator', 'Cabang/Toko Tujuan', 'Status', 'Waktu'];
                const rows = [];
                this.filteredGroups.forEach(group => {
                    group.rows.forEach(r => {
                        rows.push([
                            group.nama_gudang,
                            r.kode_pengiriman,
                            r.nama_operator  || '',
                            r.nama_cabang    || '',
                            this.statusLabel(r.status),
                            r.waktu_pengiriman,
                        ]);
                    });
                });
                const csv = [headers, ...rows]
                    .map(r => r.map(v => `"${v ?? ''}"`).join(','))
                    .join('\n');
                const a = document.createElement('a');
                a.href = 'data:text/csv;charset=utf-8,' + encodeURIComponent('\uFEFF' + csv);
                a.download = `laporan_pengiriman_gudang_${new Date().toISOString().slice(0, 10)}.csv`;
                a.click();
            },
        },

        mounted() {
            this.load();
            lucide.createIcons();
        },
    }).mount('#app');
</script>
<?= $this->endSection() ?>