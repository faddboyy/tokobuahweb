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

    <!-- LAYOUT UTAMA: 2 KOLOM SIDE BY SIDE -->
    <div class="pos-layout flex-fill" style="min-height: 0;">

        <!-- KOLOM KIRI 25% -->
        <div class="pos-left glass-panel d-flex flex-column position-relative">

            <!-- LOCK OVERLAY -->
            <div v-if="!activeId" class="lock-overlay">
                <div class="text-center lock-content">
                    <div class="lock-icon mb-3">
                        <i data-lucide="lock" style="width: 64px; height: 64px;"></i>
                    </div>
                    <h4 class="fw-bold mb-2">Transaksi Terkunci</h4>
                    <p class="text-muted small">Klik tombol di bawah untuk memulai</p>
                </div>
            </div>

            <!-- Header -->
            <div class="px-4 pt-4 pb-3 border-bottom">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <i data-lucide="shopping-cart" style="width: 22px;"></i>
                    <h5 class="fw-bold mb-0 text-dark">Transaksi Penjualan</h5>
                </div>
                <p class="text-muted small mb-0">Point of Sale - Toko Buah</p>
            </div>

            <!-- Scrollable body kiri -->
            <div class="flex-fill overflow-y-auto px-4 py-3" style="overflow-y: auto;">

                <!-- Status transaksi / tombol mulai -->
                <div class="mb-4">
                    <button v-if="!activeId"
                        class="btn btn-primary w-100 btn-lg modern-btn d-flex align-items-center justify-content-center gap-2"
                        @click="mulaiTransaksi">
                        <i data-lucide="play-circle" style="width: 20px;"></i>
                        Mulai Transaksi
                    </button>
                    <div v-else class="badge-modern w-100 justify-content-center"
                        style="background: linear-gradient(135deg, #10b981, #059669); color: white;">
                        <i data-lucide="check-circle" style="width: 16px;"></i>
                        Transaksi #{{ activeId }} &mdash; Aktif
                    </div>
                </div>

                <!-- Customer -->
                <div class="mb-4" :class="{'opacity-50 pe-none': !activeId}">
                    <label class="form-label fw-semibold small text-muted mb-2">
                        <i data-lucide="user" style="width: 13px;"></i> CUSTOMER
                    </label>
                    <div class="position-relative">
                        <input type="text"
                            ref="customerSearch"
                            class="form-control form-control-modern"
                            placeholder="Cari customer..."
                            v-model="searchCustomer"
                            @input="searchCust"
                            @focus="customerFocused = true"
                            @blur="() => setTimeout(() => customerFocused = false, 200)">

                        <div v-if="customerResults.length && customerFocused"
                            class="dropdown-modern shadow-lg">
                            <div v-for="c in customerResults" :key="c.id"
                                @click="selectCustomer(c)"
                                class="dropdown-item-modern">
                                <i data-lucide="user" style="width: 16px;"></i>
                                <span>{{ c.nama }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="mt-2 p-2 rounded" style="background: rgba(100,116,139,0.08);">
                        <small class="text-muted">Terpilih:</small>
                        <div class="fw-semibold text-secondary d-flex align-items-center gap-2 small">
                            <i data-lucide="users" style="width: 13px;"></i>
                            {{ selectedCustomer ? selectedCustomer.nama : 'Customer Umum' }}
                        </div>
                    </div>
                </div>

                <!-- Mode Input -->
                <div class="mb-3" :class="{'opacity-50 pe-none': !activeId}">
                    <label class="form-label fw-semibold small text-muted mb-2">
                        <i data-lucide="settings" style="width: 13px;"></i> MODE INPUT
                    </label>
                    <div class="btn-group w-100 mode-selector" role="group">
                        <button type="button"
                            class="btn btn-outline-primary"
                            :class="{active: mode=='manual'}"
                            @click="mode='manual'">
                            <i data-lucide="mouse-pointer-click" style="width: 15px;"></i>
                            Manual
                        </button>
                        <button type="button"
                            class="btn btn-outline-primary"
                            :class="{active: mode=='scan'}"
                            @click="mode='scan'; $nextTick(() => $refs.barcode?.focus())">
                            <i data-lucide="scan-line" style="width: 15px;"></i>
                            Scan
                        </button>
                    </div>
                </div>

                <!-- Input Produk -->
                <div :class="{'opacity-50 pe-none': !activeId}">

                    <!-- Manual Search -->
                    <div v-if="mode=='manual'" class="position-relative">
                        <div class="input-group input-group-modern">
                            <span class="input-group-text bg-white border-end-0">
                                <i data-lucide="search" style="width: 17px;"></i>
                            </span>
                            <input type="text"
                                ref="barangSearch"
                                class="form-control form-control-modern border-start-0 ps-0"
                                placeholder="Nama atau barcode..."
                                v-model="searchProduct"
                                @input="searchProd"
                                @focus="productFocused = true"
                                @blur="() => setTimeout(() => productFocused = false, 200)">
                        </div>

                        <div v-if="productResults.length && productFocused"
                            class="dropdown-modern dropdown-product shadow-lg">
                            <div v-for="p in productResults" :key="p.inventory_id"
                                @click="addItem(p.inventory_id)"
                                class="dropdown-item-product">
                                <div class="d-flex justify-content-between align-items-center w-100">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="product-icon">
                                            <i data-lucide="package" style="width: 15px;"></i>
                                        </div>
                                        <div>
                                            <div class="fw-semibold small">{{ p.nama }}</div>
                                            <div v-if="p.nominal_diskon > 0" class="d-flex align-items-center gap-1 flex-wrap">
                                                <small class="price-original">Rp {{ format(p.harga_jual) }}</small>
                                                <small class="price-discount-badge">
                                                    <i data-lucide="tag" style="width: 9px;"></i>
                                                    -Rp {{ format(p.nominal_diskon) }}
                                                </small>
                                                <small class="price-after-discount">Rp {{ format(p.harga_setelah_diskon) }}</small>
                                            </div>
                                            <small v-else class="text-muted">Rp {{ format(p.harga_jual) }}</small>
                                        </div>
                                    </div>
                                    <div class="badge bg-light text-dark" style="font-size: 0.7rem;">
                                        {{ p.stock }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Scan Barcode -->
                    <div v-if="mode=='scan'">
                        <div class="scan-container p-3 text-center">
                            <div class="scan-animation mb-2">
                                <i data-lucide="scan-line" style="width: 36px; height: 36px;"></i>
                            </div>
                            <input type="text"
                                ref="barcode"
                                class="form-control form-control-lg text-center fw-bold border-2"
                                style="font-size: 1.2rem; letter-spacing: 2px;"
                                placeholder="Scan barcode..."
                                v-model="barcode"
                                @input="onBarcodeInput"
                                @paste="onBarcodePaste"
                                autofocus>
                            <small class="text-muted mt-2 d-block" style="font-size: 0.72rem;">
                                <i data-lucide="zap" style="width: 11px;"></i>
                                Auto-submit saat scan (100ms)
                            </small>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Footer kiri: shortcut info -->
            <div class="px-4 py-3 border-top" style="background: rgba(248,250,252,0.8);">
                <div class="d-flex flex-column gap-1">
                    <small class="text-muted" style="font-size: 0.7rem;">
                        <kbd>Alt+C</kbd> Customer &nbsp;
                        <kbd>Alt+B</kbd> Manual &nbsp;
                        <kbd>Alt+S</kbd> Scan
                    </small>
                    <small class="text-muted" style="font-size: 0.7rem;">
                        <kbd>Alt+P</kbd> Bayar &nbsp;
                        <kbd>Alt+M</kbd> Toggle mode &nbsp;
                        <kbd>Alt+D</kbd> Draft
                    </small>
                </div>
            </div>
        </div>

        <!-- KOLOM KANAN 75% -->
        <div class="pos-right glass-panel d-flex flex-column" style="min-height: 0;">

            <!-- Header keranjang -->
            <div class="px-4 pt-4 pb-3 border-bottom d-flex justify-content-between align-items-center">
                <h5 class="fw-semibold mb-0 d-flex align-items-center gap-2">
                    <i data-lucide="shopping-bag" style="width: 20px;"></i>
                    Keranjang Belanja
                    <span class="badge bg-primary rounded-pill ms-1">{{ cart.length }}</span>
                </h5>
                <div class="d-flex align-items-center gap-2">
                    <span v-if="totalDiskonItem > 0" class="badge-hemat">
                        <i data-lucide="zap" style="width: 11px;"></i>
                        Hemat Rp {{ format(totalDiskonItem) }}
                    </span>
                    <!-- Tombol Draft — Alt+D -->
                    <button class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1"
                        style="border-radius: 8px; font-size: 0.78rem; font-weight: 600;"
                        @click="openDraftModal"
                        title="Lihat Draft (Alt+D)">
                        <i data-lucide="file-clock" style="width: 14px;"></i>
                        Draft
                        <span v-if="draftCount > 0"
                            class="badge bg-secondary rounded-pill"
                            style="font-size: 0.65rem;">{{ draftCount }}</span>
                        <small class="opacity-50 ms-1">(Alt+D)</small>
                    </button>
                </div>
            </div>

            <!-- Tabel keranjang -->
            <div class="flex-fill overflow-auto" style="min-height: 0;">
                <table class="table table-hover mb-0 modern-table">
                    <thead class="table-light sticky-top">
                        <tr>
                            <th class="ps-3">Nama Barang</th>
                            <th width="110">Stok</th>
                            <th width="165">Harga</th>
                            <th width="115">Qty</th>
                            <th width="165">Subtotal</th>
                            <th width="70" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="cart.length==0">
                            <td colspan="6" class="text-center py-5">
                                <div class="empty-state">
                                    <i data-lucide="inbox" style="width: 48px; height: 48px; opacity: 0.3;"></i>
                                    <p class="text-muted mt-2 mb-0">Keranjang masih kosong</p>
                                </div>
                            </td>
                        </tr>
                        <tr v-for="item in cart" :key="item.id" class="cart-row">
                            <td class="ps-3">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="product-icon-small">
                                        <i data-lucide="package" style="width: 14px;"></i>
                                    </div>
                                    <div>
                                        <span class="fw-semibold">{{ item.nama }}</span>
                                        <span v-if="item.nominal_diskon > 0" class="ms-2 badge-diskon-aktif">
                                            <i data-lucide="tag" style="width: 10px;"></i>
                                            DISKON
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark">
                                    {{ item.stock }} {{ item.satuan }}
                                </span>
                            </td>
                            <td>
                                <div v-if="item.nominal_diskon > 0" class="price-cell">
                                    <div class="price-original-cell">Rp {{ format(item.harga_satuan) }}</div>
                                    <div class="price-diskon-cell">
                                        <i data-lucide="minus-circle" style="width: 10px;"></i>
                                        Rp {{ format(item.nominal_diskon) }}
                                    </div>
                                    <div class="price-final-cell">Rp {{ format(item.harga_setelah_diskon) }}</div>
                                </div>
                                <div v-else>Rp {{ format(item.harga_satuan) }}</div>
                            </td>
                            <td>
                                <input type="number"
                                    class="form-control form-control-sm text-center qty-input"
                                    v-model.number="item.qty"
                                    min="1"
                                    :max="item.stock"
                                    @keyup.enter="updateQty(item.id, item.qty)"
                                    @blur="updateQty(item.id, item.qty)">
                            </td>
                            <td>
                                <div v-if="item.nominal_diskon > 0" class="price-cell">
                                    <div class="subtotal-original-cell">Rp {{ format(item.harga_satuan * item.qty) }}</div>
                                    <div class="price-diskon-cell">
                                        <i data-lucide="minus-circle" style="width: 10px;"></i>
                                        Rp {{ format(item.nominal_diskon * item.qty) }}
                                    </div>
                                    <div class="subtotal-final-cell">Rp {{ format(item.subtotal) }}</div>
                                </div>
                                <div v-else class="fw-bold text-primary">Rp {{ format(item.subtotal) }}</div>
                            </td>
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

            <!-- Footer kanan: total + bayar -->
            <div class="px-4 py-3 border-top" style="background: rgba(248,250,252,0.9);">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <i data-lucide="info" style="width: 15px;" class="text-muted"></i>
                        <small class="text-muted">{{ cart.length }} item dalam keranjang</small>
                    </div>
                    <div class="d-flex align-items-center gap-4">
                        <div class="text-end">
                            <small class="text-muted d-block" style="font-size: 0.72rem;">TOTAL BELANJA</small>
                            <h4 class="mb-0 fw-bold text-primary">Rp {{ format(total) }}</h4>
                        </div>
                        <button class="btn btn-success btn-lg px-4 modern-btn d-flex align-items-center gap-2"
                            :disabled="cart.length==0"
                            @click="openModalPembayaran"
                            title="Alt + P">
                            <i data-lucide="credit-card" style="width: 18px;"></i>
                            Bayar
                            <small class="opacity-75">(Alt+P)</small>
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- MODAL PEMBAYARAN -->
<div class="modal fade" id="modalPembayaran" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg modern-modal">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold d-flex align-items-center gap-2">
                    <i data-lucide="credit-card" style="width: 24px;"></i>
                    Pembayaran
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="alert alert-light border-0 mb-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                    <div class="row align-items-center">
                        <div class="col">
                            <small class="opacity-75">Total Belanja</small>
                            <h3 class="mb-0 fw-bold">Rp {{ format(total) }}</h3>
                            <div v-if="totalDiskonItem > 0" class="mt-1">
                                <small class="opacity-90" style="background: rgba(255,255,255,0.2); padding: 2px 8px; border-radius: 20px;">
                                    <i data-lucide="zap" style="width: 10px;"></i>
                                    Hemat Rp {{ format(totalDiskonItem) }} dari diskon promo
                                </small>
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="bg-white bg-opacity-25 rounded-3 px-3 py-2">
                                <small class="opacity-75 d-block">Items</small>
                                <div class="fw-bold fs-5">{{ cart.length }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Metode Pembayaran</label>
                        <select class="form-select form-control-modern" v-model="jenisBayar" ref="selectJenisBayar">
                            <option value="tunai">Tunai</option>
                            <option value="debit">Kartu Debit</option>
                            <option value="qris">QRIS</option>
                            <option value="transfer">Transfer Bank</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Diskon Tambahan (Rp)</label>
                        <input type="number" ref="inputDiskon" class="form-control form-control-modern"
                            v-model.number="diskon" min="0" placeholder="0">
                    </div>
                    <div class="col-12">
                        <div class="p-3 rounded" style="background: rgba(0, 200, 80, 0.1);">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted">Total Setelah Diskon:</span>
                                <span class="fw-bold fs-5 text-success">Rp {{ format(grandTotal) }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Nominal Bayar</label>
                        <input type="number" ref="inputBayar"
                            class="form-control form-control-modern form-control-lg text-end fw-bold"
                            style="font-size: 1.5rem;"
                            v-model.number="bayar" min="0" placeholder="0"
                            @input="hitungKembalian">
                    </div>
                    <div class="col-12">
                        <div class="p-3 rounded"
                            :style="kembalian >= 0 ? 'background: rgba(0, 103, 192, 0.1);' : 'background: rgba(220, 53, 69, 0.1);'">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-semibold">Kembalian:</span>
                                <span class="fw-bold fs-4" :class="kembalian >= 0 ? 'text-primary' : 'text-danger'">
                                    Rp {{ format(kembalian) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <form method="post" action="<?= base_url('penjualan/finalisasi') ?>">
                    <input type="hidden" name="customer_id" :value="selectedCustomer?.id || ''">
                    <input type="hidden" name="jenis_bayar" :value="jenisBayar">
                    <input type="hidden" name="diskon" :value="diskon">
                    <input type="hidden" name="bayar" :value="bayar">
                    <input type="hidden" name="kembalian" :value="kembalian">
                    <button type="submit" class="btn btn-primary px-4 modern-btn"
                        ref="btnSubmit"
                        :disabled="kembalian < 0 || bayar == 0">
                        <i data-lucide="check-circle" style="width: 18px;"></i>
                        Proses Pembayaran
                    </button>
                </form>
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
                    <i data-lucide="alert-triangle" style="width: 48px; height: 48px; color: #f59e0b;"></i>
                </div>
                <h5 class="fw-bold mb-2">Konfirmasi</h5>
                <p class="text-muted mb-4">{{ confirmMessage }}</p>
                <div class="d-flex gap-2">
                    <button class="btn btn-light flex-fill" data-bs-dismiss="modal">Batal</button>
                    <button class="btn btn-danger flex-fill" @click="confirmAction" data-bs-dismiss="modal">
                        Ya, Hapus
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ================================================================ -->
<!-- MODAL DRAFT — Alt+D                                               -->
<!-- ================================================================ -->
<div class="modal fade" id="modalDraft" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg modern-modal">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold d-flex align-items-center gap-2">
                    <i data-lucide="file-clock" style="width: 22px;"></i>
                    Draft Transaksi Saya
                    <span v-if="draftCount > 0" class="badge bg-secondary rounded-pill ms-1"
                        style="font-size: 0.7rem;">{{ draftCount }}</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">

                <!-- Loading -->
                <div v-if="draftLoading" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="text-muted mt-3 small mb-0">Memuat draft...</p>
                </div>

                <!-- Kosong -->
                <div v-else-if="draftList.length === 0" class="text-center py-5">
                    <i data-lucide="file-x" style="width: 52px; height: 52px; color: #cbd5e1;"></i>
                    <p class="fw-semibold text-muted mt-3 mb-1">Tidak ada draft tersimpan</p>
                    <p class="text-muted small mb-0">Draft transaksi Anda akan muncul di sini</p>
                </div>

                <!-- List -->
                <div v-else class="d-flex flex-column gap-2">
                    <div v-for="d in draftList" :key="d.id"
                        class="draft-item d-flex align-items-center gap-3 p-3 rounded-3 border">

                        <!-- Icon -->
                        <div class="draft-icon flex-shrink-0">
                            <i data-lucide="receipt" style="width: 18px;"></i>
                        </div>

                        <!-- Info -->
                        <div class="flex-fill min-w-0">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="fw-bold text-dark">Draft #{{ d.id }}</span>
                                <span v-if="d.id == activeId"
                                    class="badge bg-success"
                                    style="font-size: 0.65rem;">Aktif</span>
                            </div>
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <span class="small text-muted">
                                    <i data-lucide="shopping-cart" style="width: 11px;"></i>
                                    {{ d.jumlah_item }} item
                                </span>
                                <span class="text-muted small">&#183;</span>
                                <span class="small fw-bold text-primary">Rp {{ format(d.nominal_penjualan) }}</span>
                            </div>
                            <div class="mt-1" style="font-size: 0.7rem; color: #94a3b8;">
                                <i data-lucide="clock" style="width: 10px;"></i>
                                {{ d.created_label }}
                            </div>
                        </div>

                        <!-- Aksi -->
                        <div class="d-flex gap-2 flex-shrink-0">
                            <button class="btn btn-sm btn-primary d-flex align-items-center gap-1"
                                style="border-radius: 8px; font-size: 0.78rem; white-space: nowrap;"
                                :disabled="d.id == activeId"
                                @click="teruskanDraft(d.id)">
                                <i data-lucide="play-circle" style="width: 13px;"></i>
                                {{ d.id == activeId ? 'Aktif' : 'Teruskan' }}
                            </button>
                            <button class="btn btn-sm btn-outline-danger d-flex align-items-center gap-1"
                                style="border-radius: 8px; font-size: 0.78rem;"
                                @click="hapusDraft(d.id)">
                                <i data-lucide="trash-2" style="width: 13px;"></i>
                                Hapus
                            </button>
                        </div>

                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<style>
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

    .glass-panel {
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(20px) saturate(180%);
        border: 1px solid rgba(255, 255, 255, 0.5);
        border-radius: 16px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
    }

    .modern-btn {
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: none;
    }

    .modern-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
    }

    .modern-btn:active {
        transform: translateY(0);
    }

    .badge-modern {
        padding: 0.6rem 1.2rem;
        border-radius: 10px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

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

    .form-control-modern {
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        padding: 0.65rem 1rem;
        padding-left: 2.5rem;
        transition: all 0.2s;
        font-size: 0.95rem;
    }

    .form-control-modern:focus {
        border-color: var(--win-accent);
        box-shadow: 0 0 0 4px rgba(0, 103, 192, 0.1);
    }

    .input-group-modern .input-group-text {
        border: 2px solid #e2e8f0;
        border-radius: 10px 0 0 10px;
        background: white;
    }

    .input-group-modern .form-control {
        border: 2px solid #e2e8f0;
        border-left: none;
        border-radius: 0 10px 10px 0;
    }

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
        color: var(--win-accent);
    }

    .dropdown-item-modern:last-child {
        border-bottom: none;
    }

    .dropdown-product {
        max-height: 400px;
    }

    .dropdown-item-product {
        padding: 1rem;
        cursor: pointer;
        transition: all 0.2s;
        border-bottom: 1px solid #f1f5f9;
    }

    .dropdown-item-product:hover {
        background: linear-gradient(90deg, rgba(0, 103, 192, 0.05) 0%, transparent 100%);
    }

    .product-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
    }

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

    .mode-selector .btn {
        border-radius: 10px;
        font-weight: 600;
        border: 2px solid #e2e8f0;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .mode-selector .btn.active {
        background: var(--win-accent);
        border-color: var(--win-accent);
        color: white;
        box-shadow: 0 4px 12px rgba(0, 103, 192, 0.3);
    }

    .scan-container {
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
        border-radius: 12px;
        border: 2px dashed #cbd5e1;
    }

    .scan-animation {
        color: #667eea;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {

        0%,
        100% {
            opacity: 1;
            transform: scale(1);
        }

        50% {
            opacity: 0.5;
            transform: scale(1.1);
        }
    }

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

    .qty-input {
        border-radius: 8px;
        border: 2px solid #e2e8f0;
        font-weight: 600;
    }

    .qty-input:focus {
        border-color: var(--win-accent);
        box-shadow: 0 0 0 3px rgba(0, 103, 192, 0.1);
    }

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

    .empty-state {
        padding: 2rem;
    }

    .empty-state i {
        color: #cbd5e1;
    }

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

    /* ========== LAYOUT ========== */
    html,
    body {
        height: 100%;
        overflow: hidden;
    }

    .main-content,
    .content-wrapper,
    main,
    #main,
    .page-content,
    .app-content {
        height: 100%;
        overflow: hidden;
    }

    #app {
        height: 100%;
        overflow: hidden;
    }

    .pos-layout {
        display: flex;
        gap: 12px;
        height: 100%;
        overflow: hidden;
    }

    .pos-left {
        width: 25%;
        min-width: 260px;
        flex-shrink: 0;
        height: 100%;
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }

    .pos-right {
        flex: 1;
        height: 100%;
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }

    /* ========== DISKON STYLES ========== */
    .price-original {
        color: #16a34a;
        font-weight: 600;
        font-size: 0.8rem;
    }

    .price-discount-badge {
        display: inline-flex;
        align-items: center;
        gap: 3px;
        background: #fee2e2;
        color: #dc2626;
        font-weight: 700;
        font-size: 0.72rem;
        padding: 1px 6px;
        border-radius: 20px;
    }

    .price-after-discount {
        color: #d97706;
        font-weight: 800;
        font-size: 0.82rem;
    }

    .price-cell {
        line-height: 1.4;
    }

    .price-original-cell {
        color: #16a34a;
        font-weight: 600;
        font-size: 0.82rem;
        text-decoration: line-through;
        opacity: 0.85;
    }

    .price-diskon-cell {
        display: inline-flex;
        align-items: center;
        gap: 3px;
        color: #dc2626;
        font-weight: 600;
        font-size: 0.78rem;
        background: #fee2e2;
        padding: 1px 6px;
        border-radius: 20px;
        margin: 2px 0;
    }

    .price-final-cell {
        color: #d97706;
        font-weight: 800;
        font-size: 0.9rem;
    }

    .subtotal-original-cell {
        color: #16a34a;
        font-weight: 600;
        font-size: 0.82rem;
        text-decoration: line-through;
        opacity: 0.85;
    }

    .subtotal-final-cell {
        color: #d97706;
        font-weight: 800;
        font-size: 0.95rem;
    }

    .badge-diskon-aktif {
        display: inline-flex;
        align-items: center;
        gap: 3px;
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: white;
        font-size: 0.65rem;
        font-weight: 700;
        padding: 2px 7px;
        border-radius: 20px;
        letter-spacing: 0.3px;
    }

    .badge-hemat {
        display: inline-flex;
        align-items: center;
        gap: 3px;
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: white;
        font-size: 0.72rem;
        font-weight: 700;
        padding: 3px 10px;
        border-radius: 20px;
    }

    /* ========== DRAFT MODAL ========== */
    .draft-item {
        background: #f8fafc;
        transition: background 0.2s;
    }

    .draft-item:hover {
        background: #f1f5f9;
    }

    .draft-icon {
        width: 42px;
        height: 42px;
        border-radius: 10px;
        background: linear-gradient(135deg, #64748b 0%, #475569 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
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
                activeId: <?= session()->get('active_penjualan_id') ?? 'null' ?>,
                mode: 'scan',
                isPaymentModalOpen: false,

                // Customer
                searchCustomer: '',
                customerResults: [],
                selectedCustomer: null,
                customerFocused: false,

                // Product
                searchProduct: '',
                productResults: [],
                productFocused: false,

                // Barcode
                barcode: '',
                barcodeTimeout: null,

                // Cart
                cart: [],
                total: 0,

                // Payment
                jenisBayar: 'tunai',
                diskon: 0,
                bayar: 0,

                // Toast
                toasts: [],
                toastId: 0,

                // Confirm
                confirmMessage: '',
                confirmCallback: null,

                // Modal instances
                modalPembayaran: null,
                modalConfirm: null,
                modalDraft: null,

                // Draft
                draftList: [],
                draftCount: 0,
                draftLoading: false
            }
        },

        computed: {
            grandTotal() {
                return Math.max(0, this.total - this.diskon);
            },
            kembalian() {
                return this.bayar - this.grandTotal;
            },
            totalDiskonItem() {
                return this.cart.reduce((sum, item) => {
                    return item.nominal_diskon > 0 ? sum + (item.nominal_diskon * item.qty) : sum;
                }, 0);
            }
        },

        methods: {
            handleShortcut(e) {
                if (this.isPaymentModalOpen) {
                    if (e.altKey && e.key === '2') {
                        e.preventDefault();
                        this.$refs.inputDiskon?.focus();
                        return;
                    }
                    if (e.altKey && e.key === '3') {
                        e.preventDefault();
                        this.$refs.inputBayar?.focus();
                        return;
                    }
                    if (e.altKey && e.key === '1') {
                        e.preventDefault();
                        const sel = this.$refs.selectJenisBayar;
                        if (sel) {
                            sel.focus();
                            sel.dispatchEvent(new KeyboardEvent('keydown', {
                                key: 'ArrowDown',
                                code: 'ArrowDown',
                                keyCode: 40,
                                which: 40,
                                bubbles: true
                            }));
                        }
                        return;
                    }
                    if (e.altKey && e.key === 'Enter') {
                        e.preventDefault();
                        if (this.kembalian >= 0 && this.bayar > 0) this.$refs.btnSubmit?.click();
                        return;
                    }
                    if (e.key === 'Escape') {
                        e.preventDefault();
                        this.modalPembayaran.hide();
                        return;
                    }
                }

                if (e.altKey) {
                    switch (e.key.toLowerCase()) {
                        case 'c':
                            e.preventDefault();
                            this.$refs.customerSearch?.focus();
                            break;
                        case 'b':
                            e.preventDefault();
                            this.mode = 'manual';
                            this.$nextTick(() => this.$refs.barangSearch?.focus());
                            break;
                        case 's':
                            e.preventDefault();
                            this.mode = 'scan';
                            this.$nextTick(() => this.$refs.barcode?.focus());
                            break;
                        case 'p':
                            e.preventDefault();
                            if (this.cart.length > 0) this.openModalPembayaran();
                            break;
                        case 'm':
                            e.preventDefault();
                            this.mode = this.mode === 'scan' ? 'manual' : 'scan';
                            break;
                        case 'd':
                            e.preventDefault();
                            this.openDraftModal();
                            break;
                    }
                }

                if (e.ctrlKey && e.key === '/') {
                    e.preventDefault();
                    this.mode = 'scan';
                    this.$nextTick(() => this.$refs.barcode?.focus());
                }

                if (e.key === 'Escape') {
                    this.barcode = '';
                }
            },

            // ========== TOAST ==========
            showToast(message, type = 'info') {
                const icons = {
                    success: 'check-circle',
                    error: 'x-circle',
                    info: 'info',
                    warning: 'alert-triangle'
                };
                const toast = {
                    id: this.toastId++,
                    message,
                    type,
                    icon: icons[type]
                };
                this.toasts.push(toast);
                setTimeout(() => this.removeToast(0), 4000);
                this.$nextTick(() => lucide.createIcons());
            },
            removeToast(index) {
                this.toasts.splice(index, 1);
            },

            // ========== CONFIRM ==========
            confirmDelete(id) {
                this.confirmMessage = 'Apakah Anda yakin ingin menghapus item ini?';
                this.confirmCallback = () => this.deleteItem(id);
                this.modalConfirm.show();
            },
            confirmAction() {
                if (this.confirmCallback) {
                    this.confirmCallback();
                    this.confirmCallback = null;
                }
            },

            // ========== TRANSAKSI ==========
            async mulaiTransaksi() {
                try {
                    const res = await axios.post('<?= base_url('penjualan/mulai') ?>');
                    this.activeId = res.data.penjualan_id;
                    this.showToast('Transaksi dimulai!', 'success');
                    this.loadCart();
                    this.refreshDraftCount();
                } catch (err) {
                    this.showToast(err.response?.data?.message || 'Gagal memulai transaksi', 'error');
                }
            },

            // ========== CUSTOMER ==========
            async searchCust() {
                if (this.searchCustomer.length < 2) {
                    this.customerResults = [];
                    return;
                }
                try {
                    const res = await axios.get('<?= base_url('penjualan/customers') ?>');
                    this.customerResults = res.data.filter(c =>
                        c.nama.toLowerCase().includes(this.searchCustomer.toLowerCase()));
                } catch (err) {
                    console.error(err);
                }
            },
            selectCustomer(c) {
                this.selectedCustomer = c;
                this.customerResults = [];
                this.searchCustomer = c.nama;
                this.customerFocused = false;
            },

            // ========== PRODUCT SEARCH ==========
            async searchProd() {
                if (this.searchProduct.length < 2) {
                    this.productResults = [];
                    return;
                }
                try {
                    const res = await axios.get('<?= base_url('penjualan/search') ?>?q=' + this.searchProduct);
                    this.productResults = res.data;
                    this.$nextTick(() => lucide.createIcons());
                } catch (err) {
                    console.error(err);
                }
            },

            // ========== ADD ITEM ==========
            async addItem(id) {
                try {
                    await axios.post('<?= base_url('penjualan/add-item') ?>', {
                        inventory_id: id,
                        qty: 1
                    });
                    this.searchProduct = '';
                    this.productResults = [];
                    this.productFocused = false;
                    this.showToast('Item ditambahkan ke keranjang', 'success');
                    this.loadCart();
                } catch (err) {
                    this.showToast(err.response?.data?.message || 'Gagal menambah item', 'error');
                }
            },

            // ========== SCAN BARCODE ==========
            onBarcodePaste() {
                setTimeout(() => {
                    if (this.barcode?.trim().length > 0) this.scanBarcode();
                }, 50);
            },
            onBarcodeInput() {
                if (this.barcodeTimeout) clearTimeout(this.barcodeTimeout);
                this.barcodeTimeout = setTimeout(() => {
                    if (this.barcode.trim().length > 0) this.scanBarcode();
                }, 100);
            },
            async scanBarcode() {
                if (!this.barcode.trim()) return;
                if (this.barcodeTimeout) {
                    clearTimeout(this.barcodeTimeout);
                    this.barcodeTimeout = null;
                }
                try {
                    await axios.get('<?= base_url('penjualan/scan') ?>?barcode=' + this.barcode);
                    this.barcode = '';
                    this.showToast('Item berhasil di-scan!', 'success');
                    this.loadCart();
                    setTimeout(() => this.$refs.barcode?.focus(), 100);
                } catch (err) {
                    this.showToast(err.response?.data?.message || 'Barcode tidak ditemukan', 'error');
                    this.barcode = '';
                    this.$refs.barcode?.focus();
                }
            },

            // ========== CART ==========
            async loadCart() {
                try {
                    const res = await axios.get('<?= base_url('penjualan/detail') ?>');
                    this.cart = res.data.items;
                    this.total = res.data.total;
                    this.$nextTick(() => lucide.createIcons());
                } catch (err) {
                    console.error(err);
                }
            },
            async updateQty(id, qty) {
                if (qty < 1) {
                    this.showToast('Qty minimal 1', 'warning');
                    this.loadCart();
                    return;
                }
                try {
                    const res = await axios.post(`<?= base_url('penjualan/update-qty') ?>/${id}`, {
                        qty
                    });
                    this.total = res.data.total;
                    this.showToast('Qty diupdate', 'success');
                    this.loadCart();
                } catch (err) {
                    this.showToast(err.response?.data?.message || 'Gagal update qty', 'error');
                    this.loadCart();
                }
            },
            async deleteItem(id) {
                try {
                    const res = await axios.post(`<?= base_url('penjualan/delete-item') ?>/${id}`);
                    this.total = res.data.total;
                    this.showToast('Item dihapus', 'success');
                    this.loadCart();
                } catch (err) {
                    this.showToast(err.response?.data?.message || 'Gagal hapus item', 'error');
                }
            },

            // ========== DRAFT ==========
            async refreshDraftCount() {
                try {
                    const res = await axios.get('<?= base_url('penjualan/list-draft') ?>');
                    this.draftCount = (res.data.data ?? []).length;
                } catch (_) {}
            },

            async openDraftModal() {
                this.draftLoading = true;
                this.modalDraft.show();
                try {
                    const res = await axios.get('<?= base_url('penjualan/list-draft') ?>');
                    this.draftList = res.data.data ?? [];
                    this.draftCount = this.draftList.length;
                } catch (err) {
                    this.showToast('Gagal memuat draft', 'error');
                } finally {
                    this.draftLoading = false;
                    this.$nextTick(() => lucide.createIcons());
                }
            },

            async teruskanDraft(id) {
                try {
                    await axios.post('<?= base_url('penjualan/teruskan-draft') ?>', {
                        id
                    });
                    this.activeId = id;
                    this.modalDraft.hide();
                    this.loadCart();
                    this.showToast('Draft #' + id + ' dilanjutkan', 'success');
                } catch (err) {
                    this.showToast(err.response?.data?.message || 'Gagal melanjutkan draft', 'error');
                }
            },

            async hapusDraft(id) {
                if (!confirm('Hapus draft #' + id + '? Semua item akan dikembalikan ke stok.')) return;
                try {
                    await axios.delete('<?= base_url('penjualan/hapus-draft') ?>/' + id);
                    this.draftList = this.draftList.filter(d => d.id !== id);
                    this.draftCount = this.draftList.length;
                    if (this.activeId == id) {
                        this.activeId = null;
                        this.cart = [];
                        this.total = 0;
                    }
                    this.showToast('Draft #' + id + ' dihapus', 'success');
                    this.$nextTick(() => lucide.createIcons());
                } catch (err) {
                    this.showToast(err.response?.data?.message || 'Gagal hapus draft', 'error');
                }
            },

            // ========== PAYMENT ==========
            openModalPembayaran() {
                this.bayar = this.grandTotal;
                this.modalPembayaran.show();
            },
            hitungKembalian() {
                /* computed */ },

            // ========== HELPER ==========
            format(x) {
                return new Intl.NumberFormat('id-ID').format(x ?? 0);
            }
        },

        mounted() {
            const modalEl = document.getElementById('modalPembayaran');
            modalEl.addEventListener('shown.bs.modal', () => {
                this.isPaymentModalOpen = true;
                this.$nextTick(() => this.$refs.inputBayar?.focus());
            });
            modalEl.addEventListener('hidden.bs.modal', () => {
                this.isPaymentModalOpen = false;
            });

            this.modalPembayaran = new bootstrap.Modal(document.getElementById('modalPembayaran'));
            this.modalConfirm = new bootstrap.Modal(document.getElementById('confirmDialog'));
            this.modalDraft = new bootstrap.Modal(document.getElementById('modalDraft'));

            if (this.activeId) this.loadCart();
            this.refreshDraftCount();

            this.mode = 'scan';
            this.$nextTick(() => this.$refs.barcode?.focus());

            <?php if (session()->getFlashdata('cetak')): ?>
                window.open("<?= session()->getFlashdata('cetak') ?>", "_blank");
            <?php endif; ?>

            lucide.createIcons();
            window.addEventListener('keydown', this.handleShortcut, true);
        },

        beforeUnmount() {
            window.removeEventListener('keydown', this.handleShortcut, true);
            if (this.barcodeTimeout) clearTimeout(this.barcodeTimeout);
        }

    }).mount('#app');
</script>
<?= $this->endSection() ?>