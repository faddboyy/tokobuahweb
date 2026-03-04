<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<div id="app" v-cloak class="container-fluid py-4">

    <!-- ══ HEADER ══════════════════════════════════════════════════════════ -->
    <div class="glass-panel p-4 mb-4 border-0 shadow-sm"
        style="border-radius:20px;background:linear-gradient(145deg,#ffffff,#f8f9fa)">
        <div class="row align-items-center">
            <div class="col-md-7">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-danger bg-opacity-10 p-3 rounded-4 text-danger">
                        <i data-lucide="tag" style="width:32px;height:32px"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-0 text-dark">Diskon Terbatas</h4>
                        <p class="text-muted small mb-0">Kelola periode diskon nominal per barang per cabang</p>
                    </div>
                </div>
            </div>
            <div class="col-md-5 text-md-end mt-3 mt-md-0">
                <button class="btn btn-danger fw-bold rounded-3 px-4 shadow-sm d-inline-flex align-items-center gap-2"
                    @click="openModal()">
                    <i data-lucide="plus-circle" style="width:16px"></i> Tambah Periode
                </button>
            </div>
        </div>
    </div>

    <!-- ══ FILTER ═══════════════════════════════════════════════════════════ -->
    <div class="glass-panel p-3 mb-4 border-0 shadow-sm bg-white" style="border-radius:16px">
        <div class="row g-3 align-items-end">

            <div class="col-lg-3 col-md-12">
                <label class="small fw-bold text-muted mb-1">
                    <i data-lucide="search" style="width:12px" class="me-1"></i>Cari Nama Periode
                </label>
                <div class="input-group input-group-sm rounded-3 overflow-hidden border">
                    <span class="input-group-text bg-light border-0">
                        <i data-lucide="search" style="width:15px"></i>
                    </span>
                    <input type="text" class="form-control border-0 shadow-none"
                        placeholder="Ketik nama periode..."
                        v-model="filter.search" @input="page = 1">
                </div>
            </div>

            <div class="col-lg-2 col-md-4">
                <label class="small fw-bold text-muted mb-1">
                    <i data-lucide="store" style="width:12px" class="me-1"></i>Cabang
                </label>
                <select class="form-select form-select-sm rounded-3 border fw-medium"
                    v-model="filter.cabang_id" @change="load">
                    <option value="">Semua Cabang</option>
                    <option v-for="c in cabangList" :key="c.id" :value="c.id">{{ c.nama }}</option>
                </select>
            </div>

            <div class="col-lg-2 col-md-4">
                <label class="small fw-bold text-muted mb-1">
                    <i data-lucide="activity" style="width:12px" class="me-1"></i>Status
                </label>
                <select class="form-select form-select-sm rounded-3 border fw-medium"
                    v-model="filter.status" @change="load">
                    <option value="">Semua Status</option>
                    <option value="aktif">Aktif</option>
                    <option value="nonaktif">Nonaktif</option>
                </select>
            </div>

            <div class="col-lg-2 d-flex gap-2">
                <button class="btn btn-danger btn-sm w-100 fw-bold rounded-3 shadow-sm
                               d-flex align-items-center justify-content-center gap-1"
                    @click="load" :disabled="loading">
                    <i data-lucide="filter" style="width:14px"></i>
                    <span v-if="!loading">Terapkan</span>
                    <span v-else class="spinner-border spinner-border-sm"></span>
                </button>
                <button class="btn btn-light btn-sm border rounded-3 px-3"
                    @click="resetFilter" title="Reset">
                    <i data-lucide="refresh-cw" style="width:14px"></i>
                </button>
            </div>

        </div>
    </div>

    <!-- LOADING -->
    <div v-if="loading" class="text-center py-5">
        <div class="spinner-border text-danger mb-2" role="status"></div>
        <p class="text-muted small mb-0">Memuat data...</p>
    </div>

    <!-- EMPTY -->
    <div v-else-if="!filteredRows.length"
        class="glass-panel border-0 shadow-sm bg-white p-5 text-center"
        style="border-radius:20px">
        <div class="d-flex flex-column align-items-center" style="opacity:.35">
            <i data-lucide="inbox" style="width:56px;height:56px" class="mb-3"></i>
            <p class="fw-bold mb-1">Tidak ada periode diskon</p>
            <p class="text-muted small mb-0">Klik tombol Tambah Periode untuk membuat baru</p>
        </div>
    </div>

    <!-- ══ TABLE ══════════════════════════════════════════════════════════ -->
    <div v-else class="glass-panel border-0 shadow-sm bg-white overflow-hidden mb-4"
        style="border-radius:20px">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead style="background:#f8fafc">
                    <tr class="text-muted">
                        <th class="ps-4 py-3 border-0 small text-uppercase fw-bolder">Nama Periode</th>
                        <th class="py-3 border-0 small text-uppercase fw-bolder">Cabang</th>
                        <th class="py-3 border-0 small text-uppercase fw-bolder text-center">Periode Berlaku</th>
                        <th class="py-3 border-0 small text-uppercase fw-bolder text-center">Jml Barang</th>
                        <th class="py-3 border-0 small text-uppercase fw-bolder text-center">Status</th>
                        <th class="pe-4 py-3 border-0 small text-uppercase fw-bolder text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="row in paginatedRows" :key="row.id"
                        class="border-bottom border-light row-item">

                        <!-- Nama Periode -->
                        <td class="ps-4 py-3">
                            <div class="fw-bold text-dark">{{ row.nama }}</div>
                            <div class="text-muted" style="font-size:.73rem">
                                <i data-lucide="user" style="width:10px" class="me-1"></i>
                                {{ row.created_by_nama || '-' }}
                            </div>
                        </td>

                        <!-- Cabang -->
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="store-avatar">
                                    <i data-lucide="store" style="width:11px"></i>
                                </div>
                                <span class="fw-semibold text-dark small">{{ row.nama_cabang || '-' }}</span>
                            </div>
                        </td>

                        <!-- Periode -->
                        <td class="text-center">
                            <div class="periode-wrap">
                                <span class="fw-semibold text-dark" style="font-size:.8rem">
                                    {{ formatDate(row.tgl_mulai) }}
                                </span>
                                <span class="text-muted mx-1">–</span>
                                <span class="fw-semibold text-dark" style="font-size:.8rem">
                                    {{ formatDate(row.tgl_selesai) }}
                                </span>
                            </div>
                            <div class="mt-1">
                                <span class="badge rounded-pill px-2 py-1 fw-semibold"
                                    style="font-size:.65rem"
                                    :class="periodeStatusClass(row)">
                                    {{ periodeStatusLabel(row) }}
                                </span>
                            </div>
                        </td>

                        <!-- Jumlah Barang -->
                        <td class="text-center">
                            <span class="badge bg-light text-dark border fw-bolder px-2 py-1">
                                {{ row.jumlah_item }} barang
                            </span>
                        </td>

                        <!-- Status toggle -->
                        <td class="text-center">
                            <div class="form-check form-switch d-flex justify-content-center mb-0">
                                <input class="form-check-input" type="checkbox"
                                    style="cursor:pointer;width:2.2rem;height:1.1rem"
                                    :checked="row.status === 'aktif'"
                                    @change="toggleStatus(row)">
                            </div>
                            <div style="font-size:.68rem;margin-top:3px"
                                :class="row.status === 'aktif' ? 'text-success fw-bold' : 'text-muted'">
                                {{ row.status === 'aktif' ? 'Aktif' : 'Nonaktif' }}
                            </div>
                        </td>

                        <!-- Aksi -->
                        <td class="text-center pe-4">
                            <div class="d-flex gap-1 justify-content-center">
                                <button class="btn btn-sm btn-outline-primary rounded-3 px-2 py-1 fw-semibold"
                                    style="font-size:.75rem"
                                    @click="viewDetail(row)">
                                    <i data-lucide="eye" style="width:13px" class="me-1"></i> Detail
                                </button>
                                <button class="btn btn-sm btn-outline-warning rounded-3 px-2 py-1 fw-semibold"
                                    style="font-size:.75rem"
                                    @click="openModal(row)">
                                    <i data-lucide="pencil" style="width:13px" class="me-1"></i> Edit
                                </button>
                                <button class="btn btn-sm btn-outline-danger rounded-3 px-2 py-1 fw-semibold"
                                    style="font-size:.75rem"
                                    @click="confirmDelete(row)">
                                    <i data-lucide="trash-2" style="width:13px"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-4 py-3 d-flex justify-content-between align-items-center
                    flex-wrap gap-2 border-top">
            <p class="small text-muted mb-0 fw-medium">
                Menampilkan <b>{{ paginatedRows.length }}</b> dari <b>{{ filteredRows.length }}</b> periode
            </p>
            <nav v-if="totalPage > 1">
                <ul class="pagination pagination-sm mb-0 gap-1">
                    <li class="page-item" :class="{ disabled: page === 1 }">
                        <button class="page-link border-0 rounded-3 shadow-sm" @click="page--">
                            <i data-lucide="chevron-left" style="width:14px"></i>
                        </button>
                    </li>
                    <li class="page-item active">
                        <span class="page-link border-0 rounded-3 shadow-sm fw-bold px-3">
                            {{ page }} / {{ totalPage }}
                        </span>
                    </li>
                    <li class="page-item" :class="{ disabled: page >= totalPage }">
                        <button class="page-link border-0 rounded-3 shadow-sm" @click="page++">
                            <i data-lucide="chevron-right" style="width:14px"></i>
                        </button>
                    </li>
                </ul>
            </nav>
        </div>
    </div>

    <!-- ══ MODAL FORM (Tambah / Edit) ════════════════════════════════════ -->
    <div class="modal fade" id="modalForm" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg" style="border-radius:20px">

                <div class="modal-header border-0 pb-0 px-4 pt-4">
                    <h5 class="modal-title fw-bold d-flex align-items-center gap-2">
                        <i :data-lucide="form.id ? 'pencil' : 'plus-circle'" class="text-danger"
                            style="width:20px"></i>
                        {{ form.id ? 'Edit Periode Diskon' : 'Tambah Periode Diskon' }}
                    </h5>
                    <button type="button" class="btn-close" @click="closeModal"></button>
                </div>

                <div class="modal-body px-4 py-3">

                    <!-- Alert error -->
                    <div v-if="formError" class="alert alert-danger d-flex align-items-center gap-2
                                                  rounded-3 border-0 py-2 mb-3" style="font-size:.85rem">
                        <i data-lucide="alert-circle" style="width:16px"></i>
                        {{ formError }}
                    </div>

                    <div class="row g-3 mb-3">
                        <!-- Nama Periode -->
                        <div class="col-12">
                            <label class="form-label fw-semibold small text-muted mb-1">NAMA PERIODE</label>
                            <input type="text" class="form-control rounded-3"
                                placeholder="Contoh: Promo Akhir Bulan Maret 2026"
                                v-model="form.nama">
                        </div>
                        <!-- Cabang -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-muted mb-1">CABANG</label>
                            <select class="form-select rounded-3" v-model="form.cabang_id">
                                <option value="">-- Pilih Cabang --</option>
                                <option v-for="c in cabangList" :key="c.id" :value="c.id">{{ c.nama }}</option>
                            </select>
                        </div>
                        <!-- Status (edit only) -->
                        <div class="col-md-6" v-if="form.id">
                            <label class="form-label fw-semibold small text-muted mb-1">STATUS</label>
                            <select class="form-select rounded-3" v-model="form.status">
                                <option value="aktif">Aktif</option>
                                <option value="nonaktif">Nonaktif</option>
                            </select>
                        </div>
                        <!-- Tanggal -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-muted mb-1">TANGGAL MULAI</label>
                            <input type="date" class="form-control rounded-3" v-model="form.tgl_mulai">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-muted mb-1">TANGGAL SELESAI</label>
                            <input type="date" class="form-control rounded-3" v-model="form.tgl_selesai">
                        </div>
                    </div>

                    <!-- ── Daftar Barang ── -->
                    <div class="mb-2 d-flex align-items-center justify-content-between">
                        <label class="fw-bold small text-muted text-uppercase mb-0">
                            <i data-lucide="package" style="width:12px" class="me-1"></i>
                            Barang yang Didiskon
                        </label>
                        <button class="btn btn-sm btn-outline-danger rounded-3 px-3 fw-semibold"
                            @click="addItemRow">
                            <i data-lucide="plus" style="width:13px" class="me-1"></i> Tambah Barang
                        </button>
                    </div>

                    <!-- Header kolom item -->
                    <div v-if="form.items.length" class="row g-0 mb-1 px-1">
                        <div class="col-6">
                            <span class="small fw-bold text-muted text-uppercase"
                                style="font-size:.68rem;letter-spacing:.4px">Barang</span>
                        </div>
                        <div class="col-3 text-center">
                            <span class="small fw-bold text-muted text-uppercase"
                                style="font-size:.68rem;letter-spacing:.4px">Harga Jual</span>
                        </div>
                        <div class="col-2 text-center">
                            <span class="small fw-bold text-muted text-uppercase"
                                style="font-size:.68rem;letter-spacing:.4px">Diskon/Satuan</span>
                        </div>
                        <div class="col-1"></div>
                    </div>

                    <div v-if="!form.items.length"
                        class="text-center py-4 text-muted small border rounded-3"
                        style="background:#fafafa">
                        <i data-lucide="package-open" style="width:28px;height:28px;opacity:.3" class="d-block mx-auto mb-2"></i>
                        Belum ada barang. Klik "Tambah Barang" di atas.
                    </div>

                    <div v-for="(item, idx) in form.items" :key="idx"
                        class="row g-2 align-items-center mb-2">
                        <!-- Pilih Barang -->
                        <div class="col-6">
                            <select class="form-select form-select-sm rounded-3"
                                v-model="item.barang_id"
                                @change="onBarangChange(item)">
                                <option value="">-- Pilih Barang --</option>
                                <option v-for="b in availableBarang(idx)"
                                    :key="b.id" :value="b.id">
                                    {{ b.nama }}
                                </option>
                            </select>
                        </div>
                        <!-- Harga Jual (readonly) -->
                        <div class="col-3">
                            <div class="form-control form-control-sm rounded-3 text-muted text-center bg-light"
                                style="font-size:.8rem;border:1px solid #dee2e6">
                                {{ item.barang_id ? 'Rp ' + formatNum(getHargaJual(item.barang_id)) : '—' }}
                            </div>
                        </div>
                        <!-- Nominal Diskon -->
                        <div class="col-2">
                            <input type="number" min="0" step="500"
                                class="form-control form-control-sm rounded-3 text-center fw-bold"
                                :class="item.nominal_diskon > getHargaJual(item.barang_id) && item.barang_id
                                         ? 'border-danger' : ''"
                                placeholder="0"
                                v-model.number="item.nominal_diskon">
                        </div>
                        <!-- Hapus baris -->
                        <div class="col-1 text-center">
                            <button class="btn btn-sm btn-link text-danger p-0"
                                @click="removeItemRow(idx)">
                                <i data-lucide="x-circle" style="width:18px"></i>
                            </button>
                        </div>

                        <!-- Preview harga setelah diskon -->
                        <div v-if="item.barang_id && item.nominal_diskon > 0" class="col-12">
                            <small class="text-success ms-1">
                                <i data-lucide="tag" style="width:11px" class="me-1"></i>
                                Harga setelah diskon:
                                <b>Rp {{ formatNum(getHargaJual(item.barang_id) - item.nominal_diskon) }}</b>
                            </small>
                        </div>
                    </div>

                </div><!-- /modal-body -->

                <div class="modal-footer border-0 px-4 pb-4 pt-2 gap-2">
                    <button class="btn btn-light border rounded-3 px-4 fw-semibold"
                        @click="closeModal">Batal</button>
                    <button class="btn btn-danger rounded-3 px-5 fw-bold shadow-sm
                                   d-flex align-items-center gap-2"
                        @click="submitForm" :disabled="saving">
                        <span v-if="!saving">
                            <i data-lucide="save" style="width:15px" class="me-1"></i>
                            {{ form.id ? 'Perbarui' : 'Simpan' }}
                        </span>
                        <span v-else class="d-flex align-items-center gap-2">
                            <span class="spinner-border spinner-border-sm"></span> Menyimpan...
                        </span>
                    </button>
                </div>

            </div>
        </div>
    </div>

    <!-- ══ MODAL DETAIL ═══════════════════════════════════════════════════ -->
    <div class="modal fade" id="modalDetail" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg" style="border-radius:20px">

                <div class="modal-header border-0 px-4 pt-4 pb-0">
                    <h5 class="modal-title fw-bold d-flex align-items-center gap-2">
                        <i data-lucide="tag" class="text-danger" style="width:20px"></i>
                        Detail Periode Diskon
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body px-4 py-3" v-if="detail">

                    <!-- Info cards -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="detail-card">
                                <div class="detail-card-label">
                                    <i data-lucide="tag" style="width:12px"></i> Nama Periode
                                </div>
                                <div class="detail-card-value">{{ detail.header.nama }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-card">
                                <div class="detail-card-label">
                                    <i data-lucide="store" style="width:12px"></i> Cabang
                                </div>
                                <div class="detail-card-value">{{ detail.header.nama_cabang }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-card">
                                <div class="detail-card-label">
                                    <i data-lucide="calendar" style="width:12px"></i> Periode Berlaku
                                </div>
                                <div class="detail-card-value" style="font-size:.85rem">
                                    {{ formatDate(detail.header.tgl_mulai) }}
                                    <span class="text-muted mx-1">–</span>
                                    {{ formatDate(detail.header.tgl_selesai) }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-card">
                                <div class="detail-card-label">
                                    <i data-lucide="activity" style="width:12px"></i> Status
                                </div>
                                <div class="mt-1">
                                    <span class="badge px-3 py-1 fw-bold rounded-pill"
                                        :class="detail.header.status === 'aktif'
                                            ? 'bg-success bg-opacity-10 text-success border border-success border-opacity-25'
                                            : 'bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25'">
                                        {{ detail.header.status === 'aktif' ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabel item -->
                    <div class="fw-bold small text-muted text-uppercase mb-2"
                        style="letter-spacing:.5px">
                        <i data-lucide="package" style="width:13px" class="me-1"></i>
                        {{ detail.items.length }} Barang Diskon
                    </div>
                    <div class="table-responsive border rounded-3">
                        <table class="table table-sm align-middle mb-0">
                            <thead style="background:#f8fafc">
                                <tr class="text-muted small text-uppercase">
                                    <th class="ps-3 py-2 border-0 fw-bolder">#</th>
                                    <th class="py-2 border-0 fw-bolder">Nama Barang</th>
                                    <th class="py-2 border-0 fw-bolder text-end">Harga Normal</th>
                                    <th class="py-2 border-0 fw-bolder text-center">Diskon/Satuan</th>
                                    <th class="py-2 border-0 fw-bolder text-end pe-3">Harga Diskon</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(item, idx) in detail.items" :key="item.id"
                                    class="border-bottom border-light">
                                    <td class="ps-3 text-muted small">{{ idx + 1 }}</td>
                                    <td class="fw-semibold py-2">{{ item.nama_barang }}</td>
                                    <td class="text-end text-muted" style="font-size:.82rem">
                                        Rp {{ formatNum(item.harga_jual) }}
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-danger bg-opacity-10 text-danger
                                                     border border-danger border-opacity-25 fw-bold px-2">
                                            - Rp {{ formatNum(item.nominal_diskon) }}
                                        </span>
                                    </td>
                                    <td class="text-end pe-3 fw-bold text-success">
                                        Rp {{ formatNum(item.harga_diskon) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                </div>

                <div class="modal-footer border-0 px-4 pb-4 pt-2">
                    <button class="btn btn-light border rounded-3 px-4 fw-semibold"
                        data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- ══ MODAL KONFIRMASI HAPUS ═════════════════════════════════════════ -->
    <div class="modal fade" id="modalDelete" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius:18px">
                <div class="modal-body text-center p-4">
                    <div class="bg-danger bg-opacity-10 rounded-circle d-inline-flex p-3 mb-3">
                        <i data-lucide="trash-2" class="text-danger" style="width:32px;height:32px"></i>
                    </div>
                    <h5 class="fw-bold mb-1">Hapus Periode Diskon?</h5>
                    <p class="text-muted small mb-3" v-if="deleteTarget">
                        "<b>{{ deleteTarget.nama }}</b>" dan semua item di dalamnya akan dihapus permanen.
                    </p>
                    <div class="d-flex gap-2 justify-content-center">
                        <button class="btn btn-light border rounded-3 px-4 fw-semibold"
                            data-bs-dismiss="modal">Batal</button>
                        <button class="btn btn-danger rounded-3 px-4 fw-bold"
                            @click="doDelete" :disabled="saving">
                            <span v-if="!saving">Hapus</span>
                            <span v-else class="spinner-border spinner-border-sm"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<style>
    [v-cloak] {
        display: none;
    }

    .row-item {
        transition: background .12s;
    }

    .row-item:hover {
        background: #fff5f5 !important;
    }

    .store-avatar {
        width: 22px;
        height: 22px;
        border-radius: 6px;
        background: #ecfdf5;
        color: #059669;
        display: grid;
        place-items: center;
        flex-shrink: 0;
    }

    .page-link {
        color: #dc3545;
    }

    .page-item.active .page-link {
        background-color: #dc3545;
        border-color: #dc3545;
        color: #fff;
    }

    .detail-card {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: .75rem 1rem;
    }

    .detail-card-label {
        font-size: .68rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .5px;
        color: #94a3b8;
        margin-bottom: .3rem;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .detail-card-value {
        font-weight: 700;
        color: #1e293b;
        font-size: .9rem;
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
                saving: false,
                allRows: [],
                detail: null,
                deleteTarget: null,

                cabangList: <?= json_encode($cabang_list) ?>,
                barangList: <?= json_encode($barang_list) ?>,

                filter: {
                    search: '',
                    cabang_id: '',
                    status: ''
                },
                page: 1,
                perPage: 10,

                // ── Form state ─────────────────────────────────
                form: {
                    id: null,
                    nama: '',
                    cabang_id: '',
                    tgl_mulai: '',
                    tgl_selesai: '',
                    status: 'aktif',
                    items: [],
                },
                formError: '',

                _modalForm: null,
                _modalDetail: null,
                _modalDelete: null,
            };
        },

        computed: {
            filteredRows() {
                let rows = this.allRows;
                const s = this.filter.search.toLowerCase();
                if (s) rows = rows.filter(r => (r.nama || '').toLowerCase().includes(s));
                return rows;
            },
            totalPage() {
                return Math.ceil(this.filteredRows.length / this.perPage);
            },
            paginatedRows() {
                const s = (this.page - 1) * this.perPage;
                return this.filteredRows.slice(s, s + this.perPage);
            },
        },

        methods: {
            // ── Load list ──────────────────────────────────────
            async load() {
                this.loading = true;
                try {
                    const res = await axios.get('<?= base_url('diskon-terbatas/list') ?>', {
                        params: this.filter,
                    });
                    this.allRows = res.data.data;
                    this.page = 1;
                } catch (e) {
                    console.error(e);
                } finally {
                    this.loading = false;
                    this.$nextTick(() => lucide.createIcons());
                }
            },

            resetFilter() {
                this.filter = {
                    search: '',
                    cabang_id: '',
                    status: ''
                };
                this.load();
            },

            // ── Modal Form ────────────────────────────────────
            openModal(row = null) {
                this.formError = '';
                if (row) {
                    this.form = {
                        id: row.id,
                        nama: row.nama,
                        cabang_id: row.cabang_id,
                        tgl_mulai: row.tgl_mulai,
                        tgl_selesai: row.tgl_selesai,
                        status: row.status,
                        items: [],
                    };
                    // load items dari detail endpoint
                    this.loadFormItems(row.id);
                } else {
                    this.form = {
                        id: null,
                        nama: '',
                        cabang_id: '',
                        tgl_mulai: '',
                        tgl_selesai: '',
                        status: 'aktif',
                        items: [],
                    };
                }
                this._modalForm.show();
                this.$nextTick(() => lucide.createIcons());
            },

            async loadFormItems(id) {
                const res = await axios.get(`<?= base_url('diskon-terbatas/detail') ?>/${id}`);
                this.form.items = res.data.items.map(i => ({
                    barang_id: i.barang_id,
                    nominal_diskon: i.nominal_diskon,
                }));
                this.$nextTick(() => lucide.createIcons());
            },

            closeModal() {
                this._modalForm.hide();
            },

            addItemRow() {
                this.form.items.push({
                    barang_id: '',
                    nominal_diskon: 0
                });
                this.$nextTick(() => lucide.createIcons());
            },

            removeItemRow(idx) {
                this.form.items.splice(idx, 1);
            },

            onBarangChange(item) {
                item.nominal_diskon = 0;
            },

            // Barang yang belum dipilih di baris lain
            availableBarang(currentIdx) {
                const chosen = this.form.items
                    .filter((_, i) => i !== currentIdx)
                    .map(i => i.barang_id)
                    .filter(Boolean);
                return this.barangList.filter(b => !chosen.includes(b.id));
            },

            getHargaJual(barangId) {
                const b = this.barangList.find(x => x.id == barangId);
                return b ? parseFloat(b.harga_jual) : 0;
            },

            // ── Submit form ───────────────────────────────────
            async submitForm() {
                this.formError = '';
                if (!this.form.nama.trim()) {
                    this.formError = 'Nama periode wajib diisi.';
                    return;
                }
                if (!this.form.cabang_id) {
                    this.formError = 'Pilih cabang terlebih dahulu.';
                    return;
                }
                if (!this.form.tgl_mulai || !this.form.tgl_selesai) {
                    this.formError = 'Tanggal mulai dan selesai wajib diisi.';
                    return;
                }
                if (!this.form.items.length || this.form.items.some(i => !i.barang_id)) {
                    this.formError = 'Tambahkan minimal satu barang dan pastikan semua barang sudah dipilih.';
                    return;
                }

                this.saving = true;
                try {
                    const url = this.form.id ?
                        `<?= base_url('diskon-terbatas/update') ?>/${this.form.id}` :
                        `<?= base_url('diskon-terbatas/store') ?>`;

                    const res = await axios.post(url, this.form);

                    if (!res.data.success) {
                        this.formError = res.data.message;
                        return;
                    }
                    this._modalForm.hide();
                    this.load();
                } catch (e) {
                    this.formError = 'Terjadi kesalahan server.';
                } finally {
                    this.saving = false;
                }
            },

            // ── Toggle status ─────────────────────────────────
            async toggleStatus(row) {
                await axios.post(`<?= base_url('diskon-terbatas/toggle') ?>/${row.id}`);
                await this.load();
            },

            // ── View Detail ───────────────────────────────────
            async viewDetail(row) {
                const res = await axios.get(`<?= base_url('diskon-terbatas/detail') ?>/${row.id}`);
                this.detail = res.data;
                this._modalDetail.show();
                this.$nextTick(() => lucide.createIcons());
            },

            // ── Delete ────────────────────────────────────────
            confirmDelete(row) {
                this.deleteTarget = row;
                this._modalDelete.show();
            },

            async doDelete() {
                if (!this.deleteTarget) return;
                this.saving = true;
                try {
                    await axios.post(`<?= base_url('diskon-terbatas/delete') ?>/${this.deleteTarget.id}`);
                    this._modalDelete.hide();
                    this.deleteTarget = null;
                    this.load();
                } catch (e) {
                    console.error(e);
                } finally {
                    this.saving = false;
                }
            },

            // ── Helpers ───────────────────────────────────────
            formatDate(d) {
                if (!d) return '-';
                return new Date(d).toLocaleDateString('id-ID', {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric',
                });
            },

            formatNum(n) {
                return new Intl.NumberFormat('id-ID').format(n ?? 0);
            },

            periodeStatusLabel(row) {
                const today = new Date().toISOString().slice(0, 10);
                if (today < row.tgl_mulai) return 'Akan Tiba Pada ' + this.formatDate(row.tgl_mulai);
                if (today > row.tgl_selesai) return 'Berakhir';
                return 'Berlangsung';
            },

            periodeStatusClass(row) {
                const today = new Date().toISOString().slice(0, 10);
                if (today < row.tgl_mulai)
                    return 'bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25';
                if (today > row.tgl_selesai)
                    return 'bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25';
                return 'bg-success bg-opacity-10 text-success border border-success border-opacity-25';
            },
        },

        mounted() {
            this._modalForm = new bootstrap.Modal(document.getElementById('modalForm'));
            this._modalDetail = new bootstrap.Modal(document.getElementById('modalDetail'));
            this._modalDelete = new bootstrap.Modal(document.getElementById('modalDelete'));
            this.load();
            lucide.createIcons();
        },

        updated() {
            lucide.createIcons();
        },
    }).mount('#app');
</script>
<?= $this->endSection() ?>