<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="glass-panel p-4 h-100 d-flex flex-column border-0 shadow-lg position-relative overflow-hidden">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0 text-dark">Master User</h4>
            <p class="text-muted small mb-0 d-flex align-items-center">
                <i data-lucide="shield-check" class="me-1" style="width: 14px;"></i>
                Konfigurasi hak akses dan kredensial pengguna
            </p>
        </div>
        <button class="btn btn-primary shadow-sm px-3 d-flex align-items-center gap-2"
            style="border-radius: 8px; font-weight: 600;"
            @click="openModal()">
            <i data-lucide="user-plus" style="width: 18px;"></i>
            Tambah User
        </button>
    </div>

    <div class="glass-panel bg-white bg-opacity-50 p-2 mb-4 d-flex align-items-center border-white shadow-sm" style="border-radius: 12px;">
        <div class="input-group input-group-sm border-0">
            <span class="input-group-text bg-transparent border-0 pe-1">
                <i data-lucide="search" class="text-muted" style="width: 16px;"></i>
            </span>
            <input type="text" class="form-control border-0 bg-transparent shadow-none"
                placeholder="Cari berdasarkan username atau nama lengkap..."
                v-model="search" @input="page = 1">
        </div>
    </div>

    <div class="table-responsive flex-fill custom-scroll">
        <table class="table table-hover align-middle border-0">
            <thead>
                <tr class="text-muted small text-uppercase">
                    <th class="border-0 ps-3">Username</th>
                    <th class="border-0">Nama Lengkap</th>
                    <th class="border-0 text-center">Role</th>
                    <th class="border-0 text-center">Status</th>
                    <th class="border-0 text-end pe-3">Aksi</th>
                </tr>
            </thead>
            <tbody class="border-top-0">
                <tr v-for="u in paginatedData" :key="u.id" class="table-row-modern">
                    <td class="ps-3 fw-semibold text-dark">{{ u.username }}</td>
                    <td>{{ u.nama }}</td>
                    <td class="text-center">
                        <span :class="{
                            'badge-role admin': u.role === 'admin',
                            'badge-role owner': u.role === 'owner',
                            'badge-role petugas': u.role === 'petugas'
                        }">{{ u.role }}</span>
                    </td>
                    <td class="text-center">
                        <div class="form-check form-switch d-flex justify-content-center">
                            <input class="form-check-input custom-switch"
                                type="checkbox"
                                :checked="u.is_active == 1"
                                @change="toggleActive(u)">
                        </div>
                    </td>
                    <td class="text-end pe-3">
                        <div class="d-flex justify-content-end gap-1">
                            <button class="btn btn-icon btn-light-warning" @click="openModal(u)">
                                <i data-lucide="edit-3" style="width: 16px;"></i>
                            </button>
                            <button class="btn btn-icon btn-light-danger" @click="hapus(u.id)">
                                <i data-lucide="trash-2" style="width: 16px;"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
        <small class="text-muted fw-medium">Menampilkan {{ paginatedData.length }} user</small>
        <div class="pagination-container d-flex gap-1">
            <button class="btn btn-pagination" :disabled="page==1" @click="page--">
                <i data-lucide="chevron-left" style="width: 16px;"></i>
            </button>
            <div class="px-3 d-flex align-items-center fw-bold small text-primary bg-primary bg-opacity-10 rounded-3">
                {{ page }} / {{ totalPage || 1 }}
            </div>
            <button class="btn btn-pagination" :disabled="page>=totalPage" @click="page++">
                <i data-lucide="chevron-right" style="width: 16px;"></i>
            </button>
        </div>
    </div>
</div>

<div class="modal fade force-front" id="userModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg modal-elevated">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-dark">{{ form.id ? 'Edit User' : 'Tambah User Baru' }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body py-4">
                <div v-if="globalError" class="alert alert-danger border-0 small py-2 mb-3 shadow-sm">{{ globalError }}</div>

                <div class="row g-3">
                    <div class="col-12">
                        <label class="small fw-bold text-muted mb-1">Username</label>
                        <input type="text" class="form-control modern-input" :class="{'is-invalid': errors.username}" v-model="form.username" placeholder="cth: admin_toko">
                        <span class="error-text" v-if="errors.username">{{ errors.username }}</span>
                    </div>
                    <div class="col-12">
                        <label class="small fw-bold text-muted mb-1">Nama Lengkap</label>
                        <input type="text" class="form-control modern-input" :class="{'is-invalid': errors.nama}" v-model="form.nama" placeholder="Masukkan nama lengkap">
                        <span class="error-text" v-if="errors.nama">{{ errors.nama }}</span>
                    </div>
                    <div class="col-12">
                        <label class="small fw-bold text-muted mb-1">Password {{ form.id ? '(Kosongkan jika tidak diubah)' : '' }}</label>
                        <input type="password" class="form-control modern-input" :class="{'is-invalid': errors.password}" v-model="form.password" placeholder="••••••••">
                        <span class="error-text" v-if="errors.password">{{ errors.password }}</span>
                    </div>
                    <div class="col-md-7">
                        <label class="small fw-bold text-muted mb-1">Role Akses</label>
                        <select class="form-select modern-input" :class="{'is-invalid': errors.role}" v-model="form.role">
                            <option value="">Pilih Role...</option>
                            <option value="owner">Owner</option>
                            <option value="admin">Admin</option>
                            <option value="petugas">Petugas</option>
                        </select>
                        <span class="error-text" v-if="errors.role">{{ errors.role }}</span>
                    </div>
                    <div class="col-md-5 d-flex align-items-end">
                        <div class="form-check form-switch bg-light p-2 rounded-3 w-100 d-flex justify-content-between px-3 border shadow-sm">
                            <label class="small fw-bold text-muted m-0">Aktif</label>
                            <input class="form-check-input" type="checkbox" v-model="form.is_active">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 px-4 pb-4">
                <button class="btn btn-light px-4 border shadow-sm fw-semibold" data-bs-dismiss="modal">Batal</button>
                <button class="btn btn-primary px-4 fw-bold shadow-sm" @click="simpan">
                    <i data-lucide="save" class="me-1" style="width: 16px;"></i> Simpan
                </button>
            </div>
        </div>
    </div>
</div>

<div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 9999;">
    <div id="liveToast" class="toast border-0 shadow-lg" role="alert">
        <div class="d-flex p-2">
            <div class="toast-body fw-bold small text-primary">{{ toastMessage }}</div>
            <button type="button" class="btn-close m-auto me-2" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>

<style>
    /* FIX MODAL STACKING CONTEXT - FORCE FRONT */
    body.modal-open .glass-panel {
        filter: none !important;
        /* Matikan blur pada panel saat modal buka */
    }

    .force-front {
        z-index: 9999 !important;
        position: fixed !important;
    }

    .modal-backdrop {
        z-index: 9998 !important;
    }

    .modal-elevated {
        border-radius: 16px;
        background: #ffffff !important;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3) !important;
    }

    /* Input & Badge Styling */
    .badge-role {
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
        padding: 4px 10px;
        border-radius: 6px;
    }

    .badge-role.admin {
        background: rgba(0, 103, 192, 0.1);
        color: #0067c0;
    }

    .badge-role.owner {
        background: rgba(102, 16, 242, 0.1);
        color: #6610f2;
    }

    .badge-role.petugas {
        background: rgba(108, 117, 125, 0.1);
        color: #6c757d;
    }

    .btn-icon {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        border: none;
        transition: 0.2s;
    }

    .btn-light-warning {
        background: rgba(255, 193, 7, 0.15);
        color: #997404;
    }

    .btn-light-danger {
        background: rgba(220, 53, 69, 0.15);
        color: #dc3545;
    }

    .modern-input {
        background: #fff;
        border: 1.5px solid #eee;
        border-radius: 8px;
        padding: 0.6rem;
        transition: all 0.2s;
    }

    .modern-input:focus {
        border-color: #0067c0;
        box-shadow: 0 0 0 4px rgba(0, 103, 192, 0.1);
    }

    .modern-input.is-invalid {
        border-color: #dc3545 !important;
        background-color: #fff8f8;
    }

    .error-text {
        color: #dc3545;
        font-size: 11px;
        font-weight: 600;
        margin-top: 4px;
        display: block;
    }

    .custom-switch {
        width: 3em !important;
        height: 1.5em !important;
        cursor: pointer;
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
                users: [],
                search: '',
                page: 1,
                perPage: 8,
                form: {
                    id: null,
                    username: '',
                    nama: '',
                    password: '',
                    role: '',
                    is_active: true
                },
                errors: {},
                globalError: '',
                toastMessage: ''
            }
        },
        computed: {
            filteredData() {
                return this.users.filter(u =>
                    u.username.toLowerCase().includes(this.search.toLowerCase()) ||
                    u.nama.toLowerCase().includes(this.search.toLowerCase())
                )
            },
            totalPage() {
                return Math.ceil(this.filteredData.length / this.perPage)
            },
            paginatedData() {
                const start = (this.page - 1) * this.perPage;
                return this.filteredData.slice(start, start + this.perPage);
            }
        },
        mounted() {
            this.load();
            lucide.createIcons();
        },
        updated() {
            lucide.createIcons();
        },
        methods: {
            load() {
                axios.get('<?= base_url('user/list') ?>').then(res => {
                    this.users = res.data.data;
                });
            },
            openModal(user = null) {
                this.errors = {};
                this.globalError = '';
                if (user) {
                    this.form = {
                        ...user,
                        password: '',
                        is_active: user.is_active == 1
                    };
                } else {
                    this.form = {
                        id: null,
                        username: '',
                        nama: '',
                        password: '',
                        role: '',
                        is_active: true
                    };
                }
                const modal = new bootstrap.Modal(document.getElementById('userModal'));
                modal.show();
            },
            simpan() {
                this.errors = {};
                this.globalError = '';
                let url = this.form.id ? `<?= base_url('user/update') ?>/${this.form.id}` : `<?= base_url('user/store') ?>`;
                axios.post(url, {
                        ...this.form,
                        is_active: this.form.is_active ? 1 : 0
                    })
                    .then(res => {
                        this.showToast(res.data.message);
                        this.load();
                        bootstrap.Modal.getInstance(document.getElementById('userModal')).hide();
                    })
                    .catch(err => {
                        if (err.response?.status === 422) {
                            this.errors = err.response.data.errors;
                            this.globalError = "Validasi Gagal.";
                        } else {
                            this.globalError = "Terjadi kesalahan sistem.";
                        }
                    });
            },
            showToast(msg) {
                this.toastMessage = msg;
                new bootstrap.Toast(document.getElementById('liveToast')).show();
            },
            hapus(id) {
                if (confirm('Hapus user ini?')) {
                    axios.delete(`<?= base_url('user/delete') ?>/${id}`).then(res => {
                        this.showToast(res.data.message);
                        this.load();
                    });
                }
            },
            toggleActive(user) {
                axios.post(`<?= base_url('user/toggle') ?>/${user.id}`).then(() => {
                    this.showToast("Status diubah");
                    this.load();
                });
            }
        }
    }).mount('#app');
</script>
<?= $this->endSection() ?>