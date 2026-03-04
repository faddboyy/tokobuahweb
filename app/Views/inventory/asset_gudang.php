<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<div id="app" v-cloak class="h-100 d-flex flex-column">

    <div class="glass-panel p-4 flex-fill d-flex flex-column gap-4">

        <!-- HEADER -->
        <div>
            <h5 class="fw-bold mb-1 d-flex align-items-center gap-2">
                <i data-lucide="warehouse" style="width:24px"></i>
                Aset Gudang
            </h5>
            <p class="text-muted small mb-0">
                Pilih gudang untuk melihat stok barang
            </p>
        </div>

        <!-- FILTER GUDANG -->
        <div class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label fw-semibold small text-muted mb-1">
                    PILIH GUDANG
                </label>
                <select class="form-select"
                    v-model="filter.gudang_id">
                    <option value="">-- Pilih Gudang --</option>
                    <option v-for="g in gudangList"
                        :key="g.id"
                        :value="g.id">
                        {{ g.nama }}
                    </option>
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label fw-semibold small text-muted mb-1">
                    CARI BARANG
                </label>
                <input type="text"
                    class="form-control"
                    placeholder="Nama atau barcode..."
                    v-model="filter.q">
            </div>
        </div>

        <!-- TABLE -->
        <div class="card border-0 shadow-sm flex-fill d-flex flex-column overflow-hidden">

            <div class="card-body p-0 flex-fill overflow-auto" style="max-height:60vh">

                <!-- Belum pilih gudang -->
                <div v-if="!filter.gudang_id"
                    class="text-center py-5">
                    <i data-lucide="warehouse"
                        style="width:48px;height:48px;opacity:.2;display:block;margin:0 auto 12px"></i>
                    <p class="text-muted fw-semibold mb-1">
                        Silakan pilih gudang terlebih dahulu
                    </p>
                </div>

                <!-- Loading -->
                <div v-else-if="loading"
                    class="text-center py-5">
                    <div class="spinner-border text-primary mb-3"
                        style="width:2.5rem;height:2.5rem"></div>
                    <p class="text-muted small">Memuat data...</p>
                </div>

                <!-- Kosong -->
                <div v-else-if="!data.length"
                    class="text-center py-5">
                    <i data-lucide="inbox"
                        style="width:48px;height:48px;opacity:.2;display:block;margin:0 auto 12px"></i>
                    <p class="text-muted fw-semibold mb-1">
                        Tidak ada data stok
                    </p>
                </div>

                <!-- Tabel -->
                <table v-else class="table table-hover mb-0">
                    <thead class="table-light sticky-top">
                        <tr>
                            <th style="width:40px">#</th>
                            <th>Nama Barang</th>
                            <th style="width:150px">Barcode</th>
                            <th style="width:120px" class="text-end">Qty</th>
                            <th style="width:120px">Satuan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(row, idx) in data"
                            :key="row.barang_id">
                            <td class="text-muted small">
                                {{ idx + 1 }}
                            </td>
                            <td class="fw-semibold">
                                {{ row.nama_barang }}
                            </td>
                            <td class="text-muted small">
                                {{ row.barcode }}
                            </td>
                            <td class="text-end fw-bold">
                                {{ formatNum(row.total_qty) }}
                            </td>
                            <td>
                                {{ row.satuan ?? '-' }}
                            </td>
                        </tr>
                    </tbody>
                </table>

            </div>
        </div>

    </div>
</div>

<style>
    [v-cloak] {
        display: none;
    }

    .glass-panel {
        background: rgba(255, 255, 255, .88);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, .5);
        border-radius: 16px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, .07);
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
                loading: false,
                data: [],
                gudangList: <?= json_encode($gudang_list) ?>,
                filter: {
                    gudang_id: '',
                    q: ''
                }
            }
        },

        watch: {
            'filter.gudang_id'(val) {
                if (val) this.load();
                else this.data = [];
            },
            'filter.q'() {
                if (this.filter.gudang_id) {
                    this.load();
                }
            }
        },

        methods: {
            async load() {
                this.loading = true;
                try {
                    const params = new URLSearchParams(this.filter).toString();
                    const res = await axios.get(`<?= base_url('aset-gudang/data') ?>?${params}`);
                    this.data = res.data.data;
                    this.$nextTick(() => lucide.createIcons());
                } catch (e) {
                    console.error(e);
                } finally {
                    this.loading = false;
                }
            },

            formatNum(x) {
                return new Intl.NumberFormat('id-ID').format(x ?? 0);
            }
        },

        mounted() {
            lucide.createIcons();
        },

        updated() {
            lucide.createIcons();
        }

    }).mount('#app');
</script>
<?= $this->endSection() ?>