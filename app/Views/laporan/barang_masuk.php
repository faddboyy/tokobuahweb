<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<div id="app" v-cloak class="container-fluid py-4">

    <!-- ══ HEADER ══════════════════════════════════════════════════════════ -->
    <div class="glass-panel p-4 mb-4 border-0 shadow-sm"
        style="border-radius:20px;background:linear-gradient(145deg,#ffffff,#f8f9fa)">
        <div class="row align-items-center">
            <div class="col-md-7">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-success bg-opacity-10 p-3 rounded-4 text-success">
                        <i data-lucide="package-check" style="width:32px;height:32px"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-0 text-dark">Laporan Barang Masuk</h4>
                        <p class="text-muted small mb-0">Riwayat penerimaan barang dari gudang ke cabang / toko</p>
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

    <!-- ══ FILTER ═══════════════════════════════════════════════════════════ -->
    <div class="glass-panel p-3 mb-4 border-0 shadow-sm bg-white" style="border-radius:16px">
        <div class="row g-3 align-items-end">

            <!-- Cari -->
            <div class="col-lg-3 col-md-12">
                <label class="small fw-bold text-muted mb-1">
                    <i data-lucide="search" style="width:12px" class="me-1"></i>Cari
                </label>
                <div class="input-group input-group-sm rounded-3 overflow-hidden border">
                    <span class="input-group-text bg-light border-0">
                        <i data-lucide="search" style="width:15px"></i>
                    </span>
                    <input type="text" class="form-control border-0 shadow-none"
                        placeholder="Kode masuk, kode kirim, operator, cabang..."
                        v-model="search" @input="page = 1">
                </div>
            </div>

            <!-- Gudang Pengirim -->
            <div class="col-lg-2 col-md-4">
                <label class="small fw-bold text-muted mb-1">
                    <i data-lucide="warehouse" style="width:12px" class="me-1"></i>Gudang Pengirim
                </label>
                <select class="form-select form-select-sm rounded-3 border fw-medium"
                    v-model="filter.gudang_id" @change="load">
                    <option value="">Semua Gudang</option>
                    <option v-for="g in gudangList" :key="g.id" :value="g.id">{{ g.nama }}</option>
                </select>
            </div>

            <!-- Cabang Penerima -->
            <div class="col-lg-2 col-md-4">
                <label class="small fw-bold text-muted mb-1">
                    <i data-lucide="store" style="width:12px" class="me-1"></i>Cabang Penerima
                </label>
                <select class="form-select form-select-sm rounded-3 border fw-medium"
                    v-model="filter.cabang_id" @change="load">
                    <option value="">Semua Cabang</option>
                    <option v-for="c in cabangList" :key="c.id" :value="c.id">{{ c.nama }}</option>
                </select>
            </div>

            <!-- Rentang Tanggal -->
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

            <!-- Tombol -->
            <div class="col-lg-2 d-flex gap-2">
                <button class="btn btn-success btn-sm w-100 fw-bold rounded-3 shadow-sm
                               d-flex align-items-center justify-content-center gap-1"
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
        <div class="spinner-border text-success mb-2" role="status"></div>
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

    <!-- ══ CARD PER CABANG ═══════════════════════════════════════════════════ -->
    <div v-else v-for="group in paginatedGroups" :key="group.cabang_id" class="mb-4">
        <div class="glass-panel border-0 shadow-sm bg-white overflow-hidden" style="border-radius:20px">

            <!-- Cabang Header Bar -->
            <div class="cabang-header d-flex align-items-center justify-content-between flex-wrap gap-2 px-4 py-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="cabang-header-icon">
                        <i data-lucide="store" style="width:18px"></i>
                    </div>
                    <div>
                        <div class="fw-bold text-white" style="font-size:1rem">{{ group.nama_cabang }}</div>
                        <div class="text-white-50 small">
                            {{ group.rows.length }} penerimaan
                            &bull; {{ uniqueGudang(group.rows) }} gudang pengirim
                        </div>
                    </div>
                </div>
                <button class="btn btn-sm btn-cabang-toggle rounded-3 fw-semibold px-3"
                    @click="toggleCabang(group.cabang_id)">
                    <i :data-lucide="collapsed.includes(group.cabang_id) ? 'chevron-down' : 'chevron-up'"
                        style="width:15px" class="me-1"></i>
                    {{ collapsed.includes(group.cabang_id) ? 'Tampilkan' : 'Sembunyikan' }}
                </button>
            </div>

            <!-- Tabel rows -->
            <div v-show="!collapsed.includes(group.cabang_id)">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead style="background:#f8fafc">
                            <tr class="text-muted">
                                <th class="ps-4 py-3 border-0 small text-uppercase fw-bolder">Kode Masuk</th>
                                <th class="py-3 border-0 small text-uppercase fw-bolder">Kode Pengiriman</th>
                                <th class="py-3 border-0 small text-uppercase fw-bolder th-gudang">
                                    <span class="d-flex align-items-center gap-1">
                                        <i data-lucide="warehouse" style="width:12px"></i>
                                        Gudang Pengirim
                                    </span>
                                </th>
                                <th class="py-3 border-0 small text-uppercase fw-bolder th-toko">
                                    <span class="d-flex align-items-center gap-1">
                                        <i data-lucide="store" style="width:12px"></i>
                                        Cabang Penerima
                                    </span>
                                </th>
                                <th class="py-3 border-0 small text-uppercase fw-bolder">Waktu Masuk</th>
                                <th class="pe-4 py-3 border-0 small text-uppercase fw-bolder text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="group.rows.length === 0">
                                <td colspan="6" class="text-center py-4 text-muted small">Tidak ada data</td>
                            </tr>
                            <tr v-else v-for="row in group.rows" :key="row.id"
                                class="border-bottom border-light row-item">

                                <!-- Kode Masuk -->
                                <td class="ps-4">
                                    <span class="badge bg-light text-dark fw-bolder border px-2 py-1 font-mono"
                                        style="font-size:.75rem">
                                        {{ row.kode_masuk }}
                                    </span>
                                </td>

                                <!-- Kode Pengiriman -->
                                <td>
                                    <span class="font-mono fw-semibold text-muted"
                                        style="font-size:.75rem">
                                        {{ row.kode_pengiriman || '—' }}
                                    </span>
                                </td>

                                <!-- Gudang Pengirim + Operator Kirim -->
                                <td>
                                    <div class="fw-semibold text-dark small">{{ row.nama_gudang || '-' }}</div>
                                    <div class="d-flex align-items-center gap-1 mt-1">
                                        <div class="op-avatar op-avatar-gudang">
                                            <i data-lucide="user" style="width:11px"></i>
                                        </div>
                                        <span class="text-muted" style="font-size:.75rem">
                                            {{ row.nama_operator_kirim || '-' }}
                                        </span>
                                    </div>
                                </td>

                                <!-- Cabang Penerima + Operator Masuk -->
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="store-avatar">
                                            <i data-lucide="store" style="width:11px"></i>
                                        </div>
                                        <div>
                                            <div class="fw-semibold text-dark small">{{ row.nama_cabang || '-' }}</div>
                                            <div class="d-flex align-items-center gap-1 mt-1">
                                                <div class="op-avatar op-avatar-toko">
                                                    <i data-lucide="user-check" style="width:11px"></i>
                                                </div>
                                                <span class="text-muted" style="font-size:.75rem">
                                                    {{ row.nama_operator_masuk || '-' }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Waktu Masuk -->
                                <td>
                                    <div class="fw-semibold text-dark" style="font-size:12px">
                                        {{ formatDate(row.waktu_masuk) }}
                                    </div>
                                </td>

                                <!-- Aksi -->
                                <td class="text-center pe-4">
                                    <a :href="detailUrl(row.id)"
                                        class="btn btn-sm btn-outline-success rounded-3 px-3 py-1 fw-semibold"
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

    <!-- ══ FOOTER: pagination ════════════════════════════════════════════════ -->
    <div class="glass-panel border-0 shadow-sm bg-white p-4
                d-flex justify-content-between align-items-center flex-wrap gap-3"
        style="border-radius:16px">
        <p class="small text-muted mb-0 fw-medium">
            Menampilkan <b>{{ paginatedGroups.length }}</b> dari
            <b>{{ filteredGroups.length }}</b> cabang
            (<b>{{ totalTransaksi }}</b> penerimaan)
        </p>
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
    [v-cloak] {
        display: none;
    }

    .font-mono {
        font-family: 'Courier New', monospace;
    }

    .cabang-header {
        background: linear-gradient(135deg, #064e3b 0%, #059669 100%);
    }

    .cabang-header-icon {
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

    .btn-cabang-toggle {
        background: rgba(255, 255, 255, .15);
        color: #fff;
        border: 1px solid rgba(255, 255, 255, .3);
        font-size: .78rem;
    }

    .btn-cabang-toggle:hover {
        background: rgba(255, 255, 255, .28);
        color: #fff;
    }

    .th-gudang {
        background: rgba(254, 243, 199, .55) !important;
    }

    .th-toko {
        background: rgba(209, 250, 229, .55) !important;
    }

    .row-item {
        transition: background .12s;
    }

    .row-item:hover {
        background: #f0fdf4 !important;
    }

    .op-avatar {
        width: 18px;
        height: 18px;
        border-radius: 50%;
        display: grid;
        place-items: center;
        flex-shrink: 0;
    }

    .op-avatar-gudang {
        background: #fef3c7;
        color: #d97706;
    }

    .op-avatar-toko {
        background: #d1fae5;
        color: #059669;
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

    .page-link {
        color: #334155;
    }

    .page-item.active .page-link {
        background-color: #059669;
        color: #fff;
        border-color: #059669;
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
                allRows: [],
                gudangList: [],
                cabangList: [],
                search: '',
                page: 1,
                perPage: 5,
                collapsed: [],
                filter: {
                    tgl_awal: '<?= date('Y-m-01') ?>',
                    tgl_akhir: '<?= date('Y-m-d') ?>',
                    gudang_id: '',
                    cabang_id: '',
                },
            };
        },

        computed: {
            /* Grup per cabang penerima */
            groupedByCabang() {
                const map = new Map();
                this.allRows.forEach(row => {
                    const key = row.cabang_id || '__unknown__';
                    if (!map.has(key)) {
                        map.set(key, {
                            cabang_id: key,
                            nama_cabang: row.nama_cabang || '(Tidak Diketahui)',
                            rows: [],
                        });
                    }
                    map.get(key).rows.push(row);
                });
                return Array.from(map.values());
            },

            filteredGroups() {
                if (!this.search) return this.groupedByCabang;
                const s = this.search.toLowerCase();
                return this.groupedByCabang.map(group => {
                    const rows = group.rows.filter(r =>
                        (r.kode_masuk || '').toLowerCase().includes(s) ||
                        (r.kode_pengiriman || '').toLowerCase().includes(s) ||
                        (r.nama_gudang || '').toLowerCase().includes(s) ||
                        (r.nama_cabang || '').toLowerCase().includes(s) ||
                        (r.nama_operator_kirim || '').toLowerCase().includes(s) ||
                        (r.nama_operator_masuk || '').toLowerCase().includes(s)
                    );
                    return rows.length ? {
                        ...group,
                        rows
                    } : null;
                }).filter(Boolean);
            },

            totalPage() {
                return Math.ceil(this.filteredGroups.length / this.perPage);
            },
            paginatedGroups() {
                const s = (this.page - 1) * this.perPage;
                return this.filteredGroups.slice(s, s + this.perPage);
            },
            totalTransaksi() {
                return this.filteredGroups.reduce((s, g) => s + g.rows.length, 0);
            },
        },

        methods: {
            async load() {
                this.loading = true;
                try {
                    const res = await axios.get('<?= base_url('laporan/barang-masuk/list') ?>', {
                        params: this.filter,
                    });
                    this.allRows = res.data.data;
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
                    tgl_awal: '<?= date('Y-m-01') ?>',
                    tgl_akhir: '<?= date('Y-m-d') ?>',
                    gudang_id: '',
                    cabang_id: '',
                };
                this.search = '';
                this.load();
            },

            toggleCabang(id) {
                const idx = this.collapsed.indexOf(id);
                if (idx === -1) this.collapsed.push(id);
                else this.collapsed.splice(idx, 1);
                this.$nextTick(() => lucide.createIcons());
            },

            detailUrl(id) {
                return `<?= base_url('laporan/barang-masuk/detail') ?>/${id}`;
            },

            uniqueGudang(rows) {
                return new Set(rows.map(r => r.gudang_id).filter(Boolean)).size;
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

            exportCSV() {
                if (!this.filteredGroups.length) return;
                const headers = [
                    'Cabang Penerima', 'Kode Masuk', 'Kode Pengiriman',
                    'Gudang Pengirim', 'Operator Pengirim',
                    'Operator Penerima', 'Waktu Masuk',
                ];
                const rows = [];
                this.filteredGroups.forEach(group => {
                    group.rows.forEach(r => {
                        rows.push([
                            group.nama_cabang,
                            r.kode_masuk,
                            r.kode_pengiriman || '',
                            r.nama_gudang || '',
                            r.nama_operator_kirim || '',
                            r.nama_operator_masuk || '',
                            r.waktu_masuk,
                        ]);
                    });
                });
                const csv = [headers, ...rows]
                    .map(r => r.map(v => `"${v ?? ''}"`).join(','))
                    .join('\n');
                const a = document.createElement('a');
                a.href = 'data:text/csv;charset=utf-8,' + encodeURIComponent('\uFEFF' + csv);
                a.download = `laporan_barang_masuk_${new Date().toISOString().slice(0, 10)}.csv`;
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