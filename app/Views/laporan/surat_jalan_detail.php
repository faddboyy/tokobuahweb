<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<div class="container-fluid py-4 px-3" style="min-height: 100vh;">
    <div class="glass-panel p-4 d-flex flex-column border-0 shadow-lg position-relative overflow-hidden"
        id="appDetail" style="border-radius: 24px;">

        <!-- TOP BAR -->
        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-4 border-white border-opacity-20">
            <div class="d-flex align-items-center gap-3">
                <a href="<?= base_url('laporan/surat-jalan') ?>"
                    class="btn btn-icon btn-white shadow-sm rounded-circle p-2">
                    <i data-lucide="chevron-left" class="text-dark" style="width: 20px;"></i>
                </a>
                <div>
                    <h4 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                        <i data-lucide="truck" class="text-primary"></i>
                        Detail Surat Jalan
                    </h4>
                    <p class="text-muted small mb-0 mt-1 d-flex align-items-center gap-2" v-if="header">
                        <span class="badge bg-primary px-2 font-mono">{{ header.kode_po }}</span>
                        <i data-lucide="calendar" style="width: 12px;"></i> {{ formatDate(header.waktu_po) }}
                    </p>
                </div>
            </div>
            <div class="d-flex gap-2" v-if="header">
                <span class="badge rounded-pill px-3 py-2 fw-bold align-self-center"
                    style="font-size: 11px;" :class="statusBadgeClass(header.status)">
                    &#9679; {{ statusLabel(header.status) }}
                </span>
            </div>
        </div>

        <!-- LOADING -->
        <div v-if="loading" class="text-center py-5">
            <div class="spinner-border text-primary mb-3" style="width:3rem;height:3rem"></div>
            <p class="text-muted">Memuat detail surat jalan...</p>
        </div>

        <template v-else-if="header">

            <!-- INFO CARDS -->
            <div class="row g-3 mb-4">
                <!-- Supplier -->
                <div class="col-md-3">
                    <div class="glass-panel p-3 bg-white bg-opacity-40 border-white h-100 shadow-sm transition-hover">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <i data-lucide="building-2" class="text-primary" style="width: 16px;"></i>
                            <small class="text-muted fw-bold text-uppercase" style="font-size: 10px;">Supplier</small>
                        </div>
                        <h6 class="fw-bold mb-0 text-dark ps-4">{{ header.nama_suplier || '-' }}</h6>
                        <small class="text-muted ps-4 d-block" style="font-size: 11px;" v-if="header.telepon_suplier">
                            {{ header.telepon_suplier }}
                        </small>
                        <small class="text-muted ps-4 d-block" style="font-size: 11px;" v-if="header.alamat_suplier">
                            {{ header.alamat_suplier }}
                        </small>
                    </div>
                </div>

                <!-- Gudang & Operator -->
                <div class="col-md-3">
                    <div class="glass-panel p-3 bg-white bg-opacity-40 border-white h-100 shadow-sm transition-hover">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <i data-lucide="warehouse" class="text-success" style="width: 16px;"></i>
                            <small class="text-muted fw-bold text-uppercase" style="font-size: 10px;">Gudang & Operator</small>
                        </div>
                        <h6 class="fw-bold mb-0 text-dark ps-4">{{ header.nama_gudang || '-' }}</h6>
                        <small class="text-muted ps-4" style="font-size: 11px;">{{ header.nama_operator || '-' }}</small>
                    </div>
                </div>

                <!-- Status & Tanggal -->
                <div class="col-md-3">
                    <div class="glass-panel p-3 bg-white bg-opacity-40 border-white h-100 shadow-sm transition-hover">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <i data-lucide="check-circle-2" class="text-info" style="width: 16px;"></i>
                            <small class="text-muted fw-bold text-uppercase" style="font-size: 10px;">Status & Tanggal</small>
                        </div>
                        <div class="ps-4">
                            <span class="badge px-3 py-1 fw-bold"
                                :class="statusBadgeClass(header.status)"
                                style="font-size: 11px;">
                                {{ statusLabel(header.status) }}
                            </span>
                            <small class="d-block text-muted mt-1" style="font-size: 11px;">
                                {{ formatDate(header.waktu_po) }}
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Ringkasan Qty -->
                <div class="col-md-3">
                    <div class="glass-panel p-3 bg-primary bg-opacity-10 border-white h-100 shadow-sm">
                        <div class="d-flex align-items-center gap-2 mb-2 text-primary">
                            <i data-lucide="layers" style="width: 16px;"></i>
                            <small class="fw-bold text-uppercase" style="font-size: 10px;">Ringkasan Order</small>
                        </div>
                        <h5 class="fw-bold mb-0 text-primary ps-4">{{ totalQty }} unit</h5>
                        <small class="text-muted ps-4" style="font-size: 11px;">{{ items.length }} jenis barang</small>
                    </div>
                </div>
            </div>

            <!-- TABLE ITEMS -->
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
                                    <i data-lucide="hash" style="width: 14px;"></i> Qty
                                </div>
                            </th>
                            <th class="border-0 py-3 text-center">
                                <div class="d-flex align-items-center justify-content-center gap-2">
                                    <i data-lucide="ruler" style="width: 14px;"></i> Satuan
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="!items.length">
                            <td colspan="4" class="text-center py-5 text-muted">Tidak ada item</td>
                        </tr>
                        <tr v-else v-for="(item, idx) in items" :key="item.id"
                            class="border-bottom border-white border-opacity-50">
                            <td class="ps-4 py-3 text-muted small">{{ idx + 1 }}</td>
                            <td class="py-3">
                                <div class="fw-bold text-dark">{{ item.nama_barang }}</div>
                                <small class="text-muted font-mono" style="font-size:10px" v-if="item.barcode">
                                    {{ item.barcode }}
                                </small>
                            </td>
                            <td class="py-3 text-center">
                                <span class="badge bg-light text-dark border fw-bold px-2">{{ item.qty }}</span>
                            </td>
                            <td class="py-3 text-center">
                                <span class="badge bg-light text-dark border fw-bold">{{ item.nama_satuan || '-' }}</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- SUMMARY PANEL -->
            <div class="row justify-content-end mt-4">
                <div class="col-md-5">
                    <div class="glass-panel p-4 bg-white bg-opacity-60 border-white shadow-sm" style="border-radius: 20px;">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted fw-medium d-flex align-items-center gap-2">
                                <i data-lucide="list" style="width: 14px;"></i> Jumlah Jenis
                            </span>
                            <span class="fw-bold text-dark">{{ items.length }} barang</span>
                        </div>
                        <hr class="my-3 border-dark opacity-10">
                    </div>
                </div>
            </div>

        </template>

        <!-- NOT FOUND -->
        <div v-else class="text-center py-5">
            <i data-lucide="file-x" style="width:60px;height:60px;opacity:.2"></i>
            <h5 class="mt-3 text-muted">Data tidak ditemukan</h5>
            <a href="<?= base_url('laporan/surat-jalan') ?>" class="btn btn-primary mt-2 rounded-3">Kembali</a>
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

    /* Status badge: Pesanan (order) -> indigo */
    .badge-status-order {
        background: rgba(99, 102, 241, 0.12) !important;
        color: #4f46e5 !important;
        border: 1px solid rgba(99, 102, 241, 0.3) !important;
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
        z-index: 1;
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
                header: null,
                items: [],
                id: <?= (int) $id ?>,
            };
        },

        computed: {
            totalQty() {
                return this.items.reduce((s, i) => s + (parseInt(i.qty) || 0), 0);
            },
        },

        methods: {
            async loadDetail() {
                try {
                    const res = await axios.get(
                        `<?= base_url('laporan/get-surat-jalan-detail') ?>/${this.id}`
                    );
                    this.header = res.data.header;
                    this.items = res.data.items;
                } catch (e) {
                    this.header = null;
                } finally {
                    this.loading = false;
                    this.$nextTick(() => lucide.createIcons());
                }
            },

            statusLabel(s) {
                return {
                    order: 'Pesanan',
                    selesai: 'Selesai',
                    dibatalkan: 'Dibatalkan',
                } [s] || (s || '-');
            },

            statusBadgeClass(s) {
                return {
                    order: 'badge-status-order',
                    selesai: 'bg-primary  bg-opacity-10 text-primary  border border-primary  border-opacity-25',
                    dibatalkan: 'bg-danger   bg-opacity-10 text-danger   border border-danger   border-opacity-25',
                } [s] || 'bg-secondary bg-opacity-10 text-secondary';
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
        },

        mounted() {
            this.loadDetail();
            lucide.createIcons();
        },
        updated() {
            lucide.createIcons();
        },
    }).mount('#appDetail');
</script>
<?= $this->endSection() ?>