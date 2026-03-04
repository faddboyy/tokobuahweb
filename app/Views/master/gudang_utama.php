<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<div id="appGudang" v-cloak>
    <div class="glass-panel p-4 h-100 d-flex flex-column border-0 shadow-lg">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-0 text-dark">Master Gudang</h4>
                <p class="text-muted small mb-0">
                    Pengelolaan gudang dan mandor
                </p>
            </div>

            <button class="btn btn-primary"
                @click="openModal()">
                <i data-lucide="plus-circle" style="width:18px"></i>
                Tambah Gudang
            </button>
        </div>

        <div class="table-responsive flex-fill">
            <table class="table table-hover align-middle">
                <thead>
                    <tr class="text-muted small text-uppercase">
                        <th>Nama Gudang</th>
                        <th>Mandor</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="g in gudang" :key="g.id">
                        <td class="fw-bold">{{ g.nama }}</td>
                        <td>
                            <span v-if="g.mandor_nama">
                                {{ g.mandor_nama }}
                            </span>
                            <span v-else class="text-danger small">
                                Belum ada mandor
                            </span>
                        </td>
                        <td class="text-end">
                            <button class="btn btn-light-warning btn-icon"
                                @click="openModal(g)">
                                <i data-lucide="edit-3" style="width:16px"></i>
                            </button>

                            <button class="btn btn-light-danger btn-icon"
                                @click="hapus(g.id)">
                                <i data-lucide="trash-2" style="width:16px"></i>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- MODAL -->
    <div class="modal fade" id="gudangModal" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-0">
                    <h5 class="fw-bold">
                        {{ form.id ? 'Edit Gudang' : 'Tambah Gudang' }}
                    </h5>
                    <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="small fw-bold text-muted">Nama Gudang</label>
                        <input type="text"
                            class="form-control"
                            v-model="form.nama"
                            :class="{'is-invalid': errors.nama}">
                        <div class="invalid-feedback"
                            v-if="errors.nama">
                            {{ errors.nama }}
                        </div>
                    </div>

                    <div>
                        <label class="small fw-bold text-muted">Mandor</label>
                        <select class="form-select"
                            v-model="form.mandor_id">
                            <option value="">-- Pilih Mandor --</option>
                            <option v-for="m in mandorList"
                                :value="m.id">
                                {{ m.nama }}
                            </option>
                        </select>
                    </div>
                </div>

                <div class="modal-footer border-0">
                    <button class="btn btn-light"
                        data-bs-dismiss="modal">Batal</button>
                    <button class="btn btn-primary"
                        @click="simpan"
                        :disabled="loading">
                        <span v-if="loading"
                            class="spinner-border spinner-border-sm"></span>
                        <span v-else>Simpan</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
[v-cloak] { display:none; }
.btn-icon {
    width:32px;
    height:32px;
    display:flex;
    align-items:center;
    justify-content:center;
}
</style>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
const { createApp, nextTick } = Vue;

createApp({
    data() {
        return {
            gudang: [],
            mandorList: [],
            form: { id:null, nama:'', mandor_id:'' },
            errors:{},
            loading:false
        }
    },
    mounted() {
        this.load();
        this.loadMandor();
    },
    updated() {
        if (typeof lucide !== 'undefined')
            lucide.createIcons();
    },
    methods: {
        load() {
            axios.get("<?= base_url('gudangutama/list') ?>")
                .then(res => this.gudang = res.data.data);
        },
        loadMandor() {
            axios.get("<?= base_url('gudangutama/mandor-list') ?>")
                .then(res => this.mandorList = res.data.data);
        },
        openModal(g=null) {
            this.errors = {};
            if (g) this.form = {...g};
            else this.form = { id:null, nama:'', mandor_id:'' };

            new bootstrap.Modal(
                document.getElementById('gudangModal')
            ).show();
        },
        simpan() {
            this.loading = true;
            const url = this.form.id
                ? `<?= base_url('gudangutama/update') ?>/${this.form.id}`
                : `<?= base_url('gudangutama/store') ?>`;

            axios.post(url, Qs.stringify(this.form))
                .then(res => {
                    this.load();
                    bootstrap.Modal.getInstance(
                        document.getElementById('gudangModal')
                    ).hide();
                })
                .catch(err => {
                    if (err.response?.status === 400)
                        this.errors = err.response.data.messages;
                })
                .finally(() => this.loading = false);
        },
        hapus(id) {
            if (confirm('Hapus gudang ini?')) {
                axios.delete(
                    `<?= base_url('gudangutama/delete') ?>/${id}`
                ).then(() => this.load());
            }
        }
    }
}).mount('#appGudang');
</script>
<?= $this->endSection() ?>