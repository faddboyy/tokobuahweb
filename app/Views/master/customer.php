<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="glass-panel p-4 h-100 d-flex flex-column border-0 shadow-lg position-relative overflow-hidden" id="app">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0 text-dark">Master Customer</h4>
            <p class="text-muted small mb-0">Manajemen data pelanggan</p>
        </div>
        <button class="btn btn-primary shadow-sm px-3 d-flex align-items-center gap-2" @click="openModal()">
            <i data-lucide="plus-circle" style="width: 18px;"></i> Tambah Customer
        </button>
    </div>

    <div class="glass-panel bg-white bg-opacity-50 p-2 mb-4 d-flex align-items-center border-white shadow-sm" style="border-radius: 12px;">
        <div class="input-group input-group-sm border-0">
            <span class="input-group-text bg-transparent border-0 pe-1">
                <i data-lucide="search" class="text-muted" style="width: 16px;"></i>
            </span>
            <input type="text" class="form-control border-0 bg-transparent shadow-none" placeholder="Cari pelanggan..." v-model="search">
        </div>
    </div>

    <div class="table-responsive flex-fill">
        <table class="table table-hover align-middle border-0">
            <thead>
                <tr class="text-muted small text-uppercase">
                    <th class="border-0 ps-3">No</th>
                    <th class="border-0">Nama & Kontak</th>
                    <th class="border-0">Cabang</th>
                    <th class="border-0">Ditambahkan Oleh</th>
                    <th class="border-0 text-end pe-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(c, index) in paginatedData" :key="c.id">
                    <td class="ps-3 text-muted">{{ (page - 1) * perPage + index + 1 }}</td>
                    <td>
                        <div class="fw-bold text-dark">{{ c.nama }}</div>
                        <div class="small text-muted">{{ c.telepon }}</div>
                    </td>
                    <td>
                        <span v-if="c.nama_cabang" class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25">
                            {{ c.nama_cabang }}
                        </span>
                        <span v-else class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25">
                            Global
                        </span>
                    </td>
                    <td>
                        <div class="small fw-semibold text-dark"><i data-lucide="user" class="me-1" style="width:12px"></i>{{ c.pembuat }}</div>
                    </td>
                    <td class="text-end pe-3">
                        <button v-if="canEdit(c)"
                            class="btn btn-sm btn-light-warning me-1"
                            @click="openModal(c)">
                            <i data-lucide="edit-3" style="width:14px"></i>
                        </button>

                        <button v-if="canEdit(c)"
                            class="btn btn-sm btn-light-danger"
                            @click="hapus(c.id)">
                            <i data-lucide="trash-2" style="width:14px"></i>
                        </button>
                    </td>


                </tr>
            </tbody>
        </table>
    </div>

    <div class="modal fade" id="customerModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow" style="border-radius: 15px;">
                <div class="modal-header">
                    <h5 class="fw-bold mb-0">{{ form.id ? 'Edit' : 'Tambah' }} Customer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="small fw-bold text-muted">Nama Pelanggan</label>
                        <input type="text" class="form-control" v-model="form.nama" :class="{'is-invalid': errors.nama}">
                        <div class="invalid-feedback" v-if="errors.nama">{{ errors.nama }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="small fw-bold text-muted">Telepon</label>
                        <input type="text" class="form-control" v-model="form.telepon" :class="{'is-invalid': errors.telepon}">
                        <div class="invalid-feedback" v-if="errors.telepon">{{ errors.telepon }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="small fw-bold text-muted">Alamat</label>
                        <textarea class="form-control" rows="2" v-model="form.alamat" :class="{'is-invalid': errors.alamat}"></textarea>
                        <div class="invalid-feedback" v-if="errors.alamat">{{ errors.alamat }}</div>
                    </div>

                    <?php if (session()->get('role') !== 'petugas'): ?>
                        <div class="mb-2">
                            <label class="small fw-bold text-muted d-block mb-2">Penempatan Cabang (Opsional)</label>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" :value="null" v-model="form.cabang_id" id="radGlobal">
                                <label class="form-check-label small" for="radGlobal">Global / Tanpa Cabang</label>
                            </div>
                            <div v-for="cb in listCabang" :key="cb.id" class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" :value="cb.id" v-model="form.cabang_id" :id="'rad'+cb.id">
                                <label class="form-check-label small" :for="'rad'+cb.id">{{ cb.nama }}</label>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="modal-footer border-0">
                    <button class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button class="btn btn-primary px-4" @click="simpan">Simpan</button>
                </div>
            </div>
        </div>
    </div>

    <div class="position-fixed top-0 end-0 p-4" style="z-index: 9999;">
        <div id="liveToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true"
            style="background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px); border: 1px solid #dbeafe; border-left: 4px solid #0d6efd; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.1);">
            <div class="d-flex align-items-center p-2">
                <i data-lucide="check-circle" style="width: 20px; height: 20px; color: #0d6efd;" class="me-2"></i>

                <div class="toast-body fw-semibold" style="color: #1e293b; padding: 0.5rem;">
                    {{ toastMessage }}
                </div>

                <button type="button" class="btn-close ms-auto" data-bs-dismiss="toast" aria-label="Close" style="font-size: 0.75rem;"></button>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    const {
        createApp
    } = Vue;

    createApp({
        data() {
            return {
                customer: [],
                listCabang: [],
                search: '',
                page: 1,
                perPage: 10,
                form: {
                    id: null,
                    nama: '',
                    alamat: '',
                    telepon: '',
                    cabang_id: null
                },
                errors: {},
                modal: null,
                toast: null,
                toastMessage: '',

                // 🔥 ambil dari session
                role: "<?= session()->get('role') ?>",
                userId: <?= session()->get('user_id') ?>,
                userName: "<?= session()->get('nama') ?>" // TAMBAHAN PENTING
            }
        },

        computed: {
            filteredData() {
                return this.customer.filter(c =>
                    c.nama.toLowerCase().includes(this.search.toLowerCase())
                )
            },

            paginatedData() {
                const start = (this.page - 1) * this.perPage
                return this.filteredData.slice(start, start + this.perPage)
            }
        },

        mounted() {
            this.modal = new bootstrap.Modal(document.getElementById('customerModal'))
            this.toast = new bootstrap.Toast(document.getElementById('liveToast'))
            this.loadData();
            lucide.createIcons();
        },

        updated() {
            lucide.createIcons();
        },

        methods: {

            // 🔐 LOGIC TAMPILKAN TOMBOL
            canEdit(customer) {

                // selain petugas → boleh semua
                if (this.role !== 'petugas') {
                    return true;
                }

                // jika petugas → hanya jika pembuat == user login
                return customer.pembuat === this.userName;
            },

            showToast(msg) {
                this.toastMessage = msg;
                this.toast.show();
            },

            loadData() {
                axios.get('<?= base_url('customer/list') ?>')
                    .then(res => {
                        this.customer = res.data.data
                        this.listCabang = res.data.listCabang
                    })
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
                    cabang_id: null
                }

                this.modal.show()
            },

            simpan() {
                this.errors = {}

                let url = this.form.id ?
                    `<?= base_url('customer/update') ?>/${this.form.id}` :
                    `<?= base_url('customer/store') ?>`

                axios.post(url, this.form)
                    .then(res => {
                        if (res.data.status) {
                            this.showToast(res.data.message);
                            this.loadData();
                            this.modal.hide();
                        }
                    })
                    .catch(err => {
                        if (err.response && err.response.status === 422) {
                            this.errors = err.response.data.errors;
                        } else {
                            console.error("Terjadi kesalahan sistem");
                        }
                    })
            },

            hapus(id) {
                if (confirm('Apakah Anda yakin ingin menghapus data formal ini?')) {
                    axios.delete(`<?= base_url('customer/delete') ?>/${id}`)
                        .then(res => {
                            this.showToast(res.data.message);
                            this.loadData();
                        })
                }
            }
        }

    }).mount('#app')
</script>
<?= $this->endSection() ?>