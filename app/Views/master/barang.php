<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="glass-panel p-4 h-100 d-flex flex-column border-0 shadow-lg position-relative overflow-hidden" id="app">

    <!-- ── HEADER ── -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0 text-dark">Master Barang</h4>
            <p class="text-muted small mb-0 d-flex align-items-center">
                <i data-lucide="package" class="me-1" style="width: 14px;"></i>
                Data Global semua barang yang tersedia di toko dan gudang utama. Informasi ini akan dipakai di transaksi penjualan, penerimaan gudang, dan aset toko.
            </p>
        </div>
        <div class="d-flex gap-2">
            <button v-if="isOwner"
                class="btn btn-primary shadow-sm px-3 d-flex align-items-center gap-2"
                style="border-radius: 8px; font-weight: 600;"
                @click="openModalBarang()">
                <i data-lucide="plus-circle" style="width: 18px;"></i>
                Tambah Barang
            </button>
        </div>
    </div>

    <!-- ── SEARCH ── -->
    <div class="glass-panel bg-white bg-opacity-50 p-2 mb-4 d-flex align-items-center border-white shadow-sm" style="border-radius: 12px;">
        <div class="input-group input-group-sm border-0">
            <span class="input-group-text bg-transparent border-0 pe-1">
                <i data-lucide="search" class="text-muted" style="width: 16px;"></i>
            </span>
            <input type="text" class="form-control border-0 bg-transparent shadow-none"
                placeholder="Cari nama barang..." v-model="search" @input="page = 1">
        </div>
    </div>

    <!-- ── TABEL ── -->
    <div class="table-responsive flex-fill custom-scroll">
        <table class="table table-hover align-middle border-0">
            <thead>
                <tr class="text-muted small text-uppercase">
                    <th class="border-0">No</th>
                    <th class="border-0">Nama Barang</th>
                    <th class="border-0 text-end">Harga Pokok</th>
                    <th class="border-0 text-end">Harga Jual</th>
                    <th v-if="isOwner" class="border-0 text-end pe-3">Aksi</th>
                </tr>
            </thead>
            <tbody class="border-top-0">
                <tr v-for="(b, index) in paginatedData" :key="b.id" class="table-row-modern">
                    <td>{{ (page - 1) * perPage + index + 1 }}</td>

                    <td>
                        <div class="fw-semibold text-dark d-flex align-items-center gap-2">
                            {{ b.nama }}
                            <span v-if="diskonMap[b.id]"
                                class="badge border border-secondary border-opacity-25 text-dark bg-white"
                                style="font-size: 9px; font-weight: 700;">
                                <i data-lucide="tag" style="width: 9px;"></i>
                                -Rp {{ formatNumber(diskonMap[b.id]) }}
                            </span>
                        </div>
                        <div class="small text-muted">{{ b.nama_jenis }} • {{ b.barcode }}</div>
                    </td>

                    <td class="text-end">
                        Rp {{ Number(b.harga_pokok).toLocaleString() }}
                        <div class="small text-muted">/ {{ b.nama_satuan }}</div>
                    </td>

                    <td class="text-end">
                        <div v-if="diskonMap[b.id]">
                            <div class="text-muted" style="font-size: 11px; text-decoration: line-through;">
                                Rp {{ Number(b.harga_jual).toLocaleString() }}
                            </div>
                            <div class="fw-bold text-success">
                                Rp {{ Number(b.harga_jual - diskonMap[b.id]).toLocaleString() }}
                            </div>
                            <div class="small text-muted">/ {{ b.nama_satuan }}</div>
                        </div>
                        <div v-else class="fw-bold text-success">
                            Rp {{ Number(b.harga_jual).toLocaleString() }}
                            <div class="small text-muted">/ {{ b.nama_satuan }}</div>
                        </div>
                    </td>

                    <td v-if="isOwner" class="text-end pe-3">
                        <div class="d-flex justify-content-end gap-1">
                            <button class="btn btn-icon btn-light-warning" @click="openModalBarang(b)">
                                <i data-lucide="edit-3" style="width: 16px;"></i>
                            </button>
                            <button class="btn btn-icon btn-light-danger" @click="hapusBarang(b.id)">
                                <i data-lucide="trash-2" style="width: 16px;"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- ── PAGINATION ── -->
    <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
        <small class="text-muted">Total {{ filteredData.length }} barang</small>
        <div class="pagination-container d-flex gap-1">
            <button class="btn btn-pagination" :disabled="page == 1" @click="page--">
                <i data-lucide="chevron-left" style="width:16px"></i>
            </button>
            <div class="px-3 d-flex align-items-center fw-bold small text-primary bg-primary bg-opacity-10 rounded-3">
                {{ page }} / {{ totalPage || 1 }}
            </div>
            <button class="btn btn-pagination" :disabled="page >= totalPage" @click="page++">
                <i data-lucide="chevron-right" style="width:16px"></i>
            </button>
        </div>
    </div>

    <!-- ================================================================ -->
    <!-- MODAL BARANG (owner only)                                         -->
    <!-- ================================================================ -->
    <div class="modal fade shadow-lg" id="barangModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0" style="border-radius: 16px;">
                <div class="modal-header border-0 pb-0">
                    <h5 class="fw-bold">{{ form.id ? 'Edit Barang' : 'Tambah Barang Baru' }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body py-4">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="small fw-bold text-muted mb-1">Nama Barang</label>
                                <input type="text" class="form-control modern-input"
                                    :class="{'is-invalid': errors.nama}" v-model="form.nama">
                                <div class="invalid-feedback">{{ errors.nama }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="small fw-bold text-muted mb-1">Barcode</label>
                                <input type="text" class="form-control modern-input"
                                    :class="{'is-invalid': errors.barcode}" v-model="form.barcode">
                                <div class="invalid-feedback">{{ errors.barcode }}</div>
                            </div>
                            <div class="row g-2">
                                <div class="col-6">
                                    <label class="small fw-bold text-muted mb-1">Harga Pokok</label>
                                    <input type="number" class="form-control modern-input" v-model="form.harga_pokok">
                                </div>
                                <div class="col-6">
                                    <label class="small fw-bold text-muted mb-1">Harga Jual</label>
                                    <input type="number" class="form-control modern-input" v-model="form.harga_jual">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label class="small fw-bold text-muted">Jenis</label>
                                    <button v-if="isOwner" class="btn btn-sm btn-link p-0 text-decoration-none"
                                        @click="openSettingModal('jenis')" title="Kelola Jenis">
                                        <i data-lucide="settings" style="width: 16px;"></i>
                                    </button>
                                </div>
                                <select class="form-select modern-input"
                                    :class="{'is-invalid': errors.jenis_id}" v-model="form.jenis_id">
                                    <option value="">Pilih Jenis</option>
                                    <option v-for="j in listJenis" :key="j.id" :value="j.id">{{ j.nama }}</option>
                                </select>
                                <div class="invalid-feedback">{{ errors.jenis_id }}</div>
                            </div>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label class="small fw-bold text-muted">Satuan</label>
                                    <button v-if="isOwner" class="btn btn-sm btn-link p-0 text-decoration-none"
                                        @click="openSettingModal('satuan')" title="Kelola Satuan">
                                        <i data-lucide="settings" style="width: 16px;"></i>
                                    </button>
                                </div>
                                <select class="form-select modern-input"
                                    :class="{'is-invalid': errors.satuan_id}" v-model="form.satuan_id">
                                    <option value="">Pilih Satuan</option>
                                    <option v-for="s in listSatuan" :key="s.id" :value="s.id">{{ s.nama }}</option>
                                </select>
                                <div class="invalid-feedback">{{ errors.satuan_id }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button class="btn btn-light px-4" data-bs-dismiss="modal">Batal</button>
                    <button class="btn btn-primary px-4 fw-bold" @click="simpanBarang">Simpan</button>
                </div>
            </div>
        </div>
    </div>

    <!-- ================================================================ -->
    <!-- MODAL SETTING JENIS/SATUAN (owner only)                           -->
    <!-- ================================================================ -->
    <div class="modal fade" id="settingModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0" style="border-radius: 16px;">
                <div class="modal-header border-0">
                    <h5 class="fw-bold">Kelola {{ settingType === 'jenis' ? 'Jenis Barang' : 'Satuan' }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="input-group mb-3">
                        <input type="text" class="form-control modern-input"
                            :placeholder="'Tambah ' + (settingType === 'jenis' ? 'jenis' : 'satuan') + ' baru'"
                            v-model="newItemName" @keyup.enter="tambahOpsi">
                        <button class="btn btn-primary" @click="tambahOpsi">
                            <i data-lucide="plus" style="width: 16px;"></i>
                        </button>
                    </div>
                    <div class="list-group custom-scroll" style="max-height: 300px; overflow-y: auto;">
                        <div v-for="item in (settingType === 'jenis' ? listJenis : listSatuan)"
                            :key="item.id"
                            class="list-group-item d-flex justify-content-between align-items-center border rounded-3 mb-2">
                            <span>{{ item.nama }}</span>
                            <button class="btn btn-sm btn-light-danger" @click="hapusOpsi(settingType, item.id)">
                                <i data-lucide="trash-2" style="width: 14px;"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button class="btn btn-light px-4" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- TOAST -->
    <div class="position-fixed top-0 end-0 p-4" style="z-index:9999">
        <div id="appToast" class="toast custom-toast" role="alert">
            <div class="d-flex align-items-center">
                <i data-lucide="check-circle" class="toast-icon me-2"></i>
                <div class="toast-body fw-semibold">{{ toastMsg }}</div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    </div>

</div>

<style>
    .custom-toast {
        background: #fff;
        border: 1px solid #dbeafe;
        border-left: 4px solid #0d6efd;
        border-radius: 10px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, .08);
    }

    .toast-icon {
        width: 20px;
        height: 20px;
        color: #0d6efd;
    }

    .modern-input {
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 0.5rem 0.75rem;
        transition: all 0.2s;
    }

    .modern-input:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .btn-link {
        color: #6b7280;
    }

    .btn-link:hover {
        color: #3b82f6;
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
                userRole: '<?= session()->get('role') ?>',
                barang: [],
                listJenis: [],
                listSatuan: [],
                diskonMap: {},
                search: '',
                page: 1,
                perPage: 10,
                form: {
                    id: null,
                    nama: '',
                    barcode: '',
                    jenis_id: '',
                    satuan_id: '',
                    harga_pokok: 0,
                    harga_jual: 0
                },
                errors: {},
                toastMsg: '',
                settingType: '',
                newItemName: '',
                bModal: null,
                settingModal: null,
                toast: null
            }
        },

        computed: {
            isOwner() {
                return this.userRole === 'owner';
            },
            filteredData() {
                return this.barang.filter(b =>
                    b.nama.toLowerCase().includes(this.search.toLowerCase())
                );
            },
            totalPage() {
                return Math.ceil(this.filteredData.length / this.perPage);
            },
            paginatedData() {
                const start = (this.page - 1) * this.perPage;
                return this.filteredData.slice(start, start + this.perPage);
            }
        },

        mounted() {
            this.bModal = new bootstrap.Modal(document.getElementById('barangModal'));
            this.settingModal = new bootstrap.Modal(document.getElementById('settingModal'));
            this.toast = new bootstrap.Toast(document.getElementById('appToast'));
            this.loadBarang();
            this.loadOpsi();
            this.loadDiskonAktif();
            lucide.createIcons();
        },
        updated() {
            lucide.createIcons();
        },

        methods: {
            showToast(msg) {
                this.toastMsg = msg;
                this.toast.show();
            },
            formatNumber(num) {
                return num ? new Intl.NumberFormat('id-ID').format(num) : 0;
            },

            loadBarang() {
                axios.get('<?= base_url('barang/list') ?>').then(res => {
                    this.barang = res.data.data;
                });
            },
            loadOpsi() {
                axios.get('<?= base_url('barang/jenis/list') ?>').then(res => this.listJenis = res.data.data);
                axios.get('<?= base_url('barang/satuan/list') ?>').then(res => this.listSatuan = res.data.data);
            },
            loadDiskonAktif() {
                axios.get('<?= base_url('barang/diskon-aktif') ?>').then(res => {
                    const map = {};
                    (res.data.data || []).forEach(d => {
                        map[d.barang_id] = parseFloat(d.nominal_diskon);
                    });
                    this.diskonMap = map;
                }).catch(() => {});
            },

            // ── CRUD (owner only) ──────────────────────────────────────
            openModalBarang(item = null) {
                if (!this.isOwner) return;
                this.errors = {};
                this.form = item ? {
                    ...item
                } : {
                    id: null,
                    nama: '',
                    barcode: '',
                    jenis_id: '',
                    satuan_id: '',
                    harga_pokok: 0,
                    harga_jual: 0
                };
                this.bModal.show();
            },
            simpanBarang() {
                if (!this.isOwner) return;
                const url = this.form.id ?
                    `<?= base_url('barang/update') ?>/${this.form.id}` :
                    `<?= base_url('barang/store') ?>`;
                axios.post(url, this.form)
                    .then(res => {
                        this.showToast(res.data.message);
                        this.loadBarang();
                        this.bModal.hide();
                    })
                    .catch(err => {
                        this.errors = err.response?.data?.messages || {};
                    });
            },
            hapusBarang(id) {
                if (!this.isOwner) return;
                if (confirm('Hapus barang ini secara permanen?')) {
                    axios.delete(`<?= base_url('barang/delete') ?>/${id}`)
                        .then(res => {
                            this.showToast(res.data.message);
                            this.loadBarang();
                        });
                }
            },

            // ── Jenis & Satuan (owner only) ────────────────────────────
            openSettingModal(type) {
                if (!this.isOwner) return;
                this.settingType = type;
                this.newItemName = '';
                this.settingModal.show();
            },
            tambahOpsi() {
                if (!this.isOwner || !this.newItemName.trim()) return;
                axios.post(`<?= base_url('barang') ?>/${this.settingType}/store`, {
                        nama: this.newItemName
                    })
                    .then(() => {
                        this.showToast('Data berhasil ditambahkan');
                        this.loadOpsi();
                        this.newItemName = '';
                    });
            },
            hapusOpsi(type, id) {
                if (!this.isOwner) return;
                if (confirm('Yakin hapus data ini?')) {
                    axios.delete(`<?= base_url('barang') ?>/${type}/delete/${id}`)
                        .then(() => {
                            this.showToast('Data berhasil dihapus');
                            this.loadOpsi();
                        });
                }
            }
        }
    }).mount('#app');
</script>
<?= $this->endSection() ?>