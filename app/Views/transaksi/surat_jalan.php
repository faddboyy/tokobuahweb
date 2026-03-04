<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<div id="app" v-cloak class="h-100 d-flex flex-column">

    <!-- TOAST CONTAINER -->
    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999;">
        <div v-for="(toast, index) in toasts" :key="toast.id"
            class="toast show align-items-center border-0 shadow-lg"
            :class="'toast-' + toast.type"
            role="alert">
            <div class="d-flex">
                <div class="toast-body d-flex align-items-center gap-2">
                    <i :data-lucide="toast.icon" style="width: 18px;"></i>
                    <span>{{ toast.message }}</span>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto"
                    @click="removeToast(index)"></button>
            </div>
        </div>
    </div>

    <div class="glass-panel p-4 flex-fill d-flex flex-column position-relative overflow-hidden">

        <!-- HEADER -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold mb-1 text-dark d-flex align-items-center gap-2">
                    <i data-lucide="truck" style="width: 28px;"></i>
                    Buat Pemesanan
                </h3>
                <p class="text-muted small mb-0">Purchase Order - Toko Buah</p>
            </div>

            <button v-if="!activeId"
                class="btn btn-primary btn-lg shadow-sm px-4 d-flex align-items-center gap-2 modern-btn"
                @click="openModalMulai">
                <i data-lucide="play-circle" style="width: 20px;"></i>
                Mulai Transaksi
            </button>

            <div v-else class="d-flex align-items-center gap-3">
                <div class="badge-modern" style="background: linear-gradient(135deg, #10b981, #059669); color: white;">
                    <i data-lucide="check-circle" style="width: 16px;"></i>
                    Pre Order ID #{{ activeId }}
                </div>
                <button class="btn btn-outline-danger btn-sm modern-btn" @click="confirmBatalkan">
                    <i data-lucide="x-circle" style="width: 16px;"></i>
                    Batalkan
                </button>
            </div>
        </div>

        <!-- LOCK OVERLAY -->
        <div v-if="!activeId" class="lock-overlay">
            <div class="text-center lock-content">
                <div class="lock-icon mb-3">
                    <i data-lucide="lock" style="width: 64px; height: 64px;"></i>
                </div>
                <h4 class="fw-bold mb-2">Transaksi Terkunci</h4>
                <p class="text-muted">Klik tombol "Mulai Transaksi" untuk memulai</p>
            </div>
        </div>

        <!-- MAIN CONTENT -->
        <div :class="{'blur-content': !activeId}" class="flex-fill d-flex flex-column">

            <!-- ROW 1: Supplier & Info -->
            <div class="row g-3 mb-3">
                <!-- Supplier Info (readonly setelah mulai) -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold small text-muted mb-2">
                        <i data-lucide="building-2" style="width: 14px;"></i> SUPPLIER
                    </label>
                    <div v-if="selectedSuplier" class="p-3 rounded suplier-card">
                        <div class="fw-semibold text-primary d-flex align-items-center gap-2">
                            <i data-lucide="check-circle-2" style="width: 16px;"></i>
                            {{ selectedSuplier.nama }}
                        </div>
                        <small class="text-muted">Supplier terpilih untuk surat jalan ini</small>
                    </div>
                    <div v-else class="p-3 rounded" style="background: #f8fafc; border: 2px dashed #e2e8f0;">
                        <small class="text-muted">Belum ada supplier dipilih</small>
                    </div>
                </div>

                <!-- Kode SJ Info -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold small text-muted mb-2">
                        <i data-lucide="file-text" style="width: 14px;"></i> INFO TRANSAKSI
                    </label>
                    <div v-if="activeId" class="p-3 rounded info-card">
                        <div class="d-flex justify-content-between">
                            <small class="text-muted">ID Surat Jalan</small>
                            <span class="fw-bold text-primary">#{{ activeId }}</span>
                        </div>
                    </div>
                    <div v-else class="p-3 rounded" style="background: #f8fafc; border: 2px dashed #e2e8f0;">
                        <small class="text-muted">Informasi transaksi akan muncul setelah dimulai</small>
                    </div>
                </div>
            </div>

            <!-- ROW 2: Add Item Form -->
            <div class="mb-3">
                <div class="add-item-card p-3">
                    <div class="row g-3 align-items-end">
                        <!-- Pilih Barang -->
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small text-muted mb-2">
                                <i data-lucide="package" style="width: 14px;"></i> BARANG
                            </label>
                            <div class="position-relative">
                                <input type="text"
                                    class="form-control form-control-modern"
                                    placeholder="Cari nama barang..."
                                    v-model="searchBarang"
                                    @input="searchBar"
                                    @focus="barangFocused = true"
                                    @blur="() => setTimeout(() => barangFocused = false, 200)"
                                    style="padding-left: 1rem;">

                                <div v-if="barangResults.length && barangFocused"
                                    class="dropdown-modern shadow-lg">
                                    <div v-for="b in barangResults" :key="b.id"
                                        @click="selectBarang(b)"
                                        class="dropdown-item-modern">
                                        <i data-lucide="package" style="width: 16px;"></i>
                                        <div>
                                            <div class="fw-semibold">{{ b.nama }}</div>
                                            <small class="text-muted">{{ b.barcode }}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div v-if="selectedBarang" class="mt-1">
                                <small class="text-success fw-semibold">
                                    <i data-lucide="check" style="width: 12px;"></i>
                                    {{ selectedBarang.nama }}
                                </small>
                            </div>
                        </div>

                        <!-- Satuan (pilih dari list, default satuan barang) -->
                        <div class="col-md-2">
                            <label class="form-label fw-semibold small text-muted mb-2">
                                <i data-lucide="ruler" style="width: 14px;"></i> SATUAN
                            </label>
                            <select class="form-select form-control-modern"
                                v-model="selectedSatuanId"
                                :disabled="!selectedBarang || satuanList.length === 0"
                                style="padding-left: 1rem;">
                                <option value="" disabled>- Pilih -</option>
                                <option v-for="s in satuanList" :key="s.id" :value="s.id">
                                    {{ s.nama }}
                                </option>
                            </select>
                        </div>

                        <!-- Qty -->
                        <div class="col-md-2">
                            <label class="form-label fw-semibold small text-muted mb-2">
                                <i data-lucide="hash" style="width: 14px;"></i> QTY
                            </label>
                            <input type="number"
                                class="form-control form-control-modern text-center fw-bold"
                                style="padding-left: 1rem;"
                                v-model.number="qtyInput"
                                min="1"
                                placeholder="0">
                        </div>

                        <!-- Total Harga Beli (sudah termasuk qty, borongan) -->
                        <div class="col-md-2">
                            <label class="form-label fw-semibold small text-muted mb-2">
                                <i data-lucide="tag" style="width: 14px;"></i> TOTAL HARGA BELI
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0 text-muted">Rp</span>
                                <input type="number"
                                    class="form-control form-control-modern border-start-0"
                                    style="padding-left: 0.5rem;"
                                    v-model.number="hargaInput"
                                    min="0"
                                    placeholder="0">
                            </div>
                        </div>

                        <!-- Tombol Tambah -->
                        <div class="col-md-2">
                            <button class="btn btn-primary w-100 modern-btn d-flex align-items-center justify-content-center gap-2"
                                @click="addItem"
                                :disabled="!selectedBarang || !selectedSatuanId || qtyInput < 1 || hargaInput < 1">
                                <i data-lucide="plus-circle" style="width: 18px;"></i>
                                Tambah
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ITEM TABLE -->
            <div class="card border-0 shadow-sm flex-fill d-flex flex-column" style="background: white;">
                <div class="card-header bg-transparent border-0 pt-3 pb-2 px-3">
                    <h6 class="fw-semibold mb-0 d-flex align-items-center gap-2">
                        <i data-lucide="list" style="width: 18px;"></i>
                        Daftar Barang
                        <span class="badge bg-primary rounded-pill ms-2">{{ items.length }}</span>
                    </h6>
                </div>
                <div class="card-body p-0 flex-fill overflow-auto">
                    <table class="table table-hover mb-0 modern-table">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th class="ps-3" width="40">#</th>
                                <th>Nama Barang</th>
                                <th width="220" class="text-center">Qty</th>
                                <th width="200" class="text-end pe-3">Total Harga Beli</th>
                                <th width="80" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="items.length == 0">
                                <td colspan="6" class="text-center py-5">
                                    <div class="empty-state">
                                        <i data-lucide="inbox" style="width: 48px; height: 48px; opacity: 0.3;"></i>
                                        <p class="text-muted mt-2 mb-0">Belum ada barang ditambahkan</p>
                                    </div>
                                </td>
                            </tr>
                            <tr v-for="(item, idx) in items" :key="item.id" class="cart-row">
                                <td class="ps-3 text-muted">{{ idx + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="product-icon-small">
                                            <i data-lucide="package" style="width: 14px;"></i>
                                        </div>
                                        <div>
                                            <div class="fw-semibold">{{ item.nama }}</div>
                                          
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center fw-semibold">{{ item.qty }} <span class="badge bg-light text-dark">{{ item.nama_satuan }}</span></td>
                                <td class="text-end pe-3 fw-bold text-primary">Rp {{ format(item.harga_beli) }}</td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-danger-soft"
                                        @click="confirmDelete(item.id)"
                                        title="Hapus item">
                                        <i data-lucide="trash-2" style="width: 14px;"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- FOOTER TOTAL -->
                <div class="card-footer bg-light border-0 p-3">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center gap-2">
                                <i data-lucide="info" style="width: 16px;" class="text-muted"></i>
                                <small class="text-muted">
                                    Total {{ items.length }} jenis barang dalam surat jalan
                                </small>
                            </div>
                        </div>
                        <div class="col-md-6 text-end">
                            <div class="d-flex justify-content-end align-items-center gap-3">
                                <div>
                                    <small class="text-muted d-block">TOTAL NOMINAL</small>
                                    <h4 class="mb-0 fw-bold text-primary">Rp {{ format(total) }}</h4>
                                </div>
                                <button class="btn btn-success btn-lg px-4 modern-btn"
                                    :disabled="items.length == 0"
                                    @click="confirmFinalisasi">
                                    <i data-lucide="check-circle" style="width: 18px;"></i>
                                    Selesaikan
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- MODAL MULAI TRANSAKSI -->
<div class="modal fade" id="modalMulai" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg modern-modal">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold d-flex align-items-center gap-2">
                    <i data-lucide="truck" style="width: 24px;"></i>
                    Mulai Surat Jalan Baru
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Pilih Supplier <span class="text-danger">*</span></label>
                    <select class="form-select form-control-modern" v-model="suplierIdInput"
                        style="padding-left: 1rem;">
                        <option value="">-- Pilih Supplier --</option>
                        <option v-for="s in suplierList" :key="s.id" :value="s.id">
                            {{ s.nama }}
                        </option>
                    </select>
                    <small class="text-muted">Supplier tidak dapat diubah setelah transaksi dimulai</small>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <button type="button"
                    class="btn btn-primary px-4 modern-btn"
                    :disabled="!suplierIdInput"
                    @click="mulaiTransaksi">
                    <i data-lucide="play-circle" style="width: 18px;"></i>
                    Mulai Transaksi
                </button>
            </div>
        </div>
    </div>
</div>

<!-- CONFIRMATION DIALOG -->
<div class="modal fade" id="confirmDialog" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg modern-modal">
            <div class="modal-body text-center p-4">
                <div class="mb-3">
                    <i :data-lucide="confirmIcon" :style="'width: 48px; height: 48px; color: ' + confirmColor + ';'"></i>
                </div>
                <h5 class="fw-bold mb-2">{{ confirmTitle }}</h5>
                <p class="text-muted mb-4">{{ confirmMessage }}</p>
                <div class="d-flex gap-2">
                    <button class="btn btn-light flex-fill" data-bs-dismiss="modal">Batal</button>
                    <button class="btn flex-fill" :class="confirmBtnClass" @click="confirmAction" data-bs-dismiss="modal">
                        {{ confirmBtnText }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Toast Notifications */
    .toast {
        min-width: 300px;
        border-radius: 12px;
        animation: slideInRight 0.3s ease;
    }

    .toast-success {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
    }

    .toast-error {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: white;
    }

    .toast-info {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
    }

    .toast-warning {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: white;
    }

    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }

        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    /* Glass Panel */
    .glass-panel {
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(20px) saturate(180%);
        border: 1px solid rgba(255, 255, 255, 0.5);
        border-radius: 16px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
    }

    /* Modern Buttons */
    .modern-btn {
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .modern-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
    }

    .modern-btn:active {
        transform: translateY(0);
    }

    /* Badge Modern */
    .badge-modern {
        padding: 0.6rem 1.2rem;
        border-radius: 10px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    /* Lock Overlay */
    .lock-overlay {
        position: absolute;
        inset: 0;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 100;
        border-radius: 16px;
    }

    .lock-content {
        animation: fadeInUp 0.5s ease;
    }

    .lock-icon {
        color: #94a3b8;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .blur-content {
        pointer-events: none;
        opacity: 0.5;
        filter: blur(2px);
    }

    /* Form Controls */
    .form-control-modern {
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        padding: 0.65rem 1rem;
        transition: all 0.2s;
        font-size: 0.95rem;
    }

    .form-control-modern:focus {
        border-color: var(--win-accent, #0067C0);
        box-shadow: 0 0 0 4px rgba(0, 103, 192, 0.1);
    }

    /* Add Item Card */
    .add-item-card {
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.06) 0%, rgba(118, 75, 162, 0.06) 100%);
        border-radius: 12px;
        border: 2px solid #e2e8f0;
    }

    /* Supplier Card */
    .suplier-card {
        background: rgba(0, 103, 192, 0.08);
        border: 2px solid rgba(0, 103, 192, 0.2);
    }

    /* Info Card */
    .info-card {
        background: rgba(16, 185, 129, 0.08);
        border: 2px solid rgba(16, 185, 129, 0.2);
        border-radius: 10px;
    }

    /* Dropdown Modern */
    .dropdown-modern {
        position: absolute;
        width: 100%;
        background: white;
        border-radius: 12px;
        margin-top: 8px;
        max-height: 300px;
        overflow-y: auto;
        z-index: 1000;
        border: 2px solid #e2e8f0;
    }

    .dropdown-item-modern {
        padding: 0.75rem 1rem;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        gap: 10px;
        border-bottom: 1px solid #f1f5f9;
    }

    .dropdown-item-modern:hover {
        background: linear-gradient(90deg, rgba(0, 103, 192, 0.05) 0%, transparent 100%);
        color: var(--win-accent, #0067C0);
    }

    .dropdown-item-modern:last-child {
        border-bottom: none;
    }

    /* Product Icon */
    .product-icon-small {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
    }

    /* Modern Table */
    .modern-table {
        font-size: 0.9rem;
    }

    .modern-table thead th {
        font-weight: 700;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #64748b;
        border-bottom: 2px solid #e2e8f0;
        padding: 0.75rem;
    }

    .modern-table tbody tr {
        transition: all 0.2s;
    }

    .modern-table tbody tr:hover {
        background: rgba(0, 103, 192, 0.03);
    }

    .modern-table tbody td {
        padding: 1rem 0.75rem;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
    }

    /* Inputs in table */
    .qty-input,
    .harga-input {
        border-radius: 8px;
        border: 2px solid #e2e8f0;
        font-weight: 600;
    }

    .qty-input:focus,
    .harga-input:focus {
        border-color: var(--win-accent, #0067C0);
        box-shadow: 0 0 0 3px rgba(0, 103, 192, 0.1);
    }

    /* Btn danger soft */
    .btn-danger-soft {
        background: rgba(239, 68, 68, 0.1);
        color: #dc2626;
        border: none;
        border-radius: 8px;
        transition: all 0.2s;
    }

    .btn-danger-soft:hover {
        background: #dc2626;
        color: white;
        transform: scale(1.05);
    }

    /* Empty State */
    .empty-state {
        padding: 2rem;
    }

    .empty-state i {
        color: #cbd5e1;
    }

    /* Modern Modal */
    .modern-modal {
        border-radius: 16px;
        overflow: hidden;
    }

    .modern-modal .modal-header {
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        padding: 1.5rem;
    }

    .modern-modal .modal-body {
        background: white;
    }

    .modern-modal .modal-footer {
        background: #f8fafc;
        padding: 1rem 1.5rem;
    }

    /* Scrollbar */
    ::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }

    ::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 10px;
    }

    ::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }

    ::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }

    [v-cloak] {
        display: none;
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
                activeId: <?= session()->get('active_surat_jalan_id') ?? 'null' ?>,

                // Supplier
                suplierList: <?= json_encode($supliers) ?>,
                suplierIdInput: '',
                selectedSuplier: null,

                // Info
                gudangNama: '<?= session()->get('gudang_nama') ?? '-' ?>',
                operatorNama: '<?= session()->get('user_name') ?? '-' ?>',

                // Barang Search
                searchBarang: '',
                barangResults: [],
                barangFocused: false,
                selectedBarang: null, // { id, nama, satuan_id, nama_satuan }
                satuanList: [], // semua satuan dari /barang/satuan/list
                selectedSatuanId: '', // satuan yang dipilih (default = barang.satuan_id)

                // Input
                qtyInput: 1,
                hargaInput: 0,

                // Items
                items: [],
                total: 0,

                // Toast
                toasts: [],
                toastId: 0,

                // Confirm
                confirmTitle: 'Konfirmasi',
                confirmMessage: '',
                confirmIcon: 'alert-triangle',
                confirmColor: '#f59e0b',
                confirmBtnClass: 'btn-danger',
                confirmBtnText: 'Ya',
                confirmCallback: null,

                modalMulai: null,
                modalConfirm: null
            }
        },

        methods: {
            // ========== TOAST ==========
            showToast(message, type = 'info') {
                const icons = {
                    success: 'check-circle',
                    error: 'x-circle',
                    info: 'info',
                    warning: 'alert-triangle'
                };
                this.toasts.push({
                    id: this.toastId++,
                    message,
                    type,
                    icon: icons[type]
                });
                setTimeout(() => this.toasts.shift(), 4000);
                this.$nextTick(() => lucide.createIcons());
            },
            removeToast(index) {
                this.toasts.splice(index, 1);
            },

            // ========== CONFIRM ==========
            confirmDelete(id) {
                this.confirmTitle = 'Hapus Item';
                this.confirmMessage = 'Apakah Anda yakin ingin menghapus item ini?';
                this.confirmIcon = 'alert-triangle';
                this.confirmColor = '#f59e0b';
                this.confirmBtnClass = 'btn-danger';
                this.confirmBtnText = 'Ya, Hapus';
                this.confirmCallback = () => this.deleteItem(id);
                this.modalConfirm.show();
            },
            confirmFinalisasi() {
                this.confirmTitle = 'Selesaikan Surat Jalan';
                this.confirmMessage = 'Surat jalan akan diselesaikan. Lanjutkan?';
                this.confirmIcon = 'check-circle';
                this.confirmColor = '#10b981';
                this.confirmBtnClass = 'btn-success';
                this.confirmBtnText = 'Ya, Selesaikan';
                this.confirmCallback = () => this.finalisasi();
                this.modalConfirm.show();
            },
            confirmBatalkan() {
                this.confirmTitle = 'Batalkan Transaksi';
                this.confirmMessage = 'Transaksi akan dibatalkan. Semua item yang sudah ditambahkan tidak akan diproses.';
                this.confirmIcon = 'x-circle';
                this.confirmColor = '#ef4444';
                this.confirmBtnClass = 'btn-danger';
                this.confirmBtnText = 'Ya, Batalkan';
                this.confirmCallback = () => this.batalkan();
                this.modalConfirm.show();
            },
            confirmAction() {
                if (this.confirmCallback) {
                    this.confirmCallback();
                    this.confirmCallback = null;
                }
            },

            // ========== MODAL MULAI ==========
            openModalMulai() {
                this.suplierIdInput = '';
                this.modalMulai.show();
            },

            // ========== MULAI TRANSAKSI ==========
            async mulaiTransaksi() {
                try {
                    const res = await axios.post('<?= base_url('suratjalan/mulai') ?>', {
                        suplier_id: this.suplierIdInput
                    });
                    this.activeId = res.data.surat_jalan_id;
                    this.selectedSuplier = this.suplierList.find(s => s.id == this.suplierIdInput) || null;
                    this.modalMulai.hide();
                    this.showToast('Transaksi surat jalan dimulai!', 'success');
                    this.loadItems();
                } catch (err) {
                    this.showToast(err.response?.data?.message || 'Gagal memulai transaksi', 'error');
                }
            },

            // ========== SEARCH BARANG ==========
            async searchBar() {
                if (this.searchBarang.length < 2) {
                    this.barangResults = [];
                    return;
                }
                try {
                    const res = await axios.get('<?= base_url('suratjalan/search-barang') ?>?q=' + this.searchBarang);
                    this.barangResults = res.data;
                    this.$nextTick(() => lucide.createIcons());
                } catch (err) {
                    console.error(err);
                }
            },

            selectBarang(b) {
                this.selectedBarang = b;
                this.searchBarang = b.nama;
                this.barangResults = [];
                this.barangFocused = false;
                this.selectedSatuanId = b.satuan_id; // default ke satuan barang
                this.loadSatuanList();
            },

            // ========== LOAD SEMUA SATUAN ==========
            async loadSatuanList() {
                try {
                    const res = await axios.get('<?= base_url('suratjalan/satuan-list') ?>');
                    this.satuanList = res.data;
                } catch (err) {
                    console.error('Gagal load satuan:', err);
                }
            },

            // ========== ADD ITEM ==========
            async addItem() {
                if (!this.selectedBarang || !this.selectedSatuanId || this.qtyInput < 1 || this.hargaInput < 1) {
                    this.showToast('Lengkapi semua field terlebih dahulu', 'warning');
                    return;
                }
                try {
                    await axios.post('<?= base_url('suratjalan/add-item') ?>', {
                        barang_id: this.selectedBarang.id,
                        satuan_id: this.selectedSatuanId,
                        qty: this.qtyInput,
                        harga_beli: this.hargaInput
                    });
                    // Reset form
                    this.searchBarang = '';
                    this.selectedBarang = null;
                    this.selectedSatuanId = '';
                    this.qtyInput = 1;
                    this.hargaInput = 0;
                    this.showToast('Barang ditambahkan', 'success');
                    this.loadItems();
                } catch (err) {
                    this.showToast(err.response?.data?.message || 'Gagal menambah item', 'error');
                }
            },

            // ========== LOAD ITEMS ==========
            async loadItems() {
                try {
                    const res = await axios.get('<?= base_url('suratjalan/detail') ?>');
                    this.items = res.data.items;
                    this.total = res.data.total;
                    this.$nextTick(() => lucide.createIcons());
                } catch (err) {
                    console.error(err);
                }
            },

            // ========== UPDATE ITEM ==========
            async updateItem(id, qty, harga_beli) {
                if (qty < 1) {
                    this.showToast('Qty minimal 1', 'warning');
                    this.loadItems();
                    return;
                }
                try {
                    const res = await axios.post(`<?= base_url('suratjalan/update-item') ?>/${id}`, {
                        qty,
                        harga_beli
                    });
                    this.total = res.data.total;
                    this.showToast('Item diupdate', 'success');
                    this.loadItems();
                } catch (err) {
                    this.showToast(err.response?.data?.message || 'Gagal update item', 'error');
                    this.loadItems();
                }
            },

            // ========== DELETE ITEM ==========
            async deleteItem(id) {
                try {
                    const res = await axios.post(`<?= base_url('suratjalan/delete-item') ?>/${id}`);
                    this.total = res.data.total;
                    this.showToast('Item dihapus', 'success');
                    this.loadItems();
                } catch (err) {
                    this.showToast(err.response?.data?.message || 'Gagal hapus item', 'error');
                }
            },

            // ========== FINALISASI ==========
            async finalisasi() {
                try {
                    const res = await axios.post('<?= base_url('suratjalan/finalisasi') ?>');
                    this.showToast('Surat jalan diselesaikan!', 'success');
                    // Reset state
                    this.activeId = null;
                    this.items = [];
                    this.total = 0;
                    this.selectedSuplier = null;
                    if (res.data.redirect) {
                        setTimeout(() => window.open(res.data.redirect, '_blank'), 1000);
                    }
                } catch (err) {
                    this.showToast(err.response?.data?.message || 'Gagal finalisasi', 'error');
                }
            },

            // ========== BATALKAN ==========
            async batalkan() {
                if (!this.activeId) return;
                try {
                    await axios.post(`<?= base_url('suratjalan/batalkan') ?>/${this.activeId}`);
                    this.showToast('Transaksi dibatalkan', 'warning');
                    this.activeId = null;
                    this.items = [];
                    this.total = 0;
                    this.selectedSuplier = null;
                } catch (err) {
                    this.showToast(err.response?.data?.message || 'Gagal membatalkan', 'error');
                }
            },

            // ========== HELPER ==========
            format(x) {
                return new Intl.NumberFormat('id-ID').format(x ?? 0);
            }
        },

        mounted() {
            this.modalMulai = new bootstrap.Modal(document.getElementById('modalMulai'));
            this.modalConfirm = new bootstrap.Modal(document.getElementById('confirmDialog'));

            // Load satuan list sekali saat halaman dibuka
            this.loadSatuanList();

            if (this.activeId) {
                // Restore supplier dari session jika ada
                const sid = <?= session()->get('active_surat_jalan_suplier_id') ?? 'null' ?>;
                this.selectedSuplier = this.suplierList.find(s => s.id == sid) || null;
                this.loadItems();
            }

            lucide.createIcons();
        }
    }).mount('#app');
</script>
<?= $this->endSection() ?>