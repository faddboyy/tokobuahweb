<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="glass-panel p-4 h-100 d-flex flex-column border-0 shadow-lg position-relative overflow-hidden" id="app">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0 text-dark">Master Suplier</h4>
            <p class="text-muted small mb-0 d-flex align-items-center">
                <i data-lucide="truck" class="me-1" style="width: 14px;"></i>
                Manajemen daftar pemasok barang
            </p>
        </div>
        <button class="btn btn-primary shadow-sm px-3 d-flex align-items-center gap-2"
            style="border-radius: 8px; font-weight: 600;" @click="openModal()">
            <i data-lucide="plus-circle" style="width: 18px;"></i>
            Tambah Suplier
        </button>
    </div>

    <div class="glass-panel bg-white bg-opacity-50 p-2 mb-4 d-flex align-items-center border-white shadow-sm" style="border-radius: 12px;">
        <div class="input-group input-group-sm border-0">
            <span class="input-group-text bg-transparent border-0 pe-1">
                <i data-lucide="search" class="text-muted" style="width: 16px;"></i>
            </span>
            <input type="text" class="form-control border-0 bg-transparent shadow-none"
                placeholder="Cari nama atau telepon suplier..." v-model="search" @input="page = 1">
        </div>
    </div>

    <div class="table-responsive flex-fill custom-scroll">
        <table class="table table-hover align-middle border-0">
            <thead>
                <tr class="text-muted small text-uppercase">
                    <th class="border-0 ps-3">No</th>
                    <th class="border-0">Informasi Suplier</th>
                    <th class="border-0">Alamat</th>
                    <th class="border-0 text-end pe-3">Aksi</th>
                </tr>
            </thead>
            <tbody class="border-top-0">
                <tr v-for="(s, index) in paginatedData" :key="s.id" class="table-row-modern">
                    <td class="ps-3 text-muted" style="width: 50px;">{{ (page - 1) * perPage + index + 1 }}</td>
                    <td>
                        <div class="fw-bold text-dark">{{ s.nama }}</div>
                        <div class="small text-muted d-flex align-items-center gap-2">
                            <span><i data-lucide="phone" style="width: 12px;"></i> {{ s.telepon }}</span>
                            <span v-if="s.email">| <i data-lucide="mail" style="width: 12px;"></i> {{ s.email }}</span>
                        </div>
                    </td>
                    <td class="small text-secondary">{{ s.alamat }}</td>
                    <td class="text-end pe-3">
                        <div class="d-flex justify-content-end gap-1">
                            <button class="btn btn-icon btn-light-warning" @click="openModal(s)">
                                <i data-lucide="edit-3" style="width: 16px;"></i>
                            </button>
                            <button class="btn btn-icon btn-light-danger" @click="hapusSuplier(s.id)">
                                <i data-lucide="trash-2" style="width: 16px;"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
        <small class="text-muted">Total {{ filteredData.length }} suplier</small>
        <div class="pagination-container d-flex gap-1">
            <button class="btn btn-pagination" :disabled="page==1" @click="page--"><i data-lucide="chevron-left" style="width:16px"></i></button>
            <div class="px-3 d-flex align-items-center fw-bold small text-primary bg-primary bg-opacity-10 rounded-3">{{ page }} / {{ totalPage || 1 }}</div>
            <button class="btn btn-pagination" :disabled="page>=totalPage" @click="page++"><i data-lucide="chevron-right" style="width:16px"></i></button>
        </div>
    </div>

    <div class="modal fade shadow-lg" id="suplierModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0" style="border-radius: 16px;">
                <div class="modal-header border-0 pb-0">
                    <h5 class="fw-bold">{{ form.id ? 'Edit Suplier' : 'Tambah Suplier Baru' }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body py-4">
                    <div class="mb-3">
                        <label class="small fw-bold text-muted mb-1">Nama Suplier</label>
                        <input type="text" class="form-control modern-input" :class="{'is-invalid': errors.nama}" v-model="form.nama">
                        <div class="invalid-feedback">{{ errors.nama }}</div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="small fw-bold text-muted mb-1">Telepon</label>
                            <input type="text" class="form-control modern-input" :class="{'is-invalid': errors.telepon}" v-model="form.telepon">
                            <div class="invalid-feedback">{{ errors.telepon }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="small fw-bold text-muted mb-1">Email (Opsional)</label>
                            <input type="email" class="form-control modern-input" :class="{'is-invalid': errors.email}" v-model="form.email">
                            <div class="invalid-feedback">{{ errors.email }}</div>
                        </div>
                    </div>
                    <div class="mb-0">
                        <label class="small fw-bold text-muted mb-1">Alamat</label>
                        <textarea class="form-control modern-input" rows="3" :class="{'is-invalid': errors.alamat}" v-model="form.alamat"></textarea>
                        <div class="invalid-feedback">{{ errors.alamat }}</div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button class="btn btn-light px-4" data-bs-dismiss="modal">Batal</button>
                    <button class="btn btn-primary px-4 fw-bold" @click="simpanSuplier">Simpan</button>
                </div>
            </div>
        </div>
    </div>

    <div class="position-fixed top-0 end-0 p-4" style="z-index:9999">
        <div id="suplierToast" class="toast custom-toast" role="alert">
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

    .table-row-modern:hover {
        background-color: rgba(13, 110, 253, 0.02);
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
                suplier: [],
                search: '',
                page: 1,
                perPage: 10,
                form: {
                    id: null,
                    nama: '',
                    alamat: '',
                    telepon: '',
                    email: ''
                },
                errors: {},
                toastMsg: '',
                modal: null,
                toast: null
            }
        },
        computed: {
            filteredData() {
                return this.suplier.filter(s =>
                    s.nama.toLowerCase().includes(this.search.toLowerCase()) ||
                    s.telepon.includes(this.search)
                )
            },
            totalPage() {
                return Math.ceil(this.filteredData.length / this.perPage)
            },
            paginatedData() {
                const start = (this.page - 1) * this.perPage
                return this.filteredData.slice(start, start + this.perPage)
            }
        },
        mounted() {
            this.modal = new bootstrap.Modal(document.getElementById('suplierModal'))
            this.toast = new bootstrap.Toast(document.getElementById('suplierToast'))
            this.loadData()
            lucide.createIcons()
        },
        updated() {
            lucide.createIcons()
        },
        methods: {
            showToast(msg) {
                this.toastMsg = msg
                this.toast.show()
            },
            loadData() {
                axios.get('<?= base_url('suplier/list') ?>').then(res => this.suplier = res.data.data)
            },
            openModal(item = null) {
                this.errors = {}
                this.form = item ? {
                    ...item
                } : {
                    id: null,
                    nama: '',
                    alamat: '',
                    telepon: '',
                    email: ''
                }
                this.modal.show()
            },
            simpanSuplier() {
                let url = this.form.id ? `<?= base_url('suplier/update') ?>/${this.form.id}` : `<?= base_url('suplier/store') ?>`
                axios.post(url, this.form)
                    .then(res => {
                        this.showToast(res.data.message)
                        this.loadData()
                        this.modal.hide()
                    })
                    .catch(err => {
                        this.errors = err.response?.data?.errors || {}
                    })
            },
            hapusSuplier(id) {
                if (confirm('Hapus suplier ini?')) {
                    axios.delete(`<?= base_url('suplier/delete') ?>/${id}`)
                        .then(res => {
                            this.showToast(res.data.message)
                            this.loadData()
                        })
                }
            }
        }
    }).mount('#app')
</script>
<?= $this->endSection() ?>