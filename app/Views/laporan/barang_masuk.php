<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<div id="app" v-cloak class="container-fluid py-4">

    <!-- ══ TOAST ════════════════════════════════════════════════════════════ -->
    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index:9999">
        <div v-for="(t,i) in toasts" :key="t.id"
            class="toast show align-items-center border-0 shadow-lg mb-2"
            :class="'toast-' + t.type" role="alert">
            <div class="d-flex">
                <div class="toast-body d-flex align-items-center gap-2">
                    <i :data-lucide="t.icon" style="width:16px"></i>
                    <span>{{ t.message }}</span>
                </div>
                <button class="btn-close btn-close-white me-2 m-auto" @click="toasts.splice(i,1)"></button>
            </div>
        </div>
    </div>

    <!-- ══ MODAL VOID ════════════════════════════════════════════════════════ -->
    <div class="modal fade" id="modalVoid" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius:16px;overflow:hidden">
                <div class="modal-header border-0"
                    style="background:linear-gradient(135deg,#7f1d1d,#dc2626);color:#fff">
                    <h5 class="modal-title fw-bold d-flex align-items-center gap-2">
                        <i data-lucide="ban" style="width:20px"></i>
                        Void Barang Masuk
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert border-0 mb-3 d-flex gap-2 align-items-start"
                        style="background:#fef2f2;color:#991b1b;border-radius:10px">
                        <i data-lucide="alert-triangle" style="width:18px;flex-shrink:0;margin-top:2px"></i>
                        <div class="small">
                            Tindakan ini akan <strong>mengurangi stok inventory</strong> cabang sebesar
                            qty yang pernah masuk dan mengubah status menjadi
                            <strong>dibatalkan</strong>. Tidak dapat diurungkan.
                        </div>
                    </div>
                    <div class="mb-3 p-3 rounded-3 border" style="background:#f8fafc;font-size:.85rem">
                        <div class="text-muted small mb-1">Kode Masuk</div>
                        <div class="fw-bold font-mono">{{ voidTarget?.kode_masuk }}</div>
                    </div>
                    <label class="form-label fw-semibold small">
                        Alasan Void <span class="text-danger">*</span>
                    </label>
                    <textarea class="form-control rounded-3" rows="3"
                        placeholder="Contoh: Data duplikat, kesalahan input, dll."
                        v-model="voidReason"
                        :class="{'is-invalid': voidReasonError}"></textarea>
                    <div v-if="voidReasonError" class="invalid-feedback">{{ voidReasonError }}</div>
                </div>
                <div class="modal-footer border-0 bg-light gap-2">
                    <button class="btn btn-light border rounded-3 fw-semibold px-4"
                        data-bs-dismiss="modal">Batal</button>
                    <button class="btn btn-danger rounded-3 fw-bold px-4 d-flex align-items-center gap-2"
                        @click="submitVoid" :disabled="voidLoading">
                        <span v-if="voidLoading" class="spinner-border spinner-border-sm"></span>
                        <i v-else data-lucide="ban" style="width:15px"></i>
                        Konfirmasi Void
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ══ HEADER ════════════════════════════════════════════════════════════ -->
    <div class="glass-panel p-4 mb-4 border-0 shadow-sm"
        style="border-radius:20px;background:linear-gradient(145deg,#ffffff,#f8f9fa)">
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

    <!-- ══ FILTER ════════════════════════════════════════════════════════════ -->
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
                        placeholder="Kode masuk, kode kirim, operator, cabang..."
                        v-model="search" @input="page = 1">
                </div>
            </div>

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

            <div v-show="!collapsed.includes(group.cabang_id)">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead style="background:#f8fafc">
                            <tr class="text-muted">
                                <th class="ps-4 py-3 border-0 small text-uppercase fw-bolder">Kode Masuk</th>
                                <th class="py-3 border-0 small text-uppercase fw-bolder">Kode Pengiriman</th>
                                <th class="py-3 border-0 small text-uppercase fw-bolder th-gudang">
                                    <span class="d-flex align-items-center gap-1">
                                        <i data-lucide="warehouse" style="width:12px"></i> Gudang Pengirim
                                    </span>
                                </th>
                                <th class="py-3 border-0 small text-uppercase fw-bolder th-toko">
                                    <span class="d-flex align-items-center gap-1">
                                        <i data-lucide="store" style="width:12px"></i> Cabang Penerima
                                    </span>
                                </th>
                                <th class="py-3 border-0 small text-uppercase fw-bolder">Waktu Masuk</th>
                                <th class="py-3 border-0 small text-uppercase fw-bolder text-center">Status</th>
                                <th class="pe-4 py-3 border-0 small text-uppercase fw-bolder text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="group.rows.length === 0">
                                <td colspan="7" class="text-center py-4 text-muted small">Tidak ada data</td>
                            </tr>
                            <tr v-else v-for="row in group.rows" :key="row.id"
                                class="border-bottom border-light row-item"
                                :class="{'row-void': row.status === 'dibatalkan'}">

                                <td class="ps-4">
                                    <span class="badge bg-light text-dark fw-bolder border px-2 py-1 font-mono"
                                        style="font-size:.75rem">{{ row.kode_masuk }}</span>
                                </td>

                                <td>
                                    <span class="font-mono fw-semibold text-muted" style="font-size:.75rem">
                                        {{ row.kode_pengiriman || '—' }}
                                    </span>
                                </td>

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

                                <td>
                                    <div class="fw-semibold text-dark" style="font-size:12px">
                                        {{ formatDate(row.waktu_masuk) }}
                                    </div>
                                </td>

                                <!-- Status -->
                                <td class="text-center">
                                    <span v-if="row.status === 'dibatalkan'"
                                        class="status-badge badge-void d-inline-flex align-items-center gap-1"
                                        :title="row.reason ? 'Alasan: ' + row.reason : ''">
                                        <i data-lucide="ban" style="width:11px"></i> Dibatalkan
                                    </span>
                                    <span v-else class="status-badge badge-selesai d-inline-flex align-items-center gap-1">
                                        <i data-lucide="check-circle" style="width:11px"></i> Selesai
                                    </span>
                                </td>

                                <!-- Aksi -->
                                <td class="text-center pe-4">
                                    <div class="d-flex align-items-center justify-content-center gap-2">
                                        <a :href="detailUrl(row.id)"
                                            class="btn btn-sm btn-outline-success rounded-3 px-3 py-1 fw-semibold"
                                            style="font-size:.75rem;white-space:nowrap">
                                            <i data-lucide="eye" style="width:13px" class="me-1"></i> Detail
                                        </a>
                                        <button v-if="canVoid && row.status !== 'dibatalkan'"
                                            class="btn btn-sm btn-outline-danger rounded-3 px-3 py-1 fw-semibold"
                                            style="font-size:.75rem;white-space:nowrap"
                                            @click="openVoidModal(row)">
                                            <i data-lucide="ban" style="width:13px" class="me-1"></i> Void
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- ══ PAGINATION ════════════════════════════════════════════════════════ -->
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

    .toast {
        min-width: 280px;
        border-radius: 12px;
    }

    .toast-success {
        background: linear-gradient(135deg, #10b981, #059669);
        color: #fff;
    }

    .toast-error {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: #fff;
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

    .row-void td {
        opacity: .6;
    }

    .row-void:hover {
        background: #fff5f5 !important;
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

    .status-badge {
        font-size: .7rem;
        font-weight: 700;
        padding: 3px 9px;
        border-radius: 20px;
        border: 1px solid;
    }

    .badge-selesai {
        background: #dcfce7;
        color: #15803d;
        border-color: #bbf7d0;
    }

    .badge-void {
        background: #fee2e2;
        color: #b91c1c;
        border-color: #fecaca;
        cursor: help;
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
                canVoid: <?= in_array(session()->get('role'), ['owner', 'admin']) ? 'true' : 'false' ?>,
                voidTarget: null,
                voidReason: '',
                voidReasonError: '',
                voidLoading: false,
                modalVoidInst: null,
                toasts: [],
                toastId: 0,
            };
        },

        computed: {
            groupedByCabang() {
                const map = new Map();
                this.allRows.forEach(row => {
                    const key = row.cabang_id || '__unknown__';
                    if (!map.has(key)) map.set(key, {
                        cabang_id: key,
                        nama_cabang: row.nama_cabang || '(Tidak Diketahui)',
                        rows: []
                    });
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
                        params: this.filter
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
                    cabang_id: ''
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
                    minute: '2-digit'
                });
            },
            showToast(message, type = 'success') {
                const icons = {
                    success: 'check-circle',
                    error: 'x-circle'
                };
                const t = {
                    id: this.toastId++,
                    message,
                    type,
                    icon: icons[type] ?? 'info'
                };
                this.toasts.push(t);
                setTimeout(() => {
                    const i = this.toasts.findIndex(x => x.id === t.id);
                    if (i > -1) this.toasts.splice(i, 1);
                }, 4000);
                this.$nextTick(() => lucide.createIcons());
            },
            openVoidModal(row) {
                this.voidTarget = row;
                this.voidReason = '';
                this.voidReasonError = '';
                this.modalVoidInst.show();
                this.$nextTick(() => lucide.createIcons());
            },
            async submitVoid() {
                this.voidReasonError = '';
                if (!this.voidReason.trim()) {
                    this.voidReasonError = 'Alasan void wajib diisi';
                    return;
                }
                this.voidLoading = true;
                try {
                    const res = await axios.post(
                        `<?= base_url('laporan/barang-masuk/void') ?>/${this.voidTarget.id}`, {
                            reason: this.voidReason.trim()
                        }
                    );
                    const idx = this.allRows.findIndex(r => r.id === this.voidTarget.id);
                    if (idx > -1) {
                        this.allRows[idx].status = 'dibatalkan';
                        this.allRows[idx].reason = this.voidReason.trim();
                        this.allRows[idx].nama_voided_by = res.data.nama_voided_by;
                        this.allRows[idx].voided_at = res.data.voided_at;
                    }
                    this.modalVoidInst.hide();
                    this.showToast(`${this.voidTarget.kode_masuk} berhasil di-void`, 'success');
                    this.$nextTick(() => lucide.createIcons());
                } catch (err) {
                    this.showToast(err.response?.data?.message || 'Gagal melakukan void', 'error');
                } finally {
                    this.voidLoading = false;
                }
            },
        },

        mounted() {
            this.modalVoidInst = new bootstrap.Modal(document.getElementById('modalVoid'));
            this.load();
            lucide.createIcons();
        },
    }).mount('#app');
</script>
<?= $this->endSection() ?>