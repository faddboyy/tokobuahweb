<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<div id="appInventory" v-cloak>

    <!-- HEADER PANEL -->
    <div class="glass-panel p-4 mb-4 border-0 shadow-lg">
        <div class="row align-items-center g-3">
            <div class="col-md-6">
                <h4 class="fw-bold mb-0 text-dark">Asset Toko</h4>
                <p class="text-muted small mb-3">Manajemen distribusi stok dan aset per wilayah</p>

                <div class="d-flex gap-2">
                    <select class="form-select modern-input text-primary fw-bold"
                        v-model="cabangTerpilih"
                        @change="handleCabangChange"
                        :disabled="isPetugas">
                        <option value="">-- Pilih Lokasi Toko --</option>
                        <?php foreach ($cabang as $c): ?>
                            <option value="<?= $c['id'] ?>"><?= esc($c['nama']) ?></option>
                        <?php endforeach; ?>
                    </select>

                    <button v-if="isOwner"
                        class="btn btn-primary px-3 shadow-sm d-flex align-items-center gap-2"
                        @click="openImportModal"
                        :disabled="!cabangTerpilih">
                        <i data-lucide="plus-circle" style="width:18px;"></i> Import
                    </button>
                </div>

                <p v-if="isPetugas" class="text-muted small mt-2 mb-0">
                    <i data-lucide="info" style="width:12px;"></i>
                    Anda hanya dapat melihat data cabang yang ditugaskan.
                </p>
            </div>

            <div class="col-md-5 ms-auto">
                <div class="row g-3">
                    <div class="col-6">
                        <div class="p-3 bg-white border rounded-4 shadow-sm">
                            <small class="text-muted d-block small fw-bold">ESTIMASI ASET</small>
                            <h4 class="fw-bold mb-0 text-dark">Rp {{ totalAsset.toLocaleString('id-ID') }}</h4>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 bg-white border rounded-4 shadow-sm">
                            <small class="text-muted d-block small fw-bold">ESTIMASI MARGIN</small>
                            <h4 class="fw-bold mb-0 text-success">Rp {{ totalMargin.toLocaleString('id-ID') }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- TABEL PANEL -->
    <div class="glass-panel p-4 border-0 shadow-lg">

        <div class="d-flex justify-content-between align-items-center mb-4">

            <!-- Search -->
            <div class="input-group bg-light rounded-3 px-2 border-0 flex-fill shadow-none" style="max-width:400px;">
                <span class="input-group-text bg-transparent border-0">
                    <i data-lucide="search" class="text-muted" style="width:18px;"></i>
                </span>
                <input type="text" class="form-control bg-transparent border-0 py-2 shadow-none"
                    placeholder="Cari barang..." v-model="search">
            </div>

            <!-- Tombol PDF (owner only) -->
            <div v-if="isOwner" class="d-flex gap-2">
                <button
                    class="btn btn-outline-primary d-flex align-items-center gap-2 px-3"
                    @click="printPdf('semua')"
                    :disabled="!cabangTerpilih || inventory.length === 0">
                    <i data-lucide="file-text" style="width:16px;"></i> PDF Semua
                </button>
                <button
                    class="btn btn-outline-warning d-flex align-items-center gap-2 px-3"
                    @click="printPdf('promo')"
                    :disabled="!cabangTerpilih || jumlahPromo === 0">
                    <i data-lucide="tag" style="width:16px;"></i>
                    PDF Promo
                    <span v-if="jumlahPromo > 0"
                        class="badge bg-warning text-dark ms-1">{{ jumlahPromo }}</span>
                </button>
            </div>
        </div>

        <!-- State: belum pilih cabang -->
        <div v-if="!cabangTerpilih" class="text-center py-5">
            <i data-lucide="map-pin" class="text-muted mb-3" style="width:40px;height:40px;"></i>
            <h6 class="text-muted fw-bold">Pilih Cabang untuk memuat data stok</h6>
        </div>

        <!-- State: loading -->
        <div v-else-if="loading" class="text-center py-5">
            <div class="spinner-border text-primary mb-3" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <h6 class="text-muted fw-bold">Memuat data...</h6>
        </div>

        <!-- State: data kosong -->
        <div v-else-if="filteredData.length === 0" class="text-center py-5">
            <i data-lucide="package-x" class="text-muted mb-3" style="width:40px;height:40px;"></i>
            <h6 class="text-muted fw-bold">
                <template v-if="search">Tidak ada barang yang cocok dengan "{{ search }}"</template>
                <template v-else>Belum ada data stok untuk cabang ini</template>
            </h6>
        </div>

        <!-- State: ada data -->
        <div v-else class="table-responsive">
            <table class="table table-hover align-middle border-0">
                <thead>
                    <tr class="text-muted small text-uppercase">
                        <th>Nama Produk</th>
                        <th class="text-center">Stok</th>
                        <th>Harga Pokok</th>
                        <th>Harga Jual</th>
                        <th>Margin / Unit</th>
                        <th>Subtotal Aset</th>
                        <th>Subtotal Margin</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="item in pagedData" :key="item.id">
                        <td>
                            <div class="fw-bold d-flex align-items-center gap-2">
                                {{ item.nama_barang }}
                                <span v-if="item.ada_promo == 1"
                                    class="badge bg-white border border-secondary border-opacity-25 text-dark"
                                    style="font-size:9px;font-weight:700;white-space:nowrap;">
                                    <i data-lucide="tag" style="width:9px;"></i> PROMO
                                </span>
                            </div>
                            <span class="badge bg-light text-primary border small">{{ item.nama_jenis }}</span>
                        </td>

                        <td class="text-center">
                            <span class="badge rounded-pill px-3 py-2 fw-bold"
                                :class="item.stock > 0 ? 'bg-soft-primary text-primary' : 'bg-soft-danger text-danger'">
                                {{ item.stock }} {{ item.nama_satuan }}
                            </span>
                        </td>

                        <td>Rp {{ Number(item.harga_pokok).toLocaleString('id-ID') }}</td>

                        <td>
                            <div v-if="item.ada_promo == 1">
                                <div class="text-muted" style="font-size:11px;text-decoration:line-through;">
                                    Rp {{ Number(item.harga_jual).toLocaleString('id-ID') }}
                                </div>
                                <div class="fw-bold text-success">
                                    Rp {{ (Number(item.harga_jual) - Number(item.nominal_diskon)).toLocaleString('id-ID') }}
                                </div>
                                <div class="text-muted" style="font-size:10px;">
                                    diskon Rp {{ Number(item.nominal_diskon).toLocaleString('id-ID') }}
                                </div>
                            </div>
                            <div v-else class="fw-bold">
                                Rp {{ Number(item.harga_jual).toLocaleString('id-ID') }}
                            </div>
                        </td>

                        <td>
                            <span :class="marginPerUnit(item) >= 0 ? 'text-success fw-bold' : 'text-danger fw-bold'">
                                Rp {{ marginPerUnit(item).toLocaleString('id-ID') }}
                            </span>
                        </td>

                        <td class="fw-bold">
                            Rp {{ (Number(item.stock) * Number(item.harga_pokok)).toLocaleString('id-ID') }}
                        </td>

                        <td class="fw-bold text-success">
                            Rp {{ (Number(item.stock) * marginPerUnit(item)).toLocaleString('id-ID') }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div v-if="cabangTerpilih && !loading && totalPage > 1"
            class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
            <small class="text-muted">Total {{ filteredData.length }} barang</small>
            <div class="d-flex gap-1">
                <button class="btn btn-sm btn-outline-secondary" :disabled="currentPage === 1" @click="currentPage--">
                    <i data-lucide="chevron-left" style="width:14px;"></i>
                </button>
                <span class="px-3 d-flex align-items-center fw-bold small text-primary bg-primary bg-opacity-10 rounded-3">
                    {{ currentPage }} / {{ totalPage }}
                </span>
                <button class="btn btn-sm btn-outline-secondary" :disabled="currentPage >= totalPage" @click="currentPage++">
                    <i data-lucide="chevron-right" style="width:14px;"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- IMPORT MODAL -->
    <div v-if="isOwner" class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header">
                    <h5 class="fw-bold m-0" id="importModalLabel">Import Master Barang</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div class="d-flex justify-content-between mb-3">
                        <input type="text" class="form-control me-2"
                            placeholder="Filter barang..." v-model="searchImport">
                        <button class="btn btn-outline-primary btn-sm" @click="toggleCheckAll">
                            {{ isAllChecked ? 'Uncheck All' : 'Check All' }}
                        </button>
                    </div>

                    <div v-if="Object.keys(groupedAvailableBarang).length === 0" class="text-center py-4">
                        <i data-lucide="package-check" class="text-muted mb-2" style="width:32px;height:32px;"></i>
                        <p class="text-muted mb-0 small">
                            <template v-if="searchImport">Tidak ada barang cocok dengan pencarian.</template>
                            <template v-else>Semua barang sudah diimport ke cabang ini.</template>
                        </p>
                    </div>

                    <div v-else style="max-height:400px;overflow-y:auto;">
                        <div v-for="(items, jenis) in groupedAvailableBarang" :key="jenis" class="mb-3">
                            <div class="small fw-bold text-muted mb-2">{{ jenis }}</div>
                            <div class="row">
                                <div class="col-md-4 mb-2" v-for="b in items" :key="b.id">
                                    <label class="border rounded p-2 w-100 d-block"
                                        style="cursor:pointer;"
                                        :class="importIds.includes(b.id) ? 'border-primary bg-light' : ''">
                                        <input type="checkbox" :value="b.id" v-model="importIds" class="me-2">
                                        {{ b.nama }}
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <span class="text-muted small me-auto">{{ importIds.length }} barang dipilih</span>
                    <button class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button class="btn btn-primary"
                        :disabled="importIds.length === 0 || loadingImport"
                        @click="saveImport">
                        <span v-if="loadingImport" class="spinner-border spinner-border-sm me-1" role="status"></span>
                        Import{{ importIds.length > 0 ? ' (' + importIds.length + ')' : '' }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- TOAST -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div id="liveToast" class="toast border-0 shadow-lg" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex p-3 bg-white rounded">
                <div class="me-3 text-primary fw-bold">&#10003;</div>
                <div class="toast-body fw-bold small">{{ toastMessage }}</div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>

</div><!-- #appInventory -->

<script>
    const {
        createApp
    } = Vue;
    createApp({
        data() {
            return {
                userRole: '<?= esc($role, "js") ?>',
                userCabangId: '<?= esc($user_cabang_id, "js") ?>',
                cabangTerpilih: '',
                inventory: [],
                availableBarang: [],
                importIds: [],
                search: '',
                searchImport: '',
                toastMessage: '',
                currentPage: 1,
                itemsPerPage: 10,
                loading: false,
                loadingImport: false,
                allCabang: <?= json_encode($cabang) ?>,
            };
        },

        computed: {
            isOwner() {
                return this.userRole === 'owner';
            },
            isAdmin() {
                return this.userRole === 'admin';
            },
            isPetugas() {
                return this.userRole === 'petugas';
            },

            filteredData() {
                if (!this.search.trim()) return this.inventory;
                const q = this.search.toLowerCase();
                return this.inventory.filter(i =>
                    i.nama_barang.toLowerCase().includes(q) ||
                    i.nama_jenis.toLowerCase().includes(q)
                );
            },
            pagedData() {
                const start = (this.currentPage - 1) * this.itemsPerPage;
                return this.filteredData.slice(start, start + this.itemsPerPage);
            },
            totalPage() {
                return Math.ceil(this.filteredData.length / this.itemsPerPage) || 1;
            },
            totalAsset() {
                return this.filteredData.reduce(
                    (s, i) => s + Number(i.stock) * Number(i.harga_pokok), 0
                );
            },
            totalMargin() {
                return this.filteredData.reduce(
                    (s, i) => s + Number(i.stock) * this.marginPerUnit(i), 0
                );
            },
            jumlahPromo() {
                return this.inventory.filter(i => i.ada_promo == 1).length;
            },
            groupedAvailableBarang() {
                const q = this.searchImport.toLowerCase();
                const f = q ?
                    this.availableBarang.filter(b => b.nama.toLowerCase().includes(q)) :
                    this.availableBarang;
                const g = {};
                f.forEach(item => {
                    if (!g[item.nama_jenis]) g[item.nama_jenis] = [];
                    g[item.nama_jenis].push(item);
                });
                return g;
            },
            isAllChecked() {
                return this.availableBarang.length > 0 &&
                    this.importIds.length === this.availableBarang.length;
            },
        },

        watch: {
            search() {
                this.currentPage = 1;
            },
            totalPage(val) {
                if (this.currentPage > val) this.currentPage = val;
            },
        },

        mounted() {
            if (this.isPetugas && this.userCabangId) {
                this.cabangTerpilih = this.userCabangId;
                this.loadInventory();
            }
            lucide.createIcons();
        },
        updated() {
            lucide.createIcons();
        },

        methods: {
            marginPerUnit(item) {
                const pokok = Number(item.harga_pokok);
                const jual = Number(item.harga_jual);
                const diskon = item.ada_promo == 1 ? Number(item.nominal_diskon) : 0;
                return (jual - diskon) - pokok;
            },

            handleCabangChange() {
                this.currentPage = 1;
                this.inventory = [];
                this.search = '';
                this.loadInventory();
            },

            loadInventory() {
                if (!this.cabangTerpilih) return;
                this.loading = true;
                axios.get('<?= base_url("aset-toko/list") ?>/' + this.cabangTerpilih)
                    .then(r => {
                        this.inventory = r.data.data ?? [];
                    })
                    .catch(() => {
                        this.showToast('Gagal memuat data. Silakan coba lagi.');
                    })
                    .finally(() => {
                        this.loading = false;
                    });
            },

            openImportModal() {
                if (!this.isOwner) return;
                axios.get('<?= base_url("aset-toko/get-available-barang") ?>/' + this.cabangTerpilih)
                    .then(r => {
                        this.availableBarang = r.data.data ?? [];
                        this.importIds = [];
                        this.searchImport = '';
                        new bootstrap.Modal(document.getElementById('importModal')).show();
                    })
                    .catch(() => {
                        this.showToast('Gagal memuat daftar barang.');
                    });
            },

            toggleCheckAll() {
                this.importIds = this.isAllChecked ?
                    [] :
                    this.availableBarang.map(b => b.id);
            },

            saveImport() {
                if (!this.isOwner || this.importIds.length === 0) return;
                this.loadingImport = true;
                axios.post('<?= base_url("aset-toko/import") ?>', {
                        cabang_id: this.cabangTerpilih,
                        barang_ids: this.importIds,
                    })
                    .then(r => {
                        bootstrap.Modal.getInstance(document.getElementById('importModal')).hide();
                        this.loadInventory();
                        this.showToast(r.data.message ?? 'Berhasil import');
                    })
                    .catch(err => {
                        const msg = err.response?.data?.messages?.error ?? 'Gagal import. Silakan coba lagi.';
                        this.showToast(msg);
                    })
                    .finally(() => {
                        this.loadingImport = false;
                    });
            },

            // Buka PDF di tab baru — type: 'semua' | 'promo'
            printPdf(type) {
                if (!this.isOwner || !this.cabangTerpilih) return;
                const url = `<?= base_url('aset-toko/print-pdf') ?>/${this.cabangTerpilih}/${type}`;
                window.open(url, '_blank');
            },

            showToast(msg) {
                this.toastMessage = msg;
                const el = document.getElementById('liveToast');
                if (el) new bootstrap.Toast(el).show();
            },
        },
    }).mount('#appInventory');
</script>

<style>
    .bg-soft-primary {
        background: rgba(13, 110, 253, 0.1);
    }

    .bg-soft-danger {
        background: rgba(220, 53, 69, 0.1);
    }

    [v-cloak] {
        display: none;
    }
</style>

<?= $this->endSection() ?>