<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid py-4 px-3" style="min-height:100vh;">
    <div class="glass-panel p-4 d-flex flex-column border-0 shadow-lg position-relative overflow-hidden"
        id="appDetail" style="border-radius:24px;">

        <!-- ══ HEADER ══════════════════════════════════════════════════════ -->
        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-4
                    border-secondary border-opacity-10">
            <div class="d-flex align-items-center gap-3">
                <a href="<?= base_url('laporan/barang-masuk') ?>"
                    class="btn btn-icon btn-white shadow-sm rounded-circle p-2">
                    <i data-lucide="chevron-left" class="text-dark" style="width:20px"></i>
                </a>
                <div>
                    <h4 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                        <i data-lucide="package-check" class="text-success"></i>
                        Detail Barang Masuk
                    </h4>
                    <p class="text-muted small mb-0 mt-1 d-flex align-items-center gap-2">
                        <span class="badge bg-success px-2">{{ header.kode_masuk }}</span>
                        <i data-lucide="calendar" style="width:12px"></i>
                        {{ formatDate(header.waktu_masuk) }}
                    </p>
                </div>
            </div>
            <!-- <button class="btn btn-outline-dark shadow-sm px-4 d-flex align-items-center gap-2 rounded-pill"
                @click="exportCSV" :disabled="!items.length">
                <i data-lucide="download" style="width:18px"></i> Export CSV
            </button> -->
        </div>

        <!-- ══ INFO CARDS ═══════════════════════════════════════════════════ -->
        <div class="row g-3 mb-4">

            <!-- Gudang Pengirim -->
            <div class="col-md-3">
                <div class="glass-panel p-3 bg-white bg-opacity-40 border-white h-100 shadow-sm transition-hover">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i data-lucide="warehouse" class="text-warning" style="width:16px"></i>
                        <small class="text-muted fw-bold text-uppercase" style="font-size:10px">
                            Gudang Pengirim
                        </small>
                    </div>
                    <h6 class="fw-bold mb-0 text-dark ps-4">{{ header.nama_gudang || '-' }}</h6>
                    <div class="d-flex align-items-center gap-1 ps-4 mt-1">
                        <div class="op-dot op-dot-gudang">
                            <i data-lucide="user" style="width:9px"></i>
                        </div>
                        <small class="text-muted" style="font-size:11px">
                            {{ header.nama_operator_kirim || '-' }}
                        </small>
                    </div>
                </div>
            </div>

            <!-- Cabang Penerima -->
            <div class="col-md-3">
                <div class="glass-panel p-3 bg-white bg-opacity-40 border-white h-100 shadow-sm transition-hover">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i data-lucide="store" class="text-success" style="width:16px"></i>
                        <small class="text-muted fw-bold text-uppercase" style="font-size:10px">
                            Cabang Penerima
                        </small>
                    </div>
                    <h6 class="fw-bold mb-0 text-dark ps-4">{{ header.nama_cabang || '-' }}</h6>
                    <div class="d-flex align-items-center gap-1 ps-4 mt-1">
                        <div class="op-dot op-dot-toko">
                            <i data-lucide="user-check" style="width:9px"></i>
                        </div>
                        <small class="text-muted" style="font-size:11px">
                            {{ header.nama_operator_masuk || '-' }}
                        </small>
                    </div>
                </div>
            </div>

            <!-- Kode Pengiriman -->
            <div class="col-md-3">
                <div class="glass-panel p-3 bg-white bg-opacity-40 border-white h-100 shadow-sm transition-hover">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i data-lucide="send" class="text-primary" style="width:16px"></i>
                        <small class="text-muted fw-bold text-uppercase" style="font-size:10px">
                            Kode Pengiriman
                        </small>
                    </div>
                    <h6 class="fw-bold mb-1 font-mono text-primary ps-4" style="font-size:.82rem">
                        {{ header.kode_pengiriman || '—' }}
                    </h6>
                </div>
            </div>

            <!-- Waktu Masuk -->
            <div class="col-md-3">
                <div class="glass-panel p-3 bg-white bg-opacity-40 border-white h-100 shadow-sm transition-hover">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i data-lucide="clock" class="text-info" style="width:16px"></i>
                        <small class="text-muted fw-bold text-uppercase" style="font-size:10px">
                            Waktu Masuk
                        </small>
                    </div>
                    <h6 class="fw-bold mb-0 text-dark ps-4" style="font-size:.85rem">
                        {{ formatDate(header.waktu_masuk) }}
                    </h6>
                </div>
            </div>
        </div>

        <!-- ══ SUMMARY CHIPS ════════════════════════════════════════════════ -->
        <div v-if="summary" class="d-flex gap-2 flex-wrap mb-3">
            <span class="selisih-chip chip-ok">
                <i data-lucide="check-circle" style="width:11px"></i>
                Sesuai: {{ summary.sesuai }}
            </span>
            <span v-if="summary.lebih" class="selisih-chip chip-lebih">
                <i data-lucide="arrow-up" style="width:11px"></i>
                Lebih: {{ summary.lebih }}
            </span>
            <span v-if="summary.kurang" class="selisih-chip chip-kurang">
                <i data-lucide="arrow-down" style="width:11px"></i>
                Kurang: {{ summary.kurang }}
            </span>
        </div>

        <!-- ══ TABLE ITEMS ══════════════════════════════════════════════════ -->
        <div class="table-responsive flex-fill custom-scroll border rounded-4
                    bg-white bg-opacity-30 shadow-sm">
            <table class="table align-middle border-0 mb-0">
                <thead class="bg-dark text-white">
                    <!-- Level 1: grup kolom -->
                    <tr class="small text-uppercase">
                        <th class="ps-4 border-0 py-3" style="width:40px">#</th>
                        <th class="border-0 py-3">
                            <div class="d-flex align-items-center gap-2">
                                <i data-lucide="package" style="width:14px"></i> Barang
                            </div>
                        </th>
                        <!-- Kiriman Gudang -->
                        <th class="border-0 py-3 text-center" colspan="2">
                            <div class="d-flex align-items-center justify-content-center gap-2">
                                <i data-lucide="send" style="width:14px"></i> Kiriman Gudang
                            </div>
                        </th>
                        <!-- Aktual Diterima -->
                        <th class="border-0 py-3 text-center" colspan="2">
                            <div class="d-flex align-items-center justify-content-center gap-2">
                                <i data-lucide="package-check" style="width:14px"></i> Aktual Diterima
                            </div>
                        </th>
                        <!-- Selisih -->
                        <th class="border-0 py-3 text-center pe-4">
                            <div class="d-flex align-items-center justify-content-center gap-2">
                                <i data-lucide="git-diff" style="width:14px"></i> Selisih
                            </div>
                        </th>
                    </tr>
                    <!-- Level 2: sub-header -->
                    <tr style="background:#2d2d2d" class="small text-uppercase">
                        <th class="border-0 py-2 ps-4" colspan="2"></th>
                        <th class="border-0 py-2 text-center text-white-50 fw-semibold"
                            style="font-size:.7rem">QTY</th>
                        <th class="border-0 py-2 text-center text-white-50 fw-semibold"
                            style="font-size:.7rem">SATUAN</th>
                        <th class="border-0 py-2 text-center text-white-50 fw-semibold"
                            style="font-size:.7rem">QTY</th>
                        <th class="border-0 py-2 text-center text-white-50 fw-semibold"
                            style="font-size:.7rem">SATUAN</th>
                        <th class="border-0 py-2 text-center pe-4"></th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Loading -->
                    <tr v-if="loading">
                        <td colspan="7" class="text-center py-5">
                            <div class="spinner-border text-success mb-2" role="status"></div>
                            <p class="text-muted small mb-0">Memuat data...</p>
                        </td>
                    </tr>
                    <!-- Empty -->
                    <tr v-else-if="!items.length">
                        <td colspan="7" class="text-center py-5">
                            <div class="d-flex flex-column align-items-center" style="opacity:.4">
                                <i data-lucide="inbox" style="width:48px;height:48px" class="mb-2"></i>
                                <p class="fw-bold mb-0">Tidak ada item</p>
                            </div>
                        </td>
                    </tr>
                    <!-- Rows -->
                    <tr v-else v-for="(item, idx) in items" :key="item.id"
                        class="border-bottom border-light row-item"
                        :class="rowHighlightClass(item)">

                        <td class="ps-4 text-muted small">{{ idx + 1 }}</td>

                        <!-- Barang -->
                        <td class="py-3">
                            <div class="fw-bold text-dark">{{ item.nama_barang }}</div>
                            <div class="font-mono text-muted" style="font-size:.7rem">
                                {{ item.barcode || '' }}
                            </div>
                        </td>

                        <!-- Qty Kiriman -->
                        <td class="py-3 text-center">
                            <span class="badge bg-light text-dark border fw-bold px-2">
                                {{ item.qty_kiriman }}
                            </span>
                        </td>
                        <!-- Satuan Kirim -->
                        <td class="py-3 text-center">
                            <span class="satuan-pill satuan-kirim">
                                {{ item.satuan_kirim || '-' }}
                            </span>
                        </td>

                        <!-- Qty Aktual -->
                        <td class="py-3 text-center">
                            <span class="badge fw-bold px-2" :class="qtyAktualBadge(item)">
                                {{ item.qty_aktual }}
                            </span>
                        </td>
                        <!-- Satuan Simpan -->
                        <td class="py-3 text-center">
                            <span class="satuan-pill satuan-simpan">
                                {{ item.satuan_simpan || '-' }}
                            </span>
                        </td>

                        <!-- Selisih -->
                        <td class="py-3 text-center pe-4">
                            <span v-if="item.selisih === 0" class="selisih-chip chip-ok">
                                <i data-lucide="check" style="width:10px"></i> Sesuai
                            </span>
                            <span v-else-if="item.selisih > 0" class="selisih-chip chip-lebih">
                                +{{ item.selisih }}
                            </span>
                            <span v-else class="selisih-chip chip-kurang">
                                {{ item.selisih }}
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- ══ FOOTER SUMMARY ════════════════════════════════════════════════ -->
        <div class="row justify-content-end mt-4">
            <div class="col-md-5">
                <div class="glass-panel p-4 bg-white bg-opacity-60 border-white shadow-sm"
                    style="border-radius:20px">

                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted fw-medium d-flex align-items-center gap-2">
                            <i data-lucide="box" style="width:14px"></i> Total Jenis Barang
                        </span>
                        <span class="fw-bold text-dark">{{ items.length }} jenis</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted fw-medium d-flex align-items-center gap-2">
                            <i data-lucide="send" style="width:14px"></i> Total Qty Kiriman
                        </span>
                        <span class="fw-bold text-dark">{{ totalKiriman }}</span>
                    </div>

                    <hr class="my-3 border-dark opacity-10">

                    <div class="d-flex justify-content-between mb-3">
                        <span class="fw-bold text-dark fs-5">Total Qty Diterima</span>
                        <span class="h5 fw-bold text-success mb-0">{{ totalAktual }}</span>
                    </div>

                    <div class="p-3 bg-light rounded-3 border border-white">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted small d-flex align-items-center gap-2">
                                <i data-lucide="check-circle" style="width:14px"></i> Item Sesuai
                            </span>
                            <span class="text-success fw-bold">
                                {{ summary ? summary.sesuai : 0 }}
                            </span>
                        </div>
                        <div class="d-flex justify-content-between pt-2
                                    border-top border-dashed border-secondary">
                            <span class="text-muted small fw-bold d-flex align-items-center gap-2">
                                <i data-lucide="alert-triangle" style="width:14px"></i> TOTAL SELISIH
                            </span>
                            <span class="fw-bold fs-5"
                                :class="totalSelisih !== 0 ? 'text-danger' : 'text-success'">
                                {{ totalSelisih > 0 ? '+' : '' }}{{ totalSelisih }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<style>
    [v-cloak] {
        display: none;
    }

    .font-mono {
        font-family: 'Courier New', monospace;
    }

    .glass-panel {
        backdrop-filter: blur(15px);
        -webkit-backdrop-filter: blur(15px);
    }

    .border-dashed {
        border-style: dashed !important;
    }

    .custom-scroll {
        max-height: 440px;
        overflow-y: auto;
    }

    .btn-white {
        background: white;
        border: 1px solid #eee;
    }

    .transition-hover:hover {
        transform: translateY(-3px);
        transition: all .3s ease;
        background: rgba(255, 255, 255, .6) !important;
    }

    /* Op dots */
    .op-dot {
        width: 16px;
        height: 16px;
        border-radius: 50%;
        display: grid;
        place-items: center;
        flex-shrink: 0;
    }

    .op-dot-gudang {
        background: #fef3c7;
        color: #d97706;
    }

    .op-dot-toko {
        background: #d1fae5;
        color: #059669;
    }

    /* Row highlight */
    .row-item {
        transition: background .12s;
    }

    .row-item:hover {
        background: #f0fdf4 !important;
    }

    .row-kurang {
        background: rgba(254, 226, 226, .3) !important;
    }

    .row-lebih {
        background: rgba(219, 234, 254, .25) !important;
    }

    /* Satuan pills */
    .satuan-pill {
        display: inline-block;
        padding: 2px 9px;
        border-radius: 10px;
        font-size: .72rem;
        font-weight: 700;
    }

    .satuan-kirim {
        background: #e0e7ff;
        color: #3730a3;
        border: 1px solid #c7d2fe;
    }

    .satuan-simpan {
        background: #d1fae5;
        color: #065f46;
        border: 1px solid #6ee7b7;
    }

    /* Selisih chips */
    .selisih-chip {
        padding: 3px 10px;
        border-radius: 20px;
        font-size: .75rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 3px;
        border: 1px solid;
    }

    .chip-ok {
        background: #d1fae5;
        color: #065f46;
        border-color: #6ee7b7;
    }

    .chip-lebih {
        background: #dbeafe;
        color: #1e40af;
        border-color: #93c5fd;
    }

    .chip-kurang {
        background: #fee2e2;
        color: #991b1b;
        border-color: #fca5a5;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    #appDetail {
        animation: fadeIn .5s ease-out;
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
                loading: true,
                header: {},
                items: [],
                id: <?= (int) $id ?>,
            };
        },

        computed: {
            summary() {
                if (!this.items.length) return null;
                return {
                    sesuai: this.items.filter(i => i.selisih === 0).length,
                    lebih: this.items.filter(i => i.selisih > 0).length,
                    kurang: this.items.filter(i => i.selisih < 0).length,
                };
            },
            totalKiriman() {
                return this.items.reduce((s, i) => s + parseFloat(i.qty_kiriman || 0), 0);
            },
            totalAktual() {
                return this.items.reduce((s, i) => s + parseFloat(i.qty_aktual || 0), 0);
            },
            totalSelisih() {
                return this.items.reduce((s, i) => s + parseFloat(i.selisih || 0), 0);
            },
        },

        methods: {
            async loadDetail() {
                this.loading = true;
                try {
                    const res = await axios.get(
                        `<?= base_url('laporan/barang-masuk/get-detail') ?>/${this.id}`
                    );
                    this.header = res.data.header;
                    this.items = res.data.items;
                    this.$nextTick(() => lucide.createIcons());
                } catch (e) {
                    console.error(e);
                } finally {
                    this.loading = false;
                }
            },

            rowHighlightClass(item) {
                if (item.selisih === 0) return '';
                return item.selisih > 0 ? 'row-lebih' : 'row-kurang';
            },

            qtyAktualBadge(item) {
                if (item.selisih === 0)
                    return 'bg-success bg-opacity-10 text-success border border-success border-opacity-25';
                if (item.selisih > 0)
                    return 'bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25';
                return 'bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25';
            },

            formatDate(d) {
                if (!d) return '-';
                return new Date(d).toLocaleDateString('id-ID', {
                    day: '2-digit',
                    month: 'short',
                    year: '2-digit',
                    hour: '2-digit',
                    minute: '2-digit',
                }).replace(/\./g, ':');
            },

            exportCSV() {
                if (!this.items.length) return;
                const headers = [
                    'No', 'Nama Barang', 'Barcode',
                    'Qty Kiriman', 'Satuan Kirim',
                    'Qty Aktual', 'Satuan Simpan',
                    'Selisih',
                ];
                const rows = this.items.map((item, i) => [
                    i + 1,
                    item.nama_barang,
                    item.barcode || '',
                    item.qty_kiriman,
                    item.satuan_kirim || '',
                    item.qty_aktual,
                    item.satuan_simpan || '',
                    item.selisih,
                ]);
                const csv = [headers, ...rows]
                    .map(r => r.map(v => `"${v ?? ''}"`).join(','))
                    .join('\n');
                const kode = this.header?.kode_masuk ?? 'detail';
                const a = document.createElement('a');
                a.href = 'data:text/csv;charset=utf-8,' + encodeURIComponent('\uFEFF' + csv);
                a.download = `barang_masuk_${kode}_${new Date().toISOString().slice(0,10)}.csv`;
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