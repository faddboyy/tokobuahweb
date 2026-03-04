<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<div id="app" v-cloak class="container-fluid py-4">

    <!-- ══ HEADER ══════════════════════════════════════════════════════════ -->
    <div class="glass-panel p-4 mb-4 border-0 shadow-sm"
        style="border-radius:20px; background:linear-gradient(145deg,#ffffff,#f8f9fa)">
        <div class="d-flex align-items-center gap-3">
            <div class="bg-success bg-opacity-10 p-3 rounded-4 text-success">
                <i data-lucide="package-check" style="width:32px;height:32px"></i>
            </div>
            <div>
                <h4 class="fw-bold mb-0 text-dark">Input Barang Masuk</h4>
                <p class="text-muted small mb-0">Scan kode pengiriman dari gudang untuk memproses penerimaan toko</p>
            </div>
        </div>
    </div>

    <!-- ══ SCAN PANEL ══════════════════════════════════════════════════════ -->
    <div class="glass-panel p-4 mb-4 border-0 shadow-sm bg-white" style="border-radius:16px">
        <label class="small fw-bold text-muted mb-2 d-flex align-items-center gap-2">
            <i data-lucide="scan-line" style="width:14px"></i>
            Kode Pengiriman
        </label>
        <div class="d-flex gap-2">
            <div class="input-group rounded-3 overflow-hidden border"
                :class="scanError ? 'border-danger' : 'border-secondary border-opacity-25'">
                <span class="input-group-text bg-light border-0">
                    <i data-lucide="qr-code" style="width:16px" class="text-muted"></i>
                </span>
                <input
                    ref="scanInput"
                    type="text"
                    class="form-control border-0 shadow-none fw-mono"
                    style="font-family:'Courier New',monospace; font-size:.95rem; letter-spacing:.5px"
                    placeholder="Scan atau ketik kode pengiriman... (contoh: KRG-20260301234345)"
                    v-model="kodeInput"
                    @keyup.enter="doScan"
                    :disabled="scanning || !!pengiriman">
            </div>
            <button class="btn btn-success px-4 fw-bold rounded-3 shadow-sm d-flex align-items-center gap-2"
                @click="doScan" :disabled="scanning || !!pengiriman || !kodeInput.trim()">
                <span v-if="!scanning">
                    <i data-lucide="search" style="width:15px" class="me-1"></i> Cari
                </span>
                <span v-else class="spinner-border spinner-border-sm"></span>
            </button>
            <button v-if="pengiriman" class="btn btn-outline-secondary px-3 rounded-3 fw-semibold"
                @click="reset" title="Ganti pengiriman">
                <i data-lucide="x" style="width:15px"></i>
            </button>
        </div>
        <!-- Error scan -->
        <div v-if="scanError" class="mt-2 d-flex align-items-center gap-2 text-danger small fw-semibold">
            <i data-lucide="alert-circle" style="width:14px"></i>
            {{ scanError }}
        </div>
    </div>

    <!-- ══ LOADING SCAN ══════════════════════════════════════════════════════ -->
    <div v-if="scanning" class="text-center py-5">
        <div class="spinner-border text-success mb-2" role="status"></div>
        <p class="text-muted small mb-0">Mencari data pengiriman...</p>
    </div>

    <!-- ══ HASIL SCAN ══════════════════════════════════════════════════════ -->
    <template v-if="pengiriman && !scanning">

        <!-- Info Cards -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="info-card h-100">
                    <div class="info-card-label">
                        <i data-lucide="send" style="width:13px"></i> Kode Pengiriman
                    </div>
                    <div class="info-card-value font-mono text-success">{{ pengiriman.kode_pengiriman }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-card h-100">
                    <div class="info-card-label">
                        <i data-lucide="warehouse" style="width:13px"></i> Dari Gudang
                    </div>
                    <div class="info-card-value">{{ pengiriman.nama_gudang || '-' }}</div>
                    <div class="info-card-sub">
                        <i data-lucide="user" style="width:10px" class="me-1"></i>{{ pengiriman.nama_operator || '-' }}
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-card h-100">
                    <div class="info-card-label">
                        <i data-lucide="store" style="width:13px"></i> Tujuan Cabang
                    </div>
                    <div class="info-card-value">{{ pengiriman.nama_cabang || '-' }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-card h-100">
                    <div class="info-card-label">
                        <i data-lucide="clock" style="width:13px"></i> Waktu Pengiriman
                    </div>
                    <div class="info-card-value" style="font-size:.85rem">{{ formatDate(pengiriman.waktu_pengiriman) }}</div>
                    <div class="ps-0 mt-1">
                        <span class="badge rounded-pill px-2 py-1 bg-warning bg-opacity-10
                                     text-warning border border-warning border-opacity-25 fw-semibold"
                            style="font-size:.68rem">
                            <i data-lucide="truck" style="width:10px" class="me-1"></i>Dikirim
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- ══ TABEL ITEM ═══════════════════════════════════════════════════ -->
        <div class="glass-panel border-0 shadow-sm bg-white overflow-hidden mb-4" style="border-radius:20px">

            <!-- Table Header Bar -->
            <div class="item-header px-4 py-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center gap-3">
                    <div class="item-header-icon">
                        <i data-lucide="package" style="width:18px"></i>
                    </div>
                    <div>
                        <div class="fw-bold text-white" style="font-size:1rem">
                            Daftar Item Pengiriman
                        </div>
                        <div class="text-white-50 small">
                            {{ items.length }} jenis barang
                            &bull; Isi qty aktual diterima dan selisih (jika ada)
                        </div>
                    </div>
                </div>
                <!-- Summary chips -->
                <div class="d-flex gap-2 flex-wrap">
                    <span class="summary-chip chip-ok">
                        <i data-lucide="check-circle" style="width:11px"></i>
                        Sesuai: {{ summaryCount.ok }}
                    </span>
                    <span v-if="summaryCount.lebih" class="summary-chip chip-lebih">
                        <i data-lucide="arrow-up" style="width:11px"></i>
                        Lebih: {{ summaryCount.lebih }}
                    </span>
                    <span v-if="summaryCount.kurang" class="summary-chip chip-kurang">
                        <i data-lucide="arrow-down" style="width:11px"></i>
                        Kurang: {{ summaryCount.kurang }}
                    </span>
                    <span v-if="summaryCount.belum" class="summary-chip chip-belum">
                        <i data-lucide="clock" style="width:11px"></i>
                        Belum diisi: {{ summaryCount.belum }}
                    </span>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead style="background:#f8fafc">
                        <tr class="text-muted">
                            <th class="ps-4 py-3 border-0 small text-uppercase fw-bolder" style="width:36px">#</th>
                            <th class="py-3 border-0 small text-uppercase fw-bolder">Barang</th>

                            <!-- Surat Jalan / Kiriman -->
                            <th class="py-3 border-0 small text-uppercase fw-bolder text-center th-kirim" colspan="2">
                                <span class="d-flex align-items-center justify-content-center gap-1">
                                    <i data-lucide="send" style="width:12px"></i> Kiriman Gudang
                                </span>
                            </th>

                            <!-- Aktual diterima -->
                            <th class="py-3 border-0 small text-uppercase fw-bolder text-center th-aktual" colspan="2">
                                <span class="d-flex align-items-center justify-content-center gap-1">
                                    <i data-lucide="package-check" style="width:12px"></i> Aktual Diterima
                                </span>
                            </th>

                            <!-- Selisih -->
                            <th class="py-3 pe-4 border-0 small text-uppercase fw-bolder text-center th-selisih">
                                <span class="d-flex align-items-center justify-content-center gap-1">
                                    <i data-lucide="git-diff" style="width:12px"></i> Selisih
                                </span>
                            </th>
                        </tr>
                        <tr style="background:#f0f4f8" class="text-muted">
                            <th class="border-0 py-2 ps-4" colspan="2"></th>
                            <th class="border-0 py-2 text-center small fw-bold th-kirim" style="font-size:.7rem;opacity:.7">QTY</th>
                            <th class="border-0 py-2 text-center small fw-bold th-kirim" style="font-size:.7rem;opacity:.7">SATUAN</th>
                            <th class="border-0 py-2 text-center small fw-bold th-aktual" style="font-size:.7rem;opacity:.7">QTY MASUK</th>
                            <th class="border-0 py-2 text-center small fw-bold th-aktual" style="font-size:.7rem;opacity:.7">SATUAN SIMPAN</th>
                            <th class="border-0 py-2 text-center small fw-bold pe-4 th-selisih" style="font-size:.7rem;opacity:.7">NILAI</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(item, idx) in items" :key="item.id"
                            class="border-bottom border-light row-item"
                            :class="rowClass(item)">

                            <!-- No -->
                            <td class="ps-4 text-muted small">{{ idx + 1 }}</td>

                            <!-- Barang -->
                            <td class="py-3">
                                <div class="fw-bold text-dark">{{ item.nama_barang }}</div>
                                <div class="font-mono text-muted" style="font-size:.72rem">{{ item.barcode }}</div>
                            </td>

                            <!-- Qty Kiriman -->
                            <td class="py-3 text-center">
                                <span class="qty-badge qty-kirim">{{ item.qty_kiriman }}</span>
                            </td>

                            <!-- Satuan Kirim (dari stok_gudang) -->
                            <td class="py-3 text-center">
                                <span class="satuan-pill satuan-kirim-pill">{{ item.satuan_kirim || '-' }}</span>
                            </td>

                            <!-- Input: Qty Aktual -->
                            <td class="py-3 text-center">
                                <div class="d-flex align-items-center justify-content-center gap-1">
                                    <input
                                        type="number"
                                        min="0"
                                        step="0.01"
                                        class="form-control form-control-sm input-qty text-center fw-bold shadow-none"
                                        :class="inputQtyClass(item)"
                                        v-model.number="item.qty_aktual"
                                        @input="onQtyChange(item)"
                                        placeholder="0">
                                </div>
                            </td>

                            <!-- Satuan Simpan (dari barang.satuan_id) – read only label -->
                            <td class="py-3 text-center">
                                <span class="satuan-pill satuan-simpan-pill">{{ item.satuan_simpan || '-' }}</span>
                            </td>

                            <!-- Input: Selisih (manual, beda satuan) -->
                            <td class="py-3 text-center pe-4">
                                <div v-if="item.qty_aktual === null || item.qty_aktual === ''"
                                    class="text-muted small">—</div>
                                <div v-else>
                                    <input
                                        type="number"
                                        step="0.01"
                                        class="form-control form-control-sm input-selisih text-center fw-bold shadow-none"
                                        :class="inputSelisihClass(item)"
                                        v-model.number="item.selisih"
                                        placeholder="0">
                                    <!-- Badge preview selisih -->
                                    <div class="mt-1 text-center" v-if="item.selisih !== null && item.selisih !== ''">
                                        <span v-if="item.selisih == 0" class="selisih-chip chip-ok">
                                            <i data-lucide="check" style="width:10px"></i> Sesuai
                                        </span>
                                        <span v-else-if="item.selisih > 0" class="selisih-chip chip-lebih">
                                            +{{ item.selisih }}
                                        </span>
                                        <span v-else class="selisih-chip chip-kurang">
                                            {{ item.selisih }}
                                        </span>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ══ CATATAN & TOMBOL SIMPAN ══════════════════════════════════════ -->
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-2">
            <!-- Keterangan helper -->
            <div class="helper-box">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <i data-lucide="info" style="width:14px" class="text-primary"></i>
                    <span class="fw-bold small text-primary">Petunjuk Pengisian</span>
                </div>
                <ul class="mb-0 ps-3 text-muted small" style="line-height:1.8">
                    <li><b>Qty Masuk</b> — jumlah aktual barang yang diterima, dalam satuan simpan (toko)</li>
                    <li><b>Selisih</b> — isi manual karena satuan kiriman (gudang) bisa berbeda dengan satuan simpan (toko). Contoh: kiriman <i>1 Dus</i>, masuk <i>10 Kg</i>, selisih isi <i>0</i> jika sesuai konversi</li>
                    <li>Setelah disimpan, status pengiriman akan berubah menjadi <b>Diterima</b> dan stok toko akan bertambah</li>
                </ul>
            </div>

            <!-- Tombol Simpan -->
            <div class="d-flex flex-column align-items-end gap-2">
                <div v-if="saveError" class="text-danger small fw-semibold d-flex align-items-center gap-1">
                    <i data-lucide="alert-circle" style="width:13px"></i> {{ saveError }}
                </div>
                <div v-if="saveSuccess" class="text-success small fw-semibold d-flex align-items-center gap-1">
                    <i data-lucide="check-circle" style="width:13px"></i> {{ saveSuccess }}
                </div>
                <button
                    class="btn btn-success fw-bold px-5 py-2 rounded-3 shadow-sm d-flex align-items-center gap-2"
                    style="min-width:200px; justify-content:center"
                    @click="simpan"
                    :disabled="saving || !canSave">
                    <span v-if="!saving">
                        <i data-lucide="save" style="width:16px" class="me-1"></i>
                        Simpan Barang Masuk
                    </span>
                    <span v-else class="d-flex align-items-center gap-2">
                        <span class="spinner-border spinner-border-sm"></span> Menyimpan...
                    </span>
                </button>
                <p class="text-muted small mb-0" v-if="!canSave && items.length">
                    <i data-lucide="alert-triangle" style="width:12px" class="me-1 text-warning"></i>
                    Isi qty aktual semua item terlebih dahulu
                </p>
            </div>
        </div>

    </template>

    <!-- ══ SUKSES SIMPAN ════════════════════════════════════════════════════ -->
    <div v-if="doneKode" class="glass-panel border-0 shadow-sm p-5 text-center bg-white" style="border-radius:20px">
        <div class="d-flex flex-column align-items-center">
            <div class="bg-success bg-opacity-10 p-4 rounded-circle mb-3">
                <i data-lucide="check-circle-2" class="text-success" style="width:48px;height:48px"></i>
            </div>
            <h5 class="fw-bold text-dark mb-1">Barang Masuk Berhasil Disimpan!</h5>
            <p class="text-muted small mb-3">
                Kode penerimaan: <b class="font-mono text-success">{{ doneKode }}</b>
            </p>
            <button class="btn btn-success rounded-3 px-4 fw-bold shadow-sm" @click="resetFull">
                <i data-lucide="plus-circle" style="width:15px" class="me-1"></i>
                Input Barang Masuk Baru
            </button>
        </div>
    </div>

</div>

<!-- ══ STYLES ════════════════════════════════════════════════════════════ -->
<style>
    [v-cloak] {
        display: none;
    }

    body {
        background-color: #f4f7fa;
    }

    .font-mono {
        font-family: 'Courier New', monospace;
    }

    /* Info card */
    .info-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 1rem 1.1rem;
        box-shadow: 0 1px 4px rgba(0, 0, 0, .05);
        transition: transform .2s;
    }

    .info-card:hover {
        transform: translateY(-2px);
    }

    .info-card-label {
        font-size: .68rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .5px;
        color: #94a3b8;
        margin-bottom: .35rem;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .info-card-value {
        font-weight: 700;
        color: #1e293b;
        font-size: .92rem;
    }

    .info-card-sub {
        font-size: .73rem;
        color: #94a3b8;
        margin-top: .2rem;
    }

    /* Item header bar */
    .item-header {
        background: linear-gradient(135deg, #064e3b 0%, #059669 100%);
    }

    .item-header-icon {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        background: rgba(255, 255, 255, .18);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        flex-shrink: 0;
    }

    /* Table subheader tints */
    .th-kirim {
        background: rgba(224, 231, 255, .5) !important;
    }

    .th-aktual {
        background: rgba(209, 250, 229, .5) !important;
    }

    .th-selisih {
        background: rgba(254, 249, 195, .5) !important;
    }

    /* Row hover */
    .row-item {
        transition: background .1s;
    }

    .row-item:hover {
        background: #f8fafc !important;
    }

    .row-ok {
        background: rgba(209, 250, 229, .2) !important;
    }

    .row-lebih {
        background: rgba(219, 234, 254, .2) !important;
    }

    .row-kurang {
        background: rgba(254, 226, 226, .2) !important;
    }

    /* Qty badge */
    .qty-badge {
        display: inline-block;
        min-width: 44px;
        text-align: center;
        padding: 3px 10px;
        border-radius: 8px;
        font-weight: 700;
        font-size: .82rem;
    }

    .qty-kirim {
        background: #e0e7ff;
        color: #3730a3;
        border: 1px solid #c7d2fe;
    }

    /* Satuan pills */
    .satuan-pill {
        display: inline-block;
        padding: 2px 10px;
        border-radius: 10px;
        font-size: .72rem;
        font-weight: 700;
    }

    .satuan-kirim-pill {
        background: #e0e7ff;
        color: #3730a3;
        border: 1px solid #c7d2fe;
    }

    .satuan-simpan-pill {
        background: #d1fae5;
        color: #065f46;
        border: 1px solid #6ee7b7;
    }

    /* Input qty */
    .input-qty {
        width: 90px;
        border-radius: 8px !important;
        border: 1px solid #d1d5db !important;
        font-size: .85rem;
        transition: border-color .15s, box-shadow .15s;
    }

    .input-qty:focus {
        border-color: #059669 !important;
        box-shadow: 0 0 0 3px rgba(5, 150, 105, .12) !important;
    }

    .input-qty.is-filled {
        border-color: #059669 !important;
        background: #f0fdf4;
    }

    .input-qty.is-empty {
        border-color: #f59e0b !important;
        background: #fffbeb;
    }

    /* Input selisih */
    .input-selisih {
        width: 90px;
        border-radius: 8px !important;
        border: 1px solid #d1d5db !important;
        font-size: .85rem;
        transition: border-color .15s, box-shadow .15s;
    }

    .input-selisih:focus {
        border-color: #d97706 !important;
        box-shadow: 0 0 0 3px rgba(217, 119, 6, .12) !important;
    }

    .input-selisih.is-ok {
        border-color: #059669 !important;
        background: #f0fdf4;
    }

    .input-selisih.is-diff {
        border-color: #ef4444 !important;
        background: #fef2f2;
    }

    /* Selisih chips */
    .selisih-chip {
        padding: 2px 8px;
        border-radius: 20px;
        font-size: .69rem;
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

    /* Summary chips */
    .summary-chip {
        padding: 3px 10px;
        border-radius: 20px;
        font-size: .72rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        border: 1px solid;
    }

    .chip-belum {
        background: #fef3c7;
        color: #92400e;
        border-color: #fcd34d;
    }

    /* Helper box */
    .helper-box {
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        border-radius: 12px;
        padding: 1rem 1.2rem;
        max-width: 540px;
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
                kodeInput: '',
                scanning: false,
                saving: false,
                scanError: '',
                saveError: '',
                saveSuccess: '',
                doneKode: null,
                pengiriman: null,
                items: [],
            };
        },

        computed: {
            /* Semua item sudah diisi qty_aktual */
            canSave() {
                if (!this.items.length) return false;
                return this.items.every(i =>
                    i.qty_aktual !== null && i.qty_aktual !== '' && !isNaN(i.qty_aktual) &&
                    i.selisih !== null && i.selisih !== '' && !isNaN(i.selisih)
                );
            },

            summaryCount() {
                const filled = this.items.filter(i =>
                    i.qty_aktual !== null && i.qty_aktual !== '' && i.selisih !== null && i.selisih !== ''
                );
                return {
                    ok: filled.filter(i => Number(i.selisih) === 0).length,
                    lebih: filled.filter(i => Number(i.selisih) > 0).length,
                    kurang: filled.filter(i => Number(i.selisih) < 0).length,
                    belum: this.items.length - filled.length,
                };
            },
        },

        methods: {
            /* ── Scan ─────────────────────────────────────────── */
            async doScan() {
                const kode = this.kodeInput.trim();
                if (!kode) return;
                this.scanning = true;
                this.scanError = '';
                try {
                    const res = await axios.get('<?= base_url('barang-masuk/scan') ?>', {
                        params: {
                            kode
                        }
                    });
                    if (!res.data.success) {
                        this.scanError = res.data.message;
                        this.pengiriman = null;
                        this.items = [];
                    } else {
                        this.pengiriman = res.data.pengiriman;
                        // inisialisasi field input
                        this.items = res.data.items.map(i => ({
                            ...i,
                            qty_aktual: null,
                            selisih: null,
                        }));
                    }
                } catch (e) {
                    this.scanError = 'Terjadi kesalahan server.';
                } finally {
                    this.scanning = false;
                    this.$nextTick(() => lucide.createIcons());
                }
            },

            /* ── Auto-hitung selisih (opsional) ──────────────── */
            onQtyChange(item) {
                // Jika satuan kirim = satuan simpan, hitung otomatis
                if (
                    item.satuan_kirim_id &&
                    item.satuan_simpan_id &&
                    item.satuan_kirim_id === item.satuan_simpan_id &&
                    item.qty_aktual !== null && item.qty_aktual !== ''
                ) {
                    item.selisih = parseFloat((item.qty_aktual - item.qty_kiriman).toFixed(4));
                }
                // Jika beda satuan → biarkan user isi manual (tidak di-override)
            },

            /* ── Row class berdasarkan status selisih ─────────── */
            rowClass(item) {
                if (item.selisih === null || item.selisih === '') return '';
                const s = Number(item.selisih);
                if (s === 0) return 'row-ok';
                if (s > 0) return 'row-lebih';
                return 'row-kurang';
            },

            inputQtyClass(item) {
                if (item.qty_aktual === null || item.qty_aktual === '') return 'is-empty';
                return 'is-filled';
            },

            inputSelisihClass(item) {
                if (item.selisih === null || item.selisih === '') return '';
                return Number(item.selisih) === 0 ? 'is-ok' : 'is-diff';
            },

            /* ── Simpan ───────────────────────────────────────── */
            async simpan() {
                if (!this.canSave) return;
                this.saving = true;
                this.saveError = '';
                this.saveSuccess = '';
                try {
                    const payload = {
                        pengiriman_gudang_id: this.pengiriman.id,
                        items: this.items.map(i => ({
                            barang_id: i.barang_id,
                            qty_kiriman: i.qty_kiriman,
                            qty_aktual: i.qty_aktual,
                            selisih: i.selisih,
                            satuan_kirim: i.satuan_kirim || '',
                            satuan_simpan: i.satuan_simpan || '',
                        })),
                    };
                    const res = await axios.post('<?= base_url('barang-masuk/simpan') ?>', payload);
                    if (res.data.success) {
                        this.doneKode = res.data.kode_masuk;
                        this.pengiriman = null;
                        this.items = [];
                    } else {
                        this.saveError = res.data.message;
                    }
                } catch (e) {
                    this.saveError = 'Terjadi kesalahan server.';
                } finally {
                    this.saving = false;
                    this.$nextTick(() => lucide.createIcons());
                }
            },

            /* ── Reset ganti pengiriman ──────────────────────── */
            reset() {
                this.pengiriman = null;
                this.items = [];
                this.scanError = '';
                this.saveError = '';
                this.kodeInput = '';
                this.$nextTick(() => this.$refs.scanInput?.focus());
            },

            /* ── Reset setelah sukses ──────────────────────────── */
            resetFull() {
                this.reset();
                this.doneKode = null;
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
        },

        mounted() {
            lucide.createIcons();
            this.$nextTick(() => this.$refs.scanInput?.focus());
        },

        updated() {
            lucide.createIcons();
        },
    }).mount('#app');
</script>
<?= $this->endSection() ?>