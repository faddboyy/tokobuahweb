<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid py-4 px-3" style="min-height: 100vh;">
    <div class="glass-panel p-4 d-flex flex-column border-0 shadow-lg position-relative overflow-hidden" id="appDetail" style="border-radius: 24px;">

        <!-- ══ HEADER ══════════════════════════════════════════════════════ -->
        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-4 border-white border-opacity-20">
            <div class="d-flex align-items-center gap-3">
                <a href="<?= base_url('laporan/penerimaan-gudang') ?>" class="btn btn-icon btn-white shadow-sm rounded-circle p-2">
                    <i data-lucide="chevron-left" class="text-dark" style="width: 20px;"></i>
                </a>
                <div>
                    <h4 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                        <i data-lucide="clipboard-check" class="text-primary"></i>
                        Detail Penerimaan Gudang
                    </h4>
                    <p class="text-muted small mb-0 mt-1 d-flex align-items-center gap-2">
                        <span class="badge bg-primary px-2">{{ header.kode_penerimaan }}</span>
                        <i data-lucide="calendar" style="width: 12px;"></i> {{ formatDate(header.waktu_penerimaan) }}
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
                        <i data-lucide="warehouse" class="text-primary" style="width: 16px;"></i>
                        <small class="text-muted fw-bold text-uppercase" style="font-size: 10px;">Gudang & Operator</small>
                    </div>
                    <h6 class="fw-bold mb-0 text-dark ps-4">{{ header.nama_gudang || '-' }}</h6>
                    <small class="text-muted ps-4" style="font-size: 11px;">{{ header.nama_operator || '-' }}</small>
                </div>
            </div>

            <!-- Supplier & Kode PO -->
            <div class="col-md-3">
                <div class="glass-panel p-3 bg-white bg-opacity-40 border-white h-100 shadow-sm transition-hover">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i data-lucide="truck" class="text-success" style="width: 16px;"></i>
                        <small class="text-muted fw-bold text-uppercase" style="font-size: 10px;">Supplier & Kode PO</small>
                    </div>
                    <h6 class="fw-bold mb-0 text-dark ps-4">{{ header.nama_suplier || '-' }}</h6>
                    <small class="text-muted ps-4" style="font-size: 11px;">{{ header.kode_po || '-' }}</small>
                </div>
            </div>

            <!-- Kode Supplier -->
            <div class="col-md-3">
                <div class="glass-panel p-3 bg-white bg-opacity-40 border-white h-100 shadow-sm transition-hover">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i data-lucide="tag" class="text-warning" style="width: 16px;"></i>
                        <small class="text-muted fw-bold text-uppercase" style="font-size: 10px;">Kode Supplier</small>
                    </div>
                    <h6 class="fw-bold mb-1 text-warning ps-4 font-mono">{{ header.kode_supplier || '—' }}</h6>
                </div>
            </div>

            <!-- Status -->
            <div class="col-md-3">
                <div class="glass-panel p-3 bg-white bg-opacity-40 border-white h-100 shadow-sm transition-hover">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i data-lucide="check-circle-2" class="text-info" style="width: 16px;"></i>
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
        <div v-if="summary" class="d-flex gap-2 flex-wrap mb-3">
            <span class="selisih-chip chip-ok">
                <i data-lucide="check-circle" style="width: 11px;"></i>
                Sesuai: {{ summary.sesuai }}
            </span>
            <span v-if="summary.lebih" class="selisih-chip chip-lebih">
                <i data-lucide="arrow-up" style="width: 11px;"></i>
                Lebih: {{ summary.lebih }}
            </span>
            <span v-if="summary.kurang" class="selisih-chip chip-kurang">
                <i data-lucide="arrow-down" style="width: 11px;"></i>
                Kurang: {{ summary.kurang }}
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
                                <i data-lucide="package" style="width: 14px;"></i> Barang
                            </div>
                        </th>
                        <!-- Surat Jalan -->
                        <th class="border-0 py-3 text-center" colspan="2">
                            <div class="d-flex align-items-center justify-content-center gap-2">
                                <i data-lucide="file-text" style="width: 14px;"></i> Surat Jalan
                            </div>
                        </th>
                        <!-- Aktual -->
                        <th class="border-0 py-3 text-center" colspan="2">
                            <div class="d-flex align-items-center justify-content-center gap-2">
                                <i data-lucide="package-check" style="width: 14px;"></i> Aktual Diterima
                            </div>
                        </th>
                        <th class="border-0 py-3 text-center pe-4">
                            <div class="d-flex align-items-center justify-content-center gap-2">
                                <i data-lucide="git-diff" style="width: 14px;"></i> Selisih
                            </div>
                        </th>
                    </tr>
                    <tr class="small text-uppercase" style="background:#2d2d2d">
                        <th class="border-0 py-2 ps-4" colspan="2"></th>
                        <th class="border-0 py-2 text-center text-white-50 fw-semibold" style="font-size:.7rem;">QTY</th>
                        <th class="border-0 py-2 text-center text-white-50 fw-semibold" style="font-size:.7rem;">SATUAN</th>
                        <th class="border-0 py-2 text-center text-white-50 fw-semibold" style="font-size:.7rem;">QTY</th>
                        <th class="border-0 py-2 text-center text-white-50 fw-semibold" style="font-size:.7rem;">SATUAN</th>
                        <th class="border-0 py-2 pe-4"></th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Loading -->
                    <tr v-if="loading">
                        <td colspan="7" class="text-center py-5">
                            <div class="spinner-border text-primary mb-2" role="status"></div>
                            <p class="text-muted small mb-0">Memuat data...</p>
                        </td>
                    </tr>
                    <!-- Empty -->
                    <tr v-else-if="!items.length && !loading">
                        <td colspan="7" class="text-center py-5">
                            <div class="d-flex flex-column align-items-center" style="opacity:.4">
                                <i data-lucide="inbox" style="width:48px;height:48px" class="mb-2"></i>
                                <p class="fw-bold mb-0">Tidak ada item</p>
                            </div>
                        </td>
                    </tr>
                    <!-- Rows -->
                    <tr v-else v-for="(item, idx) in items" :key="item.id"
                        class="border-bottom border-white border-opacity-50"
                        :class="rowHighlightClass(item)">
                        <td class="ps-4 text-muted small">{{ idx + 1 }}</td>
                        <td class="py-3">
                            <div class="fw-bold text-dark">{{ item.nama_barang }}</div>
                        </td>
                        <!-- QTY Dipesan -->
                        <td class="py-3 text-center">
                            <span class="badge bg-light text-dark border fw-bold px-2">{{ item.qty_dipesan }}</span>
                        </td>
                        <!-- Satuan PO -->
                        <td class="py-3 text-center">
                            <small class="text-muted fw-bold text-uppercase" style="font-size: 9px;">{{ item.nama_satuan || '-' }}</small>
                        </td>
                        <!-- QTY Diterima -->
                        <td class="py-3 text-center">
                            <span class="badge fw-bold px-2" :class="qtyAktualBadge(item)">{{ item.qty_diterima }}</span>
                        </td>
                        <!-- Satuan aktual (sama dengan PO) -->
                        <td class="py-3 text-center">
                            <small class="text-muted fw-bold text-uppercase" style="font-size: 9px;">{{ item.nama_satuan || '-' }}</small>
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

        <!-- ══ FOOTER ════════════════════════════════════════════════════════ -->
        <div class="row justify-content-end mt-4">
            <div class="col-md-5">
                <div class="glass-panel p-4 bg-white bg-opacity-60 border-white shadow-sm" style="border-radius: 20px;">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted fw-medium d-flex align-items-center gap-2">
                            <i data-lucide="box" style="width: 14px;"></i> Total Item
                        </span>
                        <span class="fw-bold text-dark">{{ items.length }} jenis barang</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted fw-medium d-flex align-items-center gap-2">
                            <i data-lucide="layers" style="width: 14px;"></i> Total Dipesan
                        </span>
                        <span class="fw-bold text-dark">{{ totalDipesan }} {{ satuanLabel }}</span>
                    </div>
                    <hr class="my-3 border-dark opacity-10">
                    <div class="d-flex justify-content-between mb-3">
                        <span class="fw-bold text-dark fs-5">Total Diterima</span>
                        <span class="h5 fw-bold text-primary mb-0">{{ totalDiterima }} {{ satuanLabel }}</span>
                    </div>

                    <div class="p-3 bg-light bg-opacity-50 rounded-3 border border-white">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted small d-flex align-items-center gap-2">
                                <i data-lucide="check-circle" style="width: 14px;"></i> Item Sesuai
                            </span>
                            <span class="text-success fw-bold">{{ summary ? summary.sesuai : 0 }}</span>
                        </div>
                        <div class="d-flex justify-content-between pt-2 border-top border-dashed border-secondary">
                            <span class="text-muted small fw-bold d-flex align-items-center gap-2">
                                <i data-lucide="alert-triangle" style="width: 14px;"></i> TOTAL SELISIH
                            </span>
                            <span class="fw-bold fs-5" :class="totalSelisih !== 0 ? 'text-danger' : 'text-success'">
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

    /* Row highlight */
    .row-kurang {
        background-color: rgba(254, 226, 226, .3) !important;
    }

    .row-lebih {
        background-color: rgba(219, 234, 254, .25) !important;
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
        animation: fadeIn 0.5s ease-out;
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
            totalDipesan() {
                return this.items.reduce((s, i) => s + i.qty_dipesan, 0);
            },
            totalDiterima() {
                return this.items.reduce((s, i) => s + i.qty_diterima, 0);
            },
            totalSelisih() {
                return this.items.reduce((s, i) => s + i.selisih, 0);
            },
            // Tampilkan satuan pertama yang ada (semua item dalam 1 PO umumnya 1 satuan)
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
                        `<?= base_url('laporan/penerimaan-gudang/get-detail') ?>/${this.id}`
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

            statusLabel(s) {
                return {
                    digudang: 'Di Gudang',
                    ditoko: 'Di Toko',
                    dibatalkan: 'Dibatalkan'
                } [s] || s;
            },
            statusBadgeClass(s) {
                return {
                    digudang: 'bg-success bg-opacity-10 text-success border border-success border-opacity-25',
                    ditoko: 'bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25',
                    dibatalkan: 'bg-danger  bg-opacity-10 text-danger  border border-danger  border-opacity-25',
                } [s] || 'bg-secondary bg-opacity-10 text-secondary';
            },
            rowHighlightClass(item) {
                if (item.selisih === 0) return '';
                return item.selisih > 0 ? 'row-lebih' : 'row-kurang';
            },
            qtyAktualBadge(item) {
                if (item.selisih === 0) return 'bg-success bg-opacity-10 text-success border border-success border-opacity-25';
                if (item.selisih > 0) return 'bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25';
                return 'bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25';
            },
            formatDate(d) {
                if (!d) return '-';
                const opts = {
                    day: '2-digit',
                    month: 'short',
                    year: '2-digit',
                    hour: '2-digit',
                    minute: '2-digit'
                };
                return new Date(d).toLocaleDateString('id-ID', opts).replace(/\./g, ':');
            },
            exportCSV() {
                if (!this.items.length) return;
                const headers = ['No', 'Nama Barang', 'Qty Dipesan', 'Qty Diterima', 'Satuan', 'Selisih'];
                const rows = this.items.map((item, i) => [
                    i + 1,
                    item.nama_barang,
                    item.qty_dipesan,
                    item.qty_diterima,
                    item.nama_satuan || '',
                    item.selisih,
                ]);
                const csv = [headers, ...rows]
                    .map(r => r.map(v => `"${v ?? ''}"`).join(','))
                    .join('\n');
                const kode = this.header?.kode_penerimaan ?? 'detail';
                const a = document.createElement('a');
                a.href = 'data:text/csv;charset=utf-8,' + encodeURIComponent('\uFEFF' + csv);
                a.download = `penerimaan_${kode}_${new Date().toISOString().slice(0,10)}.csv`;
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