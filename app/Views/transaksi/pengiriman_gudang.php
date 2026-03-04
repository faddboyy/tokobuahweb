<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<div id="app" v-cloak class="container-fluid py-4">

    <!-- ══ HEADER ════════════════════════════════════════════════════════════ -->
    <div class="glass-panel p-4 mb-4 border-0 shadow-sm"
        style="border-radius:20px;background:linear-gradient(145deg,#ffffff,#f8f9fa)">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-warning bg-opacity-10 p-3 rounded-4 text-warning">
                        <i data-lucide="send" style="width:32px;height:32px"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-0 text-dark">Pengiriman Gudang ke Toko</h4>
                        <p class="text-muted small mb-0">Kirim barang dari gudang ke toko / cabang tujuan</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <div v-if="gudangInfo" class="d-inline-flex align-items-center gap-2
                     bg-warning bg-opacity-10 border border-warning border-opacity-25
                     rounded-3 px-3 py-2">
                    <i data-lucide="warehouse" class="text-warning" style="width:15px"></i>
                    <span class="fw-bold small text-warning">{{ gudangInfo }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- ══ PANEL UTAMA ═══════════════════════════════════════════════════════ -->
    <div class="row g-4">

        <!-- ── KOLOM KIRI ────────────────────────────────────────────────── -->
        <div class="col-lg-4">

            <!-- Pilih Toko -->
            <div class="glass-panel p-4 mb-4 border-0 shadow-sm bg-white" style="border-radius:16px">
                <h6 class="fw-bold mb-3 d-flex align-items-center gap-2">
                    <i data-lucide="store" style="width:16px" class="text-warning"></i>
                    Toko Tujuan
                </h6>
                <select class="form-select form-select-sm rounded-3 border fw-medium"
                    v-model="cabang_id" :disabled="submitted">
                    <option value="">— Pilih Toko —</option>
                    <option v-for="c in cabangList" :key="c.id" :value="c.id">{{ c.nama }}</option>
                </select>
                <div v-if="!cabang_id && triedSubmit"
                    class="text-danger small mt-1 d-flex align-items-center gap-1">
                    <i data-lucide="alert-circle" style="width:12px"></i> Toko tujuan wajib dipilih
                </div>
            </div>

            <!-- Cari Barang -->
            <div class="glass-panel p-4 border-0 shadow-sm bg-white position-relative" style="border-radius:16px">
                <h6 class="fw-bold mb-3 d-flex align-items-center gap-2">
                    <i data-lucide="search" style="width:16px" class="text-primary"></i>
                    Cari Barang
                </h6>

                <div class="input-group input-group-sm rounded-3 overflow-hidden border mb-2">
                    <span class="input-group-text bg-light border-0">
                        <i data-lucide="search" style="width:14px"></i>
                    </span>
                    <input type="text" class="form-control border-0 shadow-none"
                        placeholder="Nama barang atau barcode..."
                        v-model="searchQ" @input="onSearch"
                        :disabled="submitted" autocomplete="off">
                    <span v-if="searching" class="input-group-text bg-light border-0">
                        <span class="spinner-border spinner-border-sm text-muted"></span>
                    </span>
                </div>

                <div v-if="searchResults.length" class="search-dropdown shadow border rounded-3 bg-white">
                    <div v-for="b in searchResults" :key="b.barang_id"
                        class="search-item d-flex justify-content-between align-items-center px-3 py-2"
                        @click="pilihBarang(b)">
                        <div>
                            <div class="fw-semibold small text-dark">{{ b.nama_barang }}</div>
                            <div class="text-muted" style="font-size:.72rem">
                                Stok: <b class="text-success">{{ b.stock }}</b> {{ b.nama_satuan }}
                            </div>
                        </div>
                        <i data-lucide="plus-circle" style="width:16px" class="text-primary flex-shrink-0"></i>
                    </div>
                </div>
                <div v-else-if="searchQ.length > 1 && !searching"
                    class="text-muted small text-center py-2">
                    Tidak ada barang ditemukan
                </div>
            </div>
        </div>

        <!-- ── KOLOM KANAN ───────────────────────────────────────────────── -->
        <div class="col-lg-8">
            <div class="glass-panel border-0 shadow-sm bg-white overflow-hidden" style="border-radius:20px">

                <div class="px-4 pt-3 pb-2 d-flex justify-content-between align-items-center border-bottom">
                    <h6 class="fw-bold mb-0 d-flex align-items-center gap-2">
                        <i data-lucide="list" style="width:17px"></i>
                        Daftar Item Pengiriman
                        <span class="badge bg-primary rounded-pill ms-1">{{ items.length }}</span>
                    </h6>
                    <button v-if="items.length && !submitted"
                        class="btn btn-sm btn-outline-danger rounded-3 px-3" @click="resetItems">
                        <i data-lucide="trash-2" style="width:13px" class="me-1"></i> Kosongkan
                    </button>
                </div>

                <div class="table-responsive" style="min-height:200px">
                    <table class="table table-hover align-middle mb-0">
                        <thead style="background:#f8fafc">
                            <tr class="text-muted">
                                <th class="ps-4 py-3 border-0 small text-uppercase fw-bolder">#</th>
                                <th class="py-3 border-0 small text-uppercase fw-bolder">Nama Barang</th>
                                <th class="py-3 border-0 small text-uppercase fw-bolder text-center">Stok</th>
                                <th class="py-3 border-0 small text-uppercase fw-bolder text-center">Qty Kirim</th>
                                <th class="py-3 border-0 small text-uppercase fw-bolder text-center">Satuan</th>
                                <th class="pe-4 py-3 border-0 small text-uppercase fw-bolder text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="!items.length">
                                <td colspan="6" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center" style="opacity:.35">
                                        <i data-lucide="package-open" style="width:48px;height:48px" class="mb-2"></i>
                                        <p class="fw-bold mb-0 small">Cari dan tambahkan barang</p>
                                    </div>
                                </td>
                            </tr>
                            <tr v-else v-for="(item, idx) in items" :key="item.barang_id"
                                class="border-bottom border-light row-item"
                                :class="{ 'table-danger': item.qty > item.stock }">

                                <td class="ps-4 text-muted small">{{ idx + 1 }}</td>
                                <td>
                                    <div class="fw-bold text-dark small">{{ item.nama_barang }}</div>
                                    <div class="text-muted font-mono" style="font-size:.7rem">{{ item.barcode }}</div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark border fw-bold" style="font-size:.72rem">
                                        {{ item.stock }}
                                    </span>
                                </td>

                                <!-- Qty: normal / edit -->
                                <td class="text-center" style="width:160px">
                                    <div v-if="editIdx !== idx || submitted"
                                        class="d-flex align-items-center justify-content-center gap-1">
                                        <span class="badge bg-white text-dark border fw-bold px-2"
                                            style="font-size:.82rem">{{ item.qty }}</span>
                                        <button v-if="!submitted"
                                            class="btn btn-xs btn-light border rounded-2 p-1 ms-1"
                                            @click="startEdit(idx)" title="Edit qty">
                                            <i data-lucide="pencil" style="width:11px"></i>
                                        </button>
                                    </div>
                                    <div v-else class="d-flex align-items-center justify-content-center gap-1">
                                        <button class="btn btn-sm btn-light border rounded-2 px-2 py-0 fw-bold"
                                            @click="adjustQty(item, -1)" :disabled="item.editQty <= 1">−</button>
                                        <input type="number"
                                            class="form-control form-control-sm text-center border rounded-2 fw-bold"
                                            style="width:58px;font-size:.85rem"
                                            v-model.number="item.editQty" :max="item.stock" min="1"
                                            @keyup.enter="confirmEdit(idx)" @keyup.esc="cancelEdit(idx)">
                                        <button class="btn btn-sm btn-light border rounded-2 px-2 py-0 fw-bold"
                                            @click="adjustQty(item, 1)" :disabled="item.editQty >= item.stock">+</button>
                                        <button class="btn btn-sm btn-success rounded-2 px-2 py-0 ms-1"
                                            @click="confirmEdit(idx)" title="Simpan">
                                            <i data-lucide="check" style="width:11px"></i>
                                        </button>
                                        <button class="btn btn-sm btn-light border rounded-2 px-2 py-0"
                                            @click="cancelEdit(idx)" title="Batal">
                                            <i data-lucide="x" style="width:11px"></i>
                                        </button>
                                    </div>
                                    <div v-if="item.qty > item.stock" class="text-danger" style="font-size:.65rem">
                                        Melebihi stok!
                                    </div>
                                </td>

                                <td class="text-center">
                                    <span class="badge bg-light text-dark border" style="font-size:.72rem">
                                        {{ item.nama_satuan }}
                                    </span>
                                </td>
                                <td class="text-center pe-4">
                                    <button class="btn btn-sm btn-outline-danger rounded-3 px-2 py-1"
                                        @click="hapusItem(idx)" :disabled="submitted || editIdx === idx">
                                        <i data-lucide="x" style="width:13px"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Footer -->
                <div class="p-4 bg-light border-top d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <p class="small text-muted mb-0 fw-medium">
                        <b>{{ items.length }}</b> jenis &bull; Total qty: <b>{{ totalQty }}</b>
                    </p>
                    <div v-if="submitted" class="d-flex align-items-center gap-2 flex-wrap">
                        <div class="d-flex align-items-center gap-2 text-success fw-bold">
                            <i data-lucide="check-circle" style="width:18px"></i>{{ lastKode }}
                        </div>
                        <a :href="cetakUrl" target="_blank"
                            class="btn btn-sm btn-outline-dark rounded-3 px-3 fw-semibold d-flex align-items-center gap-1">
                            <i data-lucide="printer" style="width:13px"></i> Cetak Surat
                        </a>
                        <button class="btn btn-sm btn-outline-secondary rounded-3 px-3" @click="resetAll">
                            <i data-lucide="plus" style="width:13px" class="me-1"></i> Pengiriman Baru
                        </button>
                    </div>
                    <button v-else
                        class="btn btn-warning fw-bold rounded-3 px-4 shadow-sm d-flex align-items-center gap-2"
                        @click="simpan" :disabled="saving || !canSubmit" :title="submitHint">
                        <span v-if="saving" class="spinner-border spinner-border-sm"></span>
                        <i v-else data-lucide="send" style="width:16px"></i>
                        <span>{{ saving ? 'Menyimpan...' : 'Kirim Sekarang' }}</span>
                    </button>
                </div>

            </div>
        </div>
    </div>

</div>

<!-- Toast -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index:9999">
    <div id="toastMsg" class="toast align-items-center border-0 shadow"
        :class="toastType === 'success' ? 'bg-success text-white' : 'bg-danger text-white'"
        role="alert" aria-live="assertive">
        <div class="d-flex">
            <div class="toast-body fw-semibold">{{ toastMsg }}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
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

    .row-item {
        transition: background .12s;
    }

    .row-item:hover {
        background: #f8fafc !important;
    }

    .btn-xs {
        font-size: .72rem;
        line-height: 1;
    }

    .search-dropdown {
        position: absolute;
        left: 1rem;
        right: 1rem;
        z-index: 999;
        max-height: 280px;
        overflow-y: auto;
    }

    .search-item {
        cursor: pointer;
        transition: background .1s;
        border-bottom: 1px solid #f1f5f9;
    }

    .search-item:last-child {
        border-bottom: none;
    }

    .search-item:hover {
        background: #f0f9ff;
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
                cabangList: [],
                cabang_id: '',
                searchQ: '',
                searchResults: [],
                searching: false,
                searchTimer: null,
                gudangInfo: '',
                items: [],
                editIdx: null,
                saving: false,
                submitted: false,
                triedSubmit: false,
                lastKode: '',
                lastId: null,
                toastMsg: '',
                toastType: 'success',
                _storageKey: 'pengiriman_gudang_draft',
            };
        },

        watch: {
            cabang_id() {
                this.persist();
            },
            submitted() {
                this.persist();
            },
            lastKode() {
                this.persist();
            },
            lastId() {
                this.persist();
            },
            items: {
                deep: true,
                handler() {
                    this.persist();
                }
            },
        },

        computed: {
            totalQty() {
                return this.items.reduce((s, i) => s + (parseFloat(i.qty) || 0), 0);
            },
            hasOverstock() {
                return this.items.some(i => i.qty > i.stock);
            },
            isEditing() {
                return this.editIdx !== null;
            },
            canSubmit() {
                return this.cabang_id !== '' &&
                    this.items.length > 0 &&
                    !this.hasOverstock &&
                    !this.isEditing;
            },
            submitHint() {
                if (!this.cabang_id) return 'Pilih toko tujuan terlebih dahulu';
                if (!this.items.length) return 'Tambahkan minimal 1 barang';
                if (this.hasOverstock) return 'Ada qty yang melebihi stok gudang';
                if (this.isEditing) return 'Selesaikan edit qty terlebih dahulu';
                return '';
            },
            cetakUrl() {
                return `<?= base_url("pengiriman-gudang/cetak") ?>/${this.lastId}`;
            },
        },

        methods: {
            persist() {
                try {
                    sessionStorage.setItem(this._storageKey, JSON.stringify({
                        cabang_id: this.cabang_id,
                        items: this.items.map(i => ({
                            barang_id: i.barang_id,
                            nama_barang: i.nama_barang,
                            barcode: i.barcode,
                            stock: i.stock,
                            satuan_id: i.satuan_id,
                            nama_satuan: i.nama_satuan,
                            qty: i.qty,
                            editQty: i.qty,
                        })),
                        submitted: this.submitted,
                        lastKode: this.lastKode,
                        lastId: this.lastId,
                    }));
                } catch (_) {}
            },
            restoreDraft() {
                try {
                    const raw = sessionStorage.getItem(this._storageKey);
                    if (!raw) return;
                    const d = JSON.parse(raw);
                    if (d.cabang_id) this.cabang_id = d.cabang_id;
                    if (d.items && d.items.length) this.items = d.items;
                    if (d.submitted) {
                        this.submitted = true;
                        this.lastKode = d.lastKode || '';
                        this.lastId = d.lastId || null;
                    }
                } catch (_) {}
            },
            clearDraft() {
                try {
                    sessionStorage.removeItem(this._storageKey);
                } catch (_) {}
            },

            async loadCabang() {
                try {
                    const res = await axios.get('<?= base_url("pengiriman-gudang/get-cabang") ?>');
                    this.cabangList = res.data.cabang;
                } catch (e) {
                    console.error(e);
                }
            },

            onSearch() {
                clearTimeout(this.searchTimer);
                if (this.searchQ.length < 2) {
                    this.searchResults = [];
                    return;
                }
                this.searching = true;
                this.searchTimer = setTimeout(() => this.doSearch(), 300);
            },
            async doSearch() {
                try {
                    const res = await axios.get('<?= base_url("pengiriman-gudang/search-barang") ?>', {
                        params: {
                            q: this.searchQ
                        },
                    });
                    const sudah = new Set(this.items.map(i => i.barang_id));
                    this.searchResults = res.data.results.filter(r => !sudah.has(r.barang_id));
                    if (!this.gudangInfo && res.data.gudang_id)
                        this.gudangInfo = 'Gudang #' + res.data.gudang_id;
                    this.$nextTick(() => lucide.createIcons());
                } catch (e) {
                    console.error(e);
                } finally {
                    this.searching = false;
                }
            },

            pilihBarang(b) {
                this.items.push({
                    barang_id: b.barang_id,
                    nama_barang: b.nama_barang,
                    barcode: b.barcode || '',
                    stock: b.stock,
                    satuan_id: b.satuan_id,
                    nama_satuan: b.nama_satuan,
                    qty: 1,
                    editQty: 1,
                });
                this.searchQ = '';
                this.searchResults = [];
                this.$nextTick(() => lucide.createIcons());
            },

            startEdit(idx) {
                if (this.editIdx !== null && this.editIdx !== idx) this.cancelEdit(this.editIdx);
                this.items[idx].editQty = this.items[idx].qty;
                this.editIdx = idx;
                this.$nextTick(() => lucide.createIcons());
            },
            confirmEdit(idx) {
                const item = this.items[idx];
                let val = parseFloat(item.editQty);
                if (!val || val < 1) val = 1;
                if (val > item.stock) val = item.stock;
                item.qty = item.editQty = val;
                this.editIdx = null;
                this.$nextTick(() => lucide.createIcons());
            },
            cancelEdit(idx) {
                this.items[idx].editQty = this.items[idx].qty;
                this.editIdx = null;
                this.$nextTick(() => lucide.createIcons());
            },
            adjustQty(item, delta) {
                const next = parseFloat(item.editQty) + delta;
                if (next >= 1 && next <= item.stock) item.editQty = next;
            },

            hapusItem(idx) {
                if (this.editIdx === idx) this.editIdx = null;
                this.items.splice(idx, 1);
            },
            resetItems() {
                this.items = [];
                this.editIdx = null;
            },

            async simpan() {
                this.triedSubmit = true;
                if (!this.canSubmit) {
                    this.showToast(this.submitHint || 'Data belum lengkap', 'error');
                    return;
                }
                this.saving = true;
                try {
                    const res = await axios.post('<?= base_url("pengiriman-gudang/simpan") ?>', {
                        cabang_id: this.cabang_id,
                        items: this.items.map(i => ({
                            barang_id: i.barang_id,
                            nama_barang: i.nama_barang,
                            qty: i.qty,
                            satuan_id: i.satuan_id,
                        })),
                    });
                    this.lastKode = res.data.kode_pengiriman;
                    this.lastId = res.data.pengiriman_id;
                    this.submitted = true;
                    this.showToast('Pengiriman ' + this.lastKode + ' berhasil!', 'success');
                    this.$nextTick(() => lucide.createIcons());
                } catch (e) {
                    this.showToast(e.response?.data?.message ?? 'Terjadi kesalahan', 'error');
                } finally {
                    this.saving = false;
                }
            },

            resetAll() {
                this.cabang_id = '';
                this.items = [];
                this.editIdx = null;
                this.searchQ = '';
                this.searchResults = [];
                this.submitted = false;
                this.triedSubmit = false;
                this.lastKode = '';
                this.lastId = null;
                this.clearDraft();
                this.$nextTick(() => lucide.createIcons());
            },

            showToast(msg, type = 'success') {
                this.toastMsg = msg;
                this.toastType = type;
                this.$nextTick(() => {
                    const el = document.getElementById('toastMsg');
                    if (el) bootstrap.Toast.getOrCreateInstance(el, {
                        delay: 4000
                    }).show();
                });
            },
        },

        mounted() {
            this.loadCabang();
            this.restoreDraft();
            lucide.createIcons();
        },
        updated() {
            lucide.createIcons();
        },
    }).mount('#app');
</script>
<?= $this->endSection() ?>