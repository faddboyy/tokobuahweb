<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid py-4 px-3" style="min-height: 100vh;">
    <div class="glass-panel p-4 d-flex flex-column border-0 shadow-lg position-relative overflow-hidden" id="appDetail" style="border-radius: 24px;">

        <!-- ── HEADER ── -->
        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-4 border-white border-opacity-20">
            <div class="d-flex align-items-center gap-3">
                <a href="<?= base_url('laporan/penjualan') ?>" class="btn btn-icon btn-white shadow-sm rounded-circle p-2">
                    <i data-lucide="chevron-left" class="text-dark" style="width: 20px;"></i>
                </a>
                <div>
                    <h4 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                        <i data-lucide="receipt-text" class="text-primary"></i>
                        Detail Transaksi
                    </h4>
                    <p class="text-muted small mb-0 mt-1 d-flex align-items-center gap-2">
                        <span class="badge bg-primary px-2">{{ header.faktur }}</span>
                        <i data-lucide="calendar" style="width: 12px;"></i> {{ formatDate(header.created_at) }}
                    </p>
                </div>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-dark shadow-sm px-4 d-flex align-items-center gap-2 rounded-pill" @click="cetak(header.id)">
                    <i data-lucide="printer" style="width: 18px;"></i>
                    Cetak Struk
                </button>
            </div>
        </div>

        <!-- ── INFO CARDS ── -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="glass-panel p-3 bg-white bg-opacity-40 border-white h-100 shadow-sm transition-hover">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i data-lucide="user-round" class="text-primary" style="width: 16px;"></i>
                        <small class="text-muted fw-bold text-uppercase" style="font-size: 10px;">Operator & Cabang</small>
                    </div>
                    <h6 class="fw-bold mb-0 text-dark ps-4">{{ header.nama_cabang }}</h6>
                    <small class="text-muted ps-4" style="font-size: 11px;">{{ header.operator }}</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="glass-panel p-3 bg-white bg-opacity-40 border-white h-100 shadow-sm transition-hover">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i data-lucide="credit-card" class="text-success" style="width: 16px;"></i>
                        <small class="text-muted fw-bold text-uppercase" style="font-size: 10px;">Customer & Metode Bayar</small>
                    </div>
                    <h6 class="fw-bold mb-0 text-dark ps-4 text-capitalize">{{ header.jenis_pembayaran }}</h6>
                    <small class="text-muted ps-4" style="font-size: 11px;">{{ header.nama_customer || 'Customer Umum' }}</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="glass-panel p-3 bg-white bg-opacity-40 border-white h-100 shadow-sm transition-hover">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i data-lucide="check-circle-2" class="text-info" style="width: 16px;"></i>
                        <small class="text-muted fw-bold text-uppercase" style="font-size: 10px;">Status</small>
                    </div>
                    <div class="ps-4">
                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3">Selesai</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="glass-panel p-3 bg-primary bg-opacity-10 border-white h-100 shadow-sm">
                    <div class="d-flex align-items-center gap-2 mb-2 text-primary">
                        <i data-lucide="banknote" style="width: 16px;"></i>
                        <small class="fw-bold text-uppercase" style="font-size: 10px;">Total Akhir</small>
                    </div>
                    <h5 class="fw-bold mb-0 text-primary ps-4">Rp {{ formatNumber(summary.total_akhir) }}</h5>
                    <!-- Badge hemat jika ada diskon -->
                    <div v-if="summary.total_semua_diskon > 0" class="ps-4 mt-1">
                        <small class="badge bg-white bg-opacity-75 text-dark border border-secondary border-opacity-25" style="font-size: 10px;">
                            <i data-lucide="zap" style="width: 10px;"></i>
                            Hemat Rp {{ formatNumber(summary.total_semua_diskon) }}
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- ── TABEL ITEM ── -->
        <div class="table-responsive flex-fill custom-scroll border rounded-4 bg-white bg-opacity-30 shadow-sm">
            <table class="table align-middle border-0 mb-0">
                <thead class="bg-dark text-white shadow-sm">
                    <tr class="small text-uppercase">
                        <th class="ps-4 border-0 py-3">
                            <div class="d-flex align-items-center gap-2">
                                <i data-lucide="package" style="width: 14px;"></i> Barang
                            </div>
                        </th>
                        <th class="border-0 py-3">
                            <div class="d-flex align-items-center gap-2">
                                <i data-lucide="tags" style="width: 14px;"></i> Harga
                            </div>
                        </th>
                        <th class="border-0 py-3 text-center">
                            <div class="d-flex align-items-center justify-content-center gap-2">
                                <i data-lucide="hash" style="width: 14px;"></i> Qty
                            </div>
                        </th>
                        <th class="border-0 py-3 text-end pe-4">
                            <div class="d-flex align-items-center justify-content-end gap-2">
                                <i data-lucide="wallet" style="width: 14px;"></i> Subtotal
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(item, index) in items" :key="item.id"
                        class="border-bottom border-white border-opacity-50"
                        :class="{ 'row-promo': item.ada_diskon }">

                        <!-- Nama barang + badge promo -->
                        <td class="ps-4 py-3">
                            <div class="fw-bold text-dark d-flex align-items-center gap-2">
                                {{ item.nama_barang }}
                                <span v-if="item.ada_diskon"
                                    class="badge bg-white text-dark border border-secondary border-opacity-25"
                                    style="font-size: 9px;">
                                    <i data-lucide="tag" style="width: 9px;"></i> PROMO
                                </span>
                            </div>
                        </td>

                        <!-- Harga: normal atau 3-baris diskon -->
                        <td class="py-3">
                            <div v-if="item.ada_diskon">
                                <!-- Harga asli coret -->
                                <div class="text-muted" style="font-size: 11px; text-decoration: line-through;">
                                    Rp {{ formatNumber(item.harga_satuan) }}
                                </div>
                                <!-- Potongan -->
                                <div class="text-danger" style="font-size: 11px;">
                                    <i data-lucide="minus-circle" style="width: 10px;"></i>
                                    Rp {{ formatNumber(item.nominal_diskon) }}/{{ item.satuan }}
                                </div>
                                <!-- Harga efektif -->
                                <div class="fw-bold text-success" style="font-size: 13px;">
                                    Rp {{ formatNumber(item.harga_setelah_diskon) }}
                                </div>
                            </div>
                            <div v-else>
                                Rp {{ formatNumber(item.harga_satuan) }}
                            </div>
                        </td>

                        <!-- Qty -->
                        <td class="py-3 text-center">
                            <div class="d-flex align-items-center justify-content-center gap-1">
                                <span class="badge bg-light text-dark border fw-bold">{{ item.qty }}</span>
                                <small class="text-muted fw-bold text-uppercase" style="font-size: 9px;">{{ item.satuan }}</small>
                            </div>
                        </td>

                        <!-- Subtotal: normal atau 2-baris diskon -->
                        <td class="py-3 text-end pe-4">
                            <div v-if="item.ada_diskon">
                                <div class="text-muted" style="font-size: 11px; text-decoration: line-through;">
                                    Rp {{ formatNumber(item.subtotal_kotor) }}
                                </div>
                                <div class="fw-bold text-dark">
                                    Rp {{ formatNumber(item.subtotal) }}
                                </div>
                                <div style="font-size: 10px;" class="text-danger">
                                    hemat Rp {{ formatNumber(item.total_diskon_item) }}
                                </div>
                            </div>
                            <div v-else class="fw-bold text-dark">
                                Rp {{ formatNumber(item.subtotal) }}
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- ── RINGKASAN HARGA ── -->
        <div class="row justify-content-end mt-4">
            <div class="col-md-5">
                <div class="glass-panel p-4 bg-white bg-opacity-60 border-white shadow-sm" style="border-radius: 20px;">

                    <!-- Subtotal kotor (sebelum semua diskon) -->
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted fw-medium d-flex align-items-center gap-2">
                            <i data-lucide="calculator" style="width: 14px;"></i> Subtotal
                        </span>
                        <span class="fw-bold text-dark">Rp {{ formatNumber(summary.subtotal_kotor) }}</span>
                    </div>

                    <!-- Diskon promo (dari item) — tampil jika ada -->
                    <div v-if="summary.diskon_promo > 0" class="d-flex justify-content-between mb-2">
                        <span class="text-muted fw-medium d-flex align-items-center gap-2">
                            <i data-lucide="tag" style="width: 14px;"></i> Diskon Promo
                        </span>
                        <span class="fw-bold text-danger">- Rp {{ formatNumber(summary.diskon_promo) }}</span>
                    </div>

                    <!-- Diskon tambahan kasir — tampil jika ada -->
                    <div v-if="summary.diskon_tambahan > 0" class="d-flex justify-content-between mb-2">
                        <span class="text-muted fw-medium d-flex align-items-center gap-2">
                            <i data-lucide="percent" style="width: 14px;"></i> Diskon Tambahan
                        </span>
                        <span class="fw-bold text-danger">- Rp {{ formatNumber(summary.diskon_tambahan) }}</span>
                    </div>

                    <!-- Total diskon gabungan — tampil jika keduanya ada -->
                    <div v-if="summary.diskon_promo > 0 && summary.diskon_tambahan > 0"
                        class="d-flex justify-content-between mb-2 pb-2 border-bottom border-dashed">
                        <span class="text-danger fw-bold d-flex align-items-center gap-2">
                            <i data-lucide="receipt" style="width: 14px;"></i> Total Diskon
                        </span>
                        <span class="fw-bold text-danger">- Rp {{ formatNumber(summary.total_semua_diskon) }}</span>
                    </div>

                    <hr class="my-3 border-dark opacity-10">

                    <!-- Total akhir -->
                    <div class="d-flex justify-content-between mb-3">
                        <span class="fw-bold text-dark fs-5">Total Akhir</span>
                        <span class="h5 fw-bold text-primary mb-0">Rp {{ formatNumber(summary.total_akhir) }}</span>
                    </div>

                    <!-- Bayar & kembalian -->
                    <div class="p-3 bg-light bg-opacity-50 rounded-3 border border-white">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted small d-flex align-items-center gap-2">
                                <i data-lucide="coins" style="width: 14px;"></i>
                                {{ header.jenis_pembayaran ? header.jenis_pembayaran.charAt(0).toUpperCase() + header.jenis_pembayaran.slice(1) : 'Bayar' }}
                            </span>
                            <span class="text-dark fw-bold">Rp {{ formatNumber(header.nominal_bayar) }}</span>
                        </div>
                        <div class="d-flex justify-content-between pt-2 border-top border-dashed border-secondary">
                            <span class="text-muted small fw-bold d-flex align-items-center gap-2">
                                <i data-lucide="hand-coins" style="width: 14px;"></i> KEMBALIAN
                            </span>
                            <span class="text-success fw-bold fs-5">Rp {{ formatNumber(header.kembalian) }}</span>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>

<style>
    #appDetail {
        margin-top: 0;
        z-index: 1;
    }

    .glass-panel {
        backdrop-filter: blur(15px);
        -webkit-backdrop-filter: blur(15px);
    }

    .border-dashed {
        border-style: dashed !important;
    }

    .custom-scroll {
        max-height: 400px;
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

    /* Baris item yang kena promo — highlight lembut */
    .row-promo {
        background: rgba(255, 193, 7, 0.04);
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
                header: {},
                items: [],
                summary: {
                    subtotal_kotor: 0,
                    diskon_promo: 0,
                    diskon_tambahan: 0,
                    total_semua_diskon: 0,
                    total_akhir: 0,
                }
            }
        },

        mounted() {
            this.loadDetail();
            lucide.createIcons();
        },

        methods: {
            loadDetail() {
                axios.get('<?= base_url('laporan/get_detail/' . $id) ?>').then(res => {
                    this.header = res.data.header;
                    this.items = res.data.items;
                    this.summary = res.data.summary;
                    this.$nextTick(() => {
                        lucide.createIcons();
                    });
                });
            },

            formatNumber(num) {
                return num ? new Intl.NumberFormat('id-ID').format(num) : 0;
            },

            formatDate(dateString) {
                if (!dateString) return '-';
                const date = new Date(dateString);
                return date.toLocaleDateString('id-ID', {
                    day: '2-digit',
                    month: 'short',
                    year: '2-digit',
                    hour: '2-digit',
                    minute: '2-digit'
                }).replace(/\./g, ':');
            },

            cetak(id) {
                if (!id) return;
                window.open('<?= base_url('penjualan/cetak') ?>/' + id, '_blank');
            }
        }
    }).mount('#appDetail');
</script>
<?= $this->endSection() ?>