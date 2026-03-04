<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid py-4 px-3" style="min-height: 100vh;">
    <div class="glass-panel p-4 d-flex flex-column border-0 shadow-lg position-relative overflow-hidden" id="appDetail" style="border-radius: 24px;">

        <!-- ══ HEADER ══════════════════════════════════════════════════════ -->
        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-4 border-white border-opacity-20">
            <div class="d-flex align-items-center gap-3">
                <a href="<?= base_url('laporan/pengiriman-gudang') ?>" class="btn btn-icon btn-white shadow-sm rounded-circle p-2">
                    <i data-lucide="chevron-left" class="text-dark" style="width: 20px;"></i>
                </a>
                <div>
                    <h4 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                        <i data-lucide="send" class="text-warning"></i>
                        Detail Pengiriman Gudang
                    </h4>
                    <p class="text-muted small mb-0 mt-1 d-flex align-items-center gap-2">
                        <span class="badge bg-warning text-dark px-2">{{ header.kode_pengiriman }}</span>
                        <i data-lucide="calendar" style="width: 12px;"></i> {{ formatDate(header.waktu_pengiriman) }}
                    </p>
                </div>
            </div>
            <div class="d-flex gap-2">
                <!-- <button class="btn btn-outline-dark shadow-sm px-4 d-flex align-items-center gap-2 rounded-pill"
                    @click="exportCSV" :disabled="!items.length">
                    <i data-lucide="download" style="width: 18px;"></i>
                    Export CSV
                </button> -->
            </div>
        </div>

        <!-- ══ INFO CARDS ═══════════════════════════════════════════════════ -->
        <div class="row g-3 mb-4">

            <!-- Gudang & Operator -->
            <div class="col-md-3">
                <div class="glass-panel p-3 bg-white bg-opacity-40 border-white h-100 shadow-sm transition-hover">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i data-lucide="warehouse" class="text-warning" style="width: 16px;"></i>
                        <small class="text-muted fw-bold text-uppercase" style="font-size: 10px;">Gudang & Operator</small>
                    </div>
                    <h6 class="fw-bold mb-0 text-dark ps-4">{{ header.nama_gudang || '-' }}</h6>
                    <small class="text-muted ps-4" style="font-size: 11px;">{{ header.nama_operator || '-' }}</small>
                </div>
            </div>

            <!-- Cabang / Toko Tujuan -->
            <div class="col-md-3">
                <div class="glass-panel p-3 bg-white bg-opacity-40 border-white h-100 shadow-sm transition-hover">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i data-lucide="store" class="text-success" style="width: 16px;"></i>
                        <small class="text-muted fw-bold text-uppercase" style="font-size: 10px;">Cabang / Toko Tujuan</small>
                    </div>
                    <h6 class="fw-bold mb-0 text-dark ps-4">{{ header.nama_cabang || '-' }}</h6>
                </div>
            </div>

            <!-- Waktu Pengiriman -->
            <div class="col-md-3">
                <div class="glass-panel p-3 bg-white bg-opacity-40 border-white h-100 shadow-sm transition-hover">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i data-lucide="clock" class="text-info" style="width: 16px;"></i>
                        <small class="text-muted fw-bold text-uppercase" style="font-size: 10px;">Waktu Pengiriman</small>
                    </div>
                    <h6 class="fw-bold mb-0 text-dark ps-4" style="font-size:.85rem">{{ formatDate(header.waktu_pengiriman) }}</h6>
                </div>
            </div>

            <!-- Status -->
            <div class="col-md-3">
                <div class="glass-panel p-3 bg-white bg-opacity-40 border-white h-100 shadow-sm transition-hover">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i data-lucide="check-circle-2" class="text-primary" style="width: 16px;"></i>
                        <small class="text-muted fw-bold text-uppercase" style="font-size: 10px;">Status</small>
                    </div>
                    <div class="ps-4">
                        <span class="badge px-3 py-1 fw-bold" :class="statusBadgeClass(header.status)">
                            {{ statusLabel(header.status) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- ══ SUMMARY CHIPS ════════════════════════════════════════════════ -->
        <div v-if="items.length" class="d-flex gap-2 flex-wrap mb-3">
            <span class="summary-chip chip-total">
                <i data-lucide="package" style="width: 11px;"></i>
                Total Item: {{ items.length }} jenis
            </span>
            <span class="summary-chip chip-qty">
                <i data-lucide="layers" style="width: 11px;"></i>
                Total Qty: {{ totalQty }}
            </span>
        </div>

        <!-- ══ TABLE ITEMS ══════════════════════════════════════════════════ -->
        <div class="table-responsive flex-fill custom-scroll border rounded-4 bg-white bg-opacity-30 shadow-sm">
            <table class="table align-middle border-0 mb-0">
                <thead class="bg-dark text-white shadow-sm">
                    <tr class="small text-uppercase">
                        <th class="ps-4 border-0 py-3" style="width: 40px;">#</th>
                        <th class="border-0 py-3">
                            <div class="d-flex align-items-center gap-2">
                                <i data-lucide="package" style="width: 14px;"></i> Nama Barang
                            </div>
                        </th>
                        <th class="border-0 py-3 text-center">
                            <div class="d-flex align-items-center justify-content-center gap-2">
                                <i data-lucide="hash" style="width: 14px;"></i> Qty Dikirim
                            </div>
                        </th>
                        <th class="border-0 py-3 text-center pe-4">
                            <div class="d-flex align-items-center justify-content-center gap-2">
                                <i data-lucide="ruler" style="width: 14px;"></i> Satuan
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Loading -->
                    <tr v-if="loading">
                        <td colspan="4" class="text-center py-5">
                            <div class="spinner-border text-warning mb-2" role="status"></div>
                            <p class="text-muted small mb-0">Memuat data...</p>
                        </td>
                    </tr>
                    <!-- Empty -->
                    <tr v-else-if="!items.length && !loading">
                        <td colspan="4" class="text-center py-5">
                            <div class="d-flex flex-column align-items-center" style="opacity:.4">
                                <i data-lucide="inbox" style="width:48px;height:48px" class="mb-2"></i>
                                <p class="fw-bold mb-0">Tidak ada item</p>
                            </div>
                        </td>
                    </tr>
                    <!-- Rows -->
                    <tr v-else v-for="(item, idx) in items" :key="item.id"
                        class="border-bottom border-white border-opacity-50 row-item">
                        <td class="ps-4 text-muted small">{{ idx + 1 }}</td>
                        <td class="py-3">
                            <div class="fw-bold text-dark">{{ item.nama_barang }}</div>
                        </td>
                        <!-- Qty -->
                        <td class="py-3 text-center">
                            <span class="badge bg-warning bg-opacity-15 text-warning-emphasis border border-warning border-opacity-25 fw-bold px-3">
                                {{ item.qty }}
                            </span>
                        </td>
                        <!-- Satuan (dari stok_gudang join satuan) -->
                        <td class="py-3 text-center pe-4">
                            <small class="text-muted fw-bold text-uppercase" style="font-size: 9px;">
                                {{ item.nama_satuan || '-' }}
                            </small>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- ══ FOOTER SUMMARY ════════════════════════════════════════════════ -->
        <div class="row justify-content-end mt-4">
            <div class="col-md-4">
                <div class="glass-panel p-4 bg-white bg-opacity-60 border-white shadow-sm" style="border-radius: 20px;">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted fw-medium d-flex align-items-center gap-2">
                            <i data-lucide="box" style="width: 14px;"></i> Total Jenis Barang
                        </span>
                        <span class="fw-bold text-dark">{{ items.length }} jenis</span>
                    </div>
                    <hr class="my-3 border-dark opacity-10">
                    <div class="d-flex justify-content-between">
                        <span class="fw-bold text-dark fs-5">Total Qty Dikirim</span>
                        <span class="h5 fw-bold text-warning mb-0">
                            {{ totalQty }} <small class="fs-6 fw-normal text-muted">{{ satuanLabel }}</small>
                        </span>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<style>
    [v-cloak] { display: none; }

    .font-mono { font-family: 'Courier New', monospace; }

    .glass-panel {
        backdrop-filter: blur(15px);
        -webkit-backdrop-filter: blur(15px);
    }

    .custom-scroll {
        max-height: 420px;
        overflow-y: auto;
    }

    .btn-white {
        background: white;
        border: 1px solid #eee;
    }

    .transition-hover:hover {
        transform: translateY(-3px);
        transition: all 0.3s ease;
        background: rgba(255, 255, 255, 0.6) !important;
    }

    .row-item { transition: background .12s; }
    .row-item:hover { background: #fffbeb !important; }

    /* Summary chips */
    .summary-chip {
        padding: 3px 10px;
        border-radius: 20px;
        font-size: .75rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        border: 1px solid;
    }

    .chip-total {
        background: #fef3c7;
        color: #92400e;
        border-color: #fcd34d;
    }

    .chip-qty {
        background: #dbeafe;
        color: #1e40af;
        border-color: #93c5fd;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    #appDetail { animation: fadeIn 0.5s ease-out; }
</style>

<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script>
    const { createApp } = Vue;
    createApp({
        data() {
            return {
                loading: true,
                header:  {},
                items:   [],
                id: <?= (int) $id ?>,
            };
        },

        computed: {
            totalQty() {
                return this.items.reduce((s, i) => s + parseFloat(i.qty || 0), 0);
            },
            satuanLabel() {
                const first = this.items.find(i => i.nama_satuan);
                return first ? first.nama_satuan : '';
            },
        },

        methods: {
            async loadDetail() {
                this.loading = true;
                try {
                    const res = await axios.get(
                        `<?= base_url('laporan/pengiriman-gudang/get-detail') ?>/${this.id}`
                    );
                    this.header = res.data.header;
                    this.items  = res.data.items;
                    this.$nextTick(() => lucide.createIcons());
                } catch (e) {
                    console.error(e);
                } finally {
                    this.loading = false;
                }
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
                    day: '2-digit', month: 'short', year: '2-digit',
                    hour: '2-digit', minute: '2-digit',
                }).replace(/\./g, ':');
            },

            exportCSV() {
                if (!this.items.length) return;
                const headers = ['No', 'Nama Barang', 'Qty Dikirim', 'Satuan'];
                const rows = this.items.map((item, i) => [
                    i + 1,
                    item.nama_barang,
                    item.qty,
                    item.nama_satuan || '',
                ]);
                const csv = [headers, ...rows]
                    .map(r => r.map(v => `"${v ?? ''}"`).join(','))
                    .join('\n');
                const kode = this.header?.kode_pengiriman ?? 'detail';
                const a = document.createElement('a');
                a.href = 'data:text/csv;charset=utf-8,' + encodeURIComponent('\uFEFF' + csv);
                a.download = `pengiriman_${kode}_${new Date().toISOString().slice(0,10)}.csv`;
                a.click();
            },
        },

        mounted() {
            this.loadDetail();
            lucide.createIcons();
        },
    }).mount('#appDetail');
</script>
<?= $this->endSection() ?>