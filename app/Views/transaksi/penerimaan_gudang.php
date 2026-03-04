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
                    <span v-html="toastIcon(toast.type)"></span>
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
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 16h6" />
                        <path d="M19 13v6" />
                        <path d="M21 10V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l2-1.14" />
                        <path d="M16.5 9.4 7.55 4.24" />
                        <polyline points="3.29 7 12 12 20.71 7" />
                        <line x1="12" y1="22" x2="12" y2="12" />
                    </svg>
                    Penerimaan Gudang
                </h3>
                <p class="text-muted small mb-0">Scan kode PO lalu verifikasi barang yang diterima</p>
            </div>

            <!-- Belum scan: tampilkan input PO -->
            <div v-if="!suratJalan">
                <div class="d-flex align-items-center gap-2">
                    <input
                        id="inputKodePo"
                        type="text"
                        class="form-control form-control-modern"
                        style="width: 240px;"
                        placeholder="Scan / ketik kode PO..."
                        v-model="kodePo"
                        @keyup.enter="scanPo"
                        :disabled="loading"
                        autocomplete="off">
                    <button
                        class="btn btn-primary btn-lg shadow-sm px-4 d-flex align-items-center gap-2 modern-btn"
                        @click="scanPo"
                        :disabled="!kodePo || loading">
                        <span v-if="loading" class="spinner-border spinner-border-sm"></span>
                        <svg v-else xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 7V5a2 2 0 0 1 2-2h2" />
                            <path d="M17 3h2a2 2 0 0 1 2 2v2" />
                            <path d="M21 17v2a2 2 0 0 1-2 2h-2" />
                            <path d="M7 21H5a2 2 0 0 1-2-2v-2" />
                            <rect width="7" height="5" x="7" y="7" rx="1" />
                            <rect width="7" height="5" x="10" y="12" rx="1" />
                        </svg>
                        Scan PO
                    </button>
                </div>
            </div>

            <!-- Sudah scan: badge + ganti PO -->
            <div v-else class="d-flex align-items-center gap-3">
             
                <button class="btn btn-outline-danger btn-sm modern-btn d-flex align-items-center gap-1" @click="reset">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10" />
                        <line x1="15" y1="9" x2="9" y2="15" />
                        <line x1="9" y1="9" x2="15" y2="15" />
                    </svg>
                    Ganti PO
                </button>
            </div>
        </div>

        <!-- LOCK OVERLAY: muncul saat belum scan -->
        <div v-if="!suratJalan" class="lock-overlay">
            <div class="text-center lock-content">
                <div class="lock-icon mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 7V5a2 2 0 0 1 2-2h2" />
                        <path d="M17 3h2a2 2 0 0 1 2 2v2" />
                        <path d="M21 17v2a2 2 0 0 1-2 2h-2" />
                        <path d="M7 21H5a2 2 0 0 1-2-2v-2" />
                        <rect width="7" height="5" x="7" y="7" rx="1" />
                        <rect width="7" height="5" x="10" y="12" rx="1" />
                    </svg>
                </div>
                <h4 class="fw-bold mb-2">Scan Kode PO</h4>
                <p class="text-muted">Ketik atau scan kode PO di atas untuk memulai penerimaan</p>
                <div v-if="scanError"
                    class="alert alert-danger d-inline-flex align-items-center gap-2 mt-2 py-2 px-3 rounded-3"
                    style="font-size:.83rem">
                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0">
                        <circle cx="12" cy="12" r="10" />
                        <line x1="12" y1="8" x2="12" y2="12" />
                        <line x1="12" y1="16" x2="12.01" y2="16" />
                    </svg>
                    {{ scanError }}
                </div>
            </div>
        </div>

        <!-- MAIN CONTENT (blur saat belum scan) -->
        <div :class="{'blur-content': !suratJalan}" class="flex-fill d-flex flex-column">

            <!-- ROW 1: Info PO + Kode Supplier -->
            <div class="row g-3 mb-3">

                <!-- Info PO -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold small text-muted mb-2 d-flex align-items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                            <polyline points="14 2 14 8 20 8" />
                            <line x1="16" y1="13" x2="8" y2="13" />
                            <line x1="16" y1="17" x2="8" y2="17" />
                            <polyline points="10 9 9 9 8 9" />
                        </svg>
                        INFO SURAT JALAN
                    </label>
                    <div v-if="suratJalan" class="p-3 rounded suplier-card">
                        <div class="fw-semibold text-primary d-flex align-items-center gap-2 mb-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z" />
                                <path d="m9 12 2 2 4-4" />
                            </svg>
                            {{ suratJalan.kode_po }}
                        </div>
                        <div class="d-flex flex-wrap gap-3" style="font-size:.82rem">
                            <div>
                                <span class="text-muted">Supplier:</span>
                                <span class="fw-semibold ms-1">{{ suratJalan.nama_suplier || '-' }}</span>
                            </div>
                            <div>
                                <span class="text-muted">Gudang:</span>
                                <span class="fw-semibold ms-1">{{ suratJalan.nama_gudang || '-' }}</span>
                            </div>
                            <div>
                                <span class="text-muted">Waktu:</span>
                                <span class="fw-semibold ms-1">{{ formatDate(suratJalan.waktu_po) }}</span>
                            </div>
                            <div>
                                <span class="badge bg-warning text-dark rounded-pill px-2"
                                    style="font-size:.68rem;font-weight:700">
                                    {{ (suratJalan.status || '').toUpperCase() }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div v-else class="p-3 rounded" style="background:#f8fafc;border:2px dashed #e2e8f0">
                        <small class="text-muted">Informasi PO akan muncul setelah scan</small>
                    </div>
                </div>

                <!-- Kode Supplier -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold small text-muted mb-2 d-flex align-items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="4" y1="9" x2="20" y2="9" />
                            <line x1="4" y1="15" x2="20" y2="15" />
                            <line x1="10" y1="3" x2="8" y2="21" />
                            <line x1="16" y1="3" x2="14" y2="21" />
                        </svg>
                        KODE SUPPLIER
                    </label>
                    <div v-if="suratJalan" class="p-3 rounded info-card">
                        <label class="form-label fw-semibold mb-2" style="font-size:.83rem">
                            Kode Supplier <span class="text-danger">*</span>
                        </label>
                        <input
                            type="text"
                            class="form-control form-control-modern font-mono"
                            :class="{'is-invalid': submitAttempted && !kodeSupplier.trim()}"
                            placeholder="Nomor dokumen dari supplier..."
                            v-model="kodeSupplier"
                            autocomplete="off">
                        <div class="invalid-feedback">Kode supplier wajib diisi</div>
                    </div>
                    <div v-else class="p-3 rounded" style="background:#f8fafc;border:2px dashed #e2e8f0">
                        <small class="text-muted">Input kode supplier akan muncul setelah scan PO</small>
                    </div>
                </div>
            </div>

            <!-- ITEM TABLE -->
            <div class="card border-0 shadow-sm flex-fill d-flex flex-column" style="background:white">
                <div class="card-header bg-transparent border-0 pt-3 pb-2 px-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <h6 class="fw-semibold mb-0 d-flex align-items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="8" y1="6" x2="21" y2="6" />
                                <line x1="8" y1="12" x2="21" y2="12" />
                                <line x1="8" y1="18" x2="21" y2="18" />
                                <line x1="3" y1="6" x2="3.01" y2="6" />
                                <line x1="3" y1="12" x2="3.01" y2="12" />
                                <line x1="3" y1="18" x2="3.01" y2="18" />
                            </svg>
                            Daftar Barang
                            <span class="badge bg-primary rounded-pill ms-2">{{ items.length }}</span>
                        </h6>
                        <div v-if="items.length" class="d-flex gap-2">
                            <button class="btn btn-sm btn-outline-secondary modern-btn d-flex align-items-center gap-1"
                                @click="fillAll">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12" />
                                </svg>
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="margin-left:-6px">
                                    <polyline points="20 6 9 17 4 12" />
                                </svg>
                                Sesuai PO
                            </button>
                            <button class="btn btn-sm btn-outline-danger modern-btn d-flex align-items-center gap-1"
                                @click="clearAll">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 20H7L3 7h18l-1 5" />
                                    <path d="m3 7 1.5-4.5" />
                                    <path d="M18 20h4" />
                                </svg>
                                Reset Qty
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card-body p-0 flex-fill overflow-auto">
                    <table class="table table-hover mb-0 modern-table">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th class="ps-3" width="40">#</th>
                                <th>Nama Barang</th>
                                <th width="100" class="text-center">Satuan</th>
                                <th width="120" class="text-center">Qty PO</th>
                                <th width="160" class="text-center">
                                    Qty Diterima <span class="text-danger">*</span>
                                </th>
                                <th width="120" class="text-center">Selisih</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="items.length === 0">
                                <td colspan="6" class="text-center py-5">
                                    <div class="empty-state">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="color:#cbd5e1">
                                            <path d="M22 2H2v20l4-4h16V2z" />
                                            <path d="M7 10h10" />
                                            <path d="M7 14h6" />
                                        </svg>
                                        <p class="text-muted mt-2 mb-0">Scan kode PO untuk menampilkan barang</p>
                                    </div>
                                </td>
                            </tr>
                            <tr v-for="(item, idx) in items" :key="item.id">
                                <td class="ps-3 text-muted">{{ idx + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="product-icon-small">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z" />
                                                <path d="m3.3 7 8.7 5 8.7-5" />
                                                <path d="M12 22V12" />
                                            </svg>
                                        </div>
                                        <span class="fw-semibold">{{ item.nama_barang }}</span>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark border fw-semibold px-2" style="font-size:.75rem">
                                        {{ item.nama_satuan }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark border fw-bold font-mono px-3">
                                        {{ item.qty }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <input
                                        type="number"
                                        min="1"
                                        v-model.number="item.qty_diterima"
                                        class="form-control qty-input text-center fw-bold"
                                        :class="{
                                            'is-invalid':     submitAttempted && !(item.qty_diterima >= 1),
                                            'border-success': item.qty_diterima >= 1 && item.qty_diterima == item.qty,
                                            'border-warning': item.qty_diterima >= 1 && item.qty_diterima != item.qty,
                                        }"
                                        style="width:90px;margin:0 auto;"
                                        placeholder="0">
                                </td>
                                <td class="text-center">
                                    <template v-if="item.qty_diterima >= 1">
                                        <span v-if="gap(item) === 0" class="chip chip-ok">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                                <polyline points="20 6 9 17 4 12" />
                                            </svg>
                                            Sesuai
                                        </span>
                                        <span v-else-if="gap(item) > 0" class="chip chip-lebih">
                                            +{{ gap(item) }}
                                        </span>
                                        <span v-else class="chip chip-kurang">
                                            {{ gap(item) }}
                                        </span>
                                    </template>
                                    <span v-else class="text-muted">—</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- FOOTER -->
                <div class="card-footer bg-light border-0 p-3">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <div v-if="simpanError"
                                class="d-flex align-items-center gap-2 text-danger"
                                style="font-size:.83rem">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0">
                                    <circle cx="12" cy="12" r="10" />
                                    <line x1="12" y1="8" x2="12" y2="12" />
                                    <line x1="12" y1="16" x2="12.01" y2="16" />
                                </svg>
                                {{ simpanError }}
                            </div>
                            <div v-else class="d-flex align-items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-muted">
                                    <circle cx="12" cy="12" r="10" />
                                    <line x1="12" y1="8" x2="12" y2="12" />
                                    <line x1="12" y1="16" x2="12.01" y2="16" />
                                </svg>
                                <small class="text-muted">
                                    Total {{ items.length }} jenis barang dalam surat jalan
                                </small>
                            </div>
                        </div>
                        <div class="col-md-6 text-end">
                            <div class="d-flex justify-content-end align-items-center gap-3">
                                
                                <button
                                    class="btn btn-success btn-lg px-4 modern-btn d-flex align-items-center gap-2"
                                    :disabled="items.length === 0 || saving"
                                    @click="simpan">
                                    <span v-if="saving">
                                        <span class="spinner-border spinner-border-sm me-1"></span>
                                        Menyimpan...
                                    </span>
                                    <template v-else>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z" />
                                            <polyline points="17 21 17 13 7 13 7 21" />
                                            <polyline points="7 3 7 8 15 8" />
                                        </svg>
                                        Simpan Penerimaan
                                    </template>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div><!-- /main content -->
    </div><!-- /glass-panel -->
</div><!-- /#app -->

<!-- MODAL SUKSES -->
<div class="modal fade" id="modalSukses" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg modern-modal">
            <div class="modal-body text-center p-4">
                <div class="mb-3 text-success">
                    <svg xmlns="http://www.w3.org/2000/svg" width="52" height="52" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z" />
                        <path d="m9 12 2 2 4-4" />
                    </svg>
                </div>
                <h5 class="fw-bold mb-1">Penerimaan Tersimpan!</h5>
                <p class="text-muted small mb-3">Stok gudang telah diperbarui secara otomatis.</p>
                <div class="p-3 rounded mb-3 text-start" style="background:#f8fafc;border:1px solid #e2e8f0">
                    <div class="d-flex justify-content-between small mb-1">
                        <span class="text-muted">Kode Penerimaan</span>
                        <span class="fw-bold font-mono text-primary">{{ sukses.kode }}</span>
                    </div>
                    <div class="d-flex justify-content-between small">
                        <span class="text-muted">ID</span>
                        <span class="fw-semibold">#{{ sukses.id }}</span>
                    </div>
                </div>
                <div class="d-flex gap-2 justify-content-center">
                    <button class="btn btn-light px-4 modern-btn" data-bs-dismiss="modal" @click="reset">
                        Baru
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .font-mono {
        font-family: 'Courier New', monospace;
    }

    /* Toast */
    .toast {
        min-width: 300px;
        border-radius: 12px;
        animation: slideInRight 0.3s ease;
    }

    .toast-success {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
    }

    .toast-error {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: white;
    }

    .toast-info {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: white;
    }

    .toast-warning {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: white;
    }

    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0
        }

        to {
            transform: translateX(0);
            opacity: 1
        }
    }

    /* Glass */
    .glass-panel {
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(20px) saturate(180%);
        border: 1px solid rgba(255, 255, 255, 0.5);
        border-radius: 16px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
    }

    /* Buttons */
    .modern-btn {
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .modern-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(0, 0, 0, .15);
    }

    .modern-btn:active {
        transform: translateY(0);
    }

    /* Badge modern */
    .badge-modern {
        padding: .6rem 1.2rem;
        border-radius: 10px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, .1);
    }

    /* Lock overlay */
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
            transform: translateY(20px)
        }

        to {
            opacity: 1;
            transform: translateY(0)
        }
    }

    .blur-content {
        pointer-events: none;
        opacity: 0.5;
        filter: blur(2px);
    }

    /* Form */
    .form-control-modern {
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        padding: .65rem 1rem;
        transition: all .2s;
        font-size: .95rem;
    }

    .form-control-modern:focus {
        border-color: var(--win-accent, #0067C0);
        box-shadow: 0 0 0 4px rgba(0, 103, 192, 0.1);
    }

    /* Info cards */
    .suplier-card {
        background: rgba(0, 103, 192, 0.08);
        border: 2px solid rgba(0, 103, 192, 0.2);
        border-radius: 10px;
    }

    .info-card {
        background: rgba(16, 185, 129, 0.08);
        border: 2px solid rgba(16, 185, 129, 0.2);
        border-radius: 10px;
    }

    /* Product icon */
    .product-icon-small {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        flex-shrink: 0;
        background: linear-gradient(135deg, #667eea, #764ba2);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
    }

    /* Table */
    .modern-table {
        font-size: .9rem;
    }

    .modern-table thead th {
        font-weight: 700;
        font-size: .75rem;
        text-transform: uppercase;
        letter-spacing: .5px;
        color: #64748b;
        border-bottom: 2px solid #e2e8f0;
        padding: .75rem;
    }

    .modern-table tbody tr {
        transition: all .2s;
    }

    .modern-table tbody tr:hover {
        background: rgba(0, 103, 192, 0.03);
    }

    .modern-table tbody td {
        padding: 1rem .75rem;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
    }

    /* Qty input */
    .qty-input {
        border-radius: 8px;
        border: 2px solid #e2e8f0;
        font-weight: 600;
    }

    .qty-input:focus {
        border-color: var(--win-accent, #0067C0);
        box-shadow: 0 0 0 3px rgba(0, 103, 192, 0.1);
    }

    /* Chips */
    .chip {
        padding: 3px 10px;
        border-radius: 20px;
        font-size: .72rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 3px;
    }

    .chip-ok {
        background: #d1fae5;
        color: #065f46;
    }

    .chip-lebih {
        background: #dbeafe;
        color: #1e40af;
    }

    .chip-kurang {
        background: #fee2e2;
        color: #991b1b;
    }

    /* Empty */
    .empty-state {
        padding: 2rem;
    }

    /* Modal */
    .modern-modal {
        border-radius: 16px;
        overflow: hidden;
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
                kodePo: '',
                suratJalan: null,
                items: [],
                loading: false,
                scanError: null,

                kodeSupplier: '',
                submitAttempted: false,
                simpanError: null,
                saving: false,

                sukses: {
                    kode: '',
                    id: null
                },
                toasts: [],
            };
        },

        computed: {
            totalQty() {
                return this.items.reduce((s, i) => s + (parseInt(i.qty_diterima) || 0), 0);
            },
            detailUrl() {
                return `<?= base_url('penerimaan-gudang/detail') ?>/${this.sukses.id}`;
            },
        },

        methods: {
            /* ── Toast ── */
            toastIcon(type) {
                const icons = {
                    success: '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>',
                    error: '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>',
                    info: '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>',
                    warning: '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>',
                };
                return icons[type] || icons.info;
            },
            addToast(message, type = 'info') {
                const id = Date.now() + Math.random();
                this.toasts.push({
                    id,
                    message,
                    type
                });
                setTimeout(() => this.removeToastById(id), 4000);
            },
            removeToast(index) {
                this.toasts.splice(index, 1);
            },
            removeToastById(id) {
                const i = this.toasts.findIndex(t => t.id === id);
                if (i !== -1) this.toasts.splice(i, 1);
            },

            /* ── Helpers ── */
            gap(item) {
                return (parseInt(item.qty_diterima) || 0) - parseInt(item.qty);
            },
            fillAll() {
                this.items = this.items.map(i => ({
                    ...i,
                    qty_diterima: parseInt(i.qty) || 0
                }));
            },
            clearAll() {
                this.items = this.items.map(i => ({
                    ...i,
                    qty_diterima: ''
                }));
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

            /* ── Apply payload dari API / session ── */
            applyPayload(data) {
                this.suratJalan = data.surat;
                this.kodePo = data.surat.kode_po;
                this.items = (data.items || []).map(i => ({
                    id: i.id,
                    barang_id: i.barang_id,
                    nama_barang: i.nama_barang,
                    nama_satuan: i.nama_satuan || '-',
                    qty: parseInt(i.qty) || 0,
                    qty_diterima: parseInt(i.qty) || 0,
                }));
            },

            /* ── Load session saat refresh ── */
            async loadSession() {
                try {
                    const r = await axios.get('<?= base_url('penerimaan-gudang/session-scan') ?>');
                    if (r.data && r.data.status) {
                        this.applyPayload(r.data);
                    }
                } catch (_) {
                    /* tidak ada session */ }
            },

            /* ── SCAN PO ── */
            async scanPo() {
                if (!this.kodePo.trim()) return;
                this.loading = true;
                this.scanError = null;
                try {
                    const r = await axios.get('<?= base_url('penerimaan-gudang/scan-po') ?>', {
                        params: {
                            kode: this.kodePo.trim()
                        },
                    });
                    this.applyPayload(r.data);
                    this.addToast('PO ditemukan: ' + r.data.surat.kode_po, 'success');
                } catch (e) {
                    this.scanError = e.response?.data?.message ?? 'PO tidak ditemukan';
                    this.addToast(this.scanError, 'error');
                } finally {
                    this.loading = false;
                }
            },

            /* ── Validasi ── */
            validate() {
                if (!this.kodeSupplier.trim()) return false;
                return this.items.every(i => parseInt(i.qty_diterima) >= 1);
            },

            /* ── SIMPAN ── */
            async simpan() {
                this.submitAttempted = true;
                this.simpanError = null;

                if (!this.validate()) {
                    this.simpanError = 'Isi kode supplier dan qty diterima semua barang terlebih dahulu.';
                    this.addToast(this.simpanError, 'warning');
                    return;
                }

                this.saving = true;
                try {
                    const r = await axios.post('<?= base_url('penerimaan-gudang/simpan') ?>', {
                        surat_jalan_id: this.suratJalan.id,
                        kode_supplier: this.kodeSupplier.trim(),
                        items: this.items.map(i => ({
                            barang_id: i.barang_id,
                            qty_dipesan: i.qty,
                            qty_diterima: parseInt(i.qty_diterima),
                        })),
                    });
                    this.sukses = {
                        kode: r.data.kode_penerimaan,
                        id: r.data.penerimaan_id
                    };
                    new bootstrap.Modal(document.getElementById('modalSukses')).show();
                } catch (e) {
                    this.simpanError = e.response?.data?.message ?? 'Gagal menyimpan penerimaan';
                    this.addToast(this.simpanError, 'error');
                } finally {
                    this.saving = false;
                }
            },

            /* ── RESET ── */
            async reset() {
                this.suratJalan = null;
                this.items = [];
                this.kodePo = '';
                this.kodeSupplier = '';
                this.scanError = null;
                this.simpanError = null;
                this.submitAttempted = false;
                try {
                    await axios.post('<?= base_url('penerimaan-gudang/clear-session') ?>');
                } catch (_) {}
                this.$nextTick(() => document.getElementById('inputKodePo')?.focus());
            },
        },

        async mounted() {
            await this.loadSession();
            // Lucide hanya dipanggil sekali di sini, tidak di updated()
            lucide.createIcons();
            if (!this.suratJalan) {
                this.$nextTick(() => document.getElementById('inputKodePo')?.focus());
            }
        },
        // !! TIDAK ADA updated() hook — ini penyebab bug list tidak muncul !!
    }).mount('#app');
</script>
<?= $this->endSection() ?>