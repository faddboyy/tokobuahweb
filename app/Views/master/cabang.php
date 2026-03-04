<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div id="appCabang" v-cloak>
    <div class="glass-panel p-4 h-100 d-flex flex-column border-0 shadow-lg position-relative overflow-hidden">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-0 text-dark">Master Cabang</h4>
                <p class="text-muted small mb-0 d-flex align-items-center">
                    <i data-lucide="map-pin" class="me-1" style="width: 14px;"></i>
                    Lokasi operasional dan penugasan staf
                </p>
            </div>
            <button class="btn btn-primary shadow-sm px-3 d-flex align-items-center gap-2" @click="openModal()">
                <i data-lucide="plus-circle" style="width: 18px;"></i> Tambah Cabang
            </button>
        </div>

        <div class="glass-panel bg-white bg-opacity-50 p-2 mb-4 d-flex align-items-center border-white shadow-sm" style="border-radius: 12px;">
            <div class="input-group input-group-sm border-0">
                <span class="input-group-text bg-transparent border-0 pe-1">
                    <i data-lucide="search" class="text-muted" style="width: 16px;"></i>
                </span>
                <input type="text" class="form-control border-0 bg-transparent shadow-none" placeholder="Cari nama cabang..." v-model="search">
            </div>
        </div>

        <div class="table-responsive flex-fill custom-scroll">
            <table class="table table-hover align-middle border-0">
                <thead>
                    <tr class="text-muted small text-uppercase">
                        <th class="border-0 ps-3" style="width: 30%;">Nama Cabang</th>
                        <th class="border-0">Petugas Terdaftar</th>
                        <th class="border-0 text-end pe-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    <tr v-for="c in filteredData" :key="c.id" class="table-row-modern">
                        <td class="ps-3 fw-bold text-dark">{{ c.nama }}</td>
                        <td>
                            <div v-if="c.petugas" class="d-flex align-items-center gap-2">
                                <div class="bg-primary bg-opacity-10 p-1 rounded">
                                    <i data-lucide="users" class="text-primary" style="width: 14px;"></i>
                                </div>
                                <span class="small text-muted">{{ c.petugas }}</span>
                            </div>
                            <span v-else class="text-danger small fst-italic">Belum ada petugas ditugaskan</span>
                        </td>
                        <td class="text-end pe-3">
                            <div class="d-flex justify-content-end gap-1">
                                <button class="btn btn-icon btn-light-warning" @click="openModal(c)">
                                    <i data-lucide="edit-3" style="width: 16px;"></i>
                                </button>
                                <button class="btn btn-icon btn-light-danger" @click="hapus(c.id)">
                                    <i data-lucide="trash-2" style="width: 16px;"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal fade" id="cabangModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                <div class="modal-header border-0 pb-0">
                    <h5 class="fw-bold text-dark">{{ form.id ? 'Edit Cabang' : 'Tambah Cabang Baru' }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" @click="resetForm"></button>
                </div>
                <div class="modal-body py-4">
                    <div class="mb-3">
                        <label class="small fw-bold text-muted mb-1">Nama Cabang</label>
                        <input type="text" class="form-control modern-input" id="inputNama"
                               :class="{'is-invalid': serverErrors.nama}" v-model="form.nama">
                        <div class="invalid-feedback" v-if="serverErrors.nama">{{ serverErrors.nama }}</div>
                    </div>

                    <div class="mb-2">
                        <label class="small fw-bold text-muted mb-2 d-block">Pilih Petugas</label>
                        <div class="petugas-selection-box border rounded-3 p-1 bg-light bg-opacity-50">
                            <div v-for="p in allPetugas" :key="p.id" class="form-check p-2 border-bottom border-white d-flex align-items-center mb-0">
                                <input class="form-check-input ms-0 me-3" type="checkbox" :id="'p'+p.id" :value="p.id" v-model="form.petugas_ids" @change="checkTransfer(p, $event)">
                                <label class="form-check-label small w-100 cursor-pointer" :for="'p'+p.id">
                                    <div class="fw-bold text-dark">{{ p.nama }}</div>
                                    <div class="text-muted" style="font-size: 10px;">
                                        <span v-if="p.cabang_id && Number(p.cabang_id) === Number(form.id)" class="text-success">Status: Di cabang ini</span>
                                        <span v-else-if="p.cabang_id" class="text-warning">Status: Di cabang lain</span>
                                        <span v-else class="text-muted">Status: Belum ada cabang</span>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4">
                    <button class="btn btn-light px-4 border shadow-sm fw-semibold" data-bs-dismiss="modal" @click="resetForm">Batal</button>
                    <button class="btn btn-primary px-4 fw-bold shadow-sm" @click="simpan" :disabled="loading">
                        <span v-if="loading" class="spinner-border spinner-border-sm me-1"></span>
                        <i v-else data-lucide="save" class="me-1" style="width: 16px;"></i> Simpan
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 9999;">
        <div id="liveToast" class="toast border-0 shadow-lg" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex align-items-center p-2 bg-white rounded-3">
                <div class="bg-success bg-opacity-10 p-2 rounded-2 me-3">
                    <i data-lucide="check-circle" class="text-success" style="width: 20px;"></i>
                </div>
                <div class="toast-body fw-bold small text-dark flex-grow-1">
                    {{ toastMessage }}
                </div>
                <button type="button" class="btn-close ms-2" data-bs-dismiss="toast"></button>
            </div>
        </div>
    </div>
</div>

<style>
    .petugas-selection-box { max-height: 220px; overflow-y: auto; scrollbar-width: thin; }
    .btn-icon { width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border-radius: 8px; border:none; }
    .modern-input { border: 1.5px solid #eee; border-radius: 8px; padding: 0.6rem; }
    [v-cloak] { display: none; }
</style>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    const { createApp, nextTick } = Vue;

    createApp({
        data() {
            return {
                cabang: [], allPetugas: [], search: '',
                form: { id: null, nama: '', petugas_ids: [] },
                serverErrors: {}, toastMessage: '', loading: false
            }
        },
        computed: {
            filteredData() {
                return this.cabang.filter(c => c.nama.toLowerCase().includes(this.search.toLowerCase()));
            }
        },
        mounted() {
            this.load();
            this.loadPetugas();
        },
        updated() {
            // Trigger Lucide setiap kali Vue merender ulang data
            this.refreshIcons();
        },
        methods: {
            refreshIcons() {
                if (typeof lucide !== 'undefined') {
                    lucide.createIcons();
                }
            },
            showToast(msg) {
                this.toastMessage = msg;
                nextTick(() => {
                    const toastEl = document.getElementById('liveToast');
                    const bsToast = new bootstrap.Toast(toastEl);
                    bsToast.show();
                    this.refreshIcons();
                });
            },
            resetForm() {
                this.form = { id: null, nama: '', petugas_ids: [] };
                this.serverErrors = {};
                this.loading = false;
            },
            load() {
                axios.get('<?= base_url('cabang/list') ?>').then(res => {
                    this.cabang = res.data.data;
                    nextTick(() => this.refreshIcons());
                });
            },
            loadPetugas() {
                axios.get('<?= base_url('cabang/petugas-list') ?>').then(res => {
                    this.allPetugas = res.data.data;
                });
            },
            openModal(cabang = null) {
                this.resetForm();
                if (cabang) {
                    const cID = Number(cabang.id);
                    // Ambil ID petugas yang saat ini terikat ke cabang ini
                    const ids = this.allPetugas
                        .filter(p => p.cabang_id && Number(p.cabang_id) === cID)
                        .map(p => Number(p.id));
                    
                    this.form = { id: cID, nama: cabang.nama, petugas_ids: ids };
                }
                const modal = new bootstrap.Modal(document.getElementById('cabangModal'));
                modal.show();
                this.refreshIcons();
            },
            checkTransfer(petugas, event) {
                if (event.target.checked && petugas.cabang_id && Number(petugas.cabang_id) !== Number(this.form.id)) {
                    if (!confirm(`Petugas "${petugas.nama}" sudah terikat di cabang lain. Pindahkan ke sini?`)) {
                        this.form.petugas_ids = this.form.petugas_ids.filter(id => id !== petugas.id);
                    }
                }
            },
            simpan() {
                this.loading = true;
                this.serverErrors = {};
                const url = this.form.id ? `<?= base_url('cabang/update') ?>/${this.form.id}` : `<?= base_url('cabang/store') ?>`;
                
                axios.post(url, Qs.stringify(this.form))
                .then(res => {
                    this.showToast(res.data.message);
                    this.load();
                    this.loadPetugas();
                    bootstrap.Modal.getInstance(document.getElementById('cabangModal')).hide();
                    this.resetForm();
                })
                .catch(err => {
                    if (err.response && err.response.status === 400) {
                        this.serverErrors = err.response.data.messages;
                    } else {
                        this.showToast("Terjadi kesalahan sistem.");
                    }
                })
                .finally(() => this.loading = false);
            },
            hapus(id) {
                if (confirm('Hapus cabang ini? Semua petugas terkait akan dilepaskan.')) {
                    axios.delete(`<?= base_url('cabang/delete') ?>/${id}`).then(res => {
                        this.showToast(res.data.message);
                        this.load();
                        this.loadPetugas();
                    });
                }
            }
        }
    }).mount('#appCabang');
</script>
<?= $this->endSection() ?>