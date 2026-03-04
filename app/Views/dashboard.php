<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div id="app" v-cloak class="d-flex flex-column h-100 gap-4">

    <!-- ══ HEADER ══════════════════════════════════════════════════════════ -->
    <div class="glass-panel p-4 d-flex justify-content-between align-items-center shadow-sm border-0"
        style="border-radius: 24px;">
        <div>
            <h1 class="h5 fw-bold mb-1">
                <?= session()->get('role') == 'petugas' ? 'Halo, ' . session()->get('nama') : 'Ringkasan Bisnis' ?>
            </h1>
            <p class="small text-muted mb-0">
                <?= session()->get('role') == 'petugas'
                    ? 'Selamat bekerja! Silakan gunakan menu di bawah untuk bertransaksi.'
                    : 'Performa riil penjualan & profitabilitas.' ?>
            </p>
        </div>

        <?php if (session()->get('role') != 'petugas'): ?>
            <div class="d-flex gap-2 bg-white bg-opacity-50 p-2 rounded-4 border border-white shadow-sm">
                <select v-model="filter.month" @change="updateData"
                    class="form-select form-select-sm border-0 bg-transparent fw-bold shadow-none">
                    <option v-for="(m, idx) in months" :value="idx + 1">{{ m }}</option>
                </select>
                <select v-model="filter.year" @change="updateData"
                    class="form-select form-select-sm border-0 bg-transparent fw-bold shadow-none border-start rounded-0">
                    <option v-for="y in [2024, 2025, 2026]" :value="y">{{ y }}</option>
                </select>
            </div>
        <?php endif; ?>
    </div>

    <?php if (session()->get('role') != 'petugas'): ?>

        <!-- ══ STAT CARDS ════════════════════════════════════════════════════ -->
        <div class="row g-4">
            <div v-for="stat in stats" :key="stat.label" class="col-md-6 col-12">
                <div class="glass-panel p-4 h-100 position-relative overflow-hidden shadow-sm border-0"
                    style="border-radius: 24px;">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="text-muted text-uppercase fw-bold"
                                style="font-size:11px; letter-spacing:1px;">
                                {{ stat.label }}
                            </div>
                            <div class="h3 fw-bolder mt-2 mb-0 text-dark">{{ stat.value }}</div>
                        </div>
                        <div :class="`bg-${stat.color} bg-opacity-10 rounded-4 p-3 text-${stat.color} shadow-sm`">
                            <i :data-lucide="stat.icon" style="width:24px; height:24px;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ══ CHART ══════════════════════════════════════════════════════════ -->
        <div class="glass-panel p-4 flex-grow-1 d-flex flex-column shadow-sm border-0"
            style="border-radius: 28px;">

            <!-- Chart header -->
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <h3 class="h6 fw-bold mb-0 d-flex align-items-center gap-2">
                    <i data-lucide="bar-chart-2" class="text-primary" style="width:18px;"></i>
                    Tren Pendapatan &amp; Pengeluaran Harian
                </h3>
                <div class="d-flex align-items-center gap-3 flex-wrap">
                    <!-- Legend -->
                    <div class="d-flex align-items-center gap-3">
                        <span class="d-flex align-items-center gap-1 small fw-semibold text-primary">
                            <span style="width:12px;height:12px;border-radius:3px;
                                         background:rgba(0,103,192,.8);display:inline-block"></span>
                            Pendapatan
                        </span>
                        <span class="d-flex align-items-center gap-1 small fw-semibold text-danger">
                            <span style="width:12px;height:12px;border-radius:3px;
                                         background:rgba(220,38,38,.75);display:inline-block"></span>
                            Pengeluaran
                        </span>
                    </div>
                    <span class="badge bg-light text-primary px-3 py-2 rounded-pill fw-bold">
                        {{ months[filter.month - 1] }} {{ filter.year }}
                    </span>
                </div>
            </div>

            <!-- Canvas -->
            <div class="flex-grow-1 position-relative" style="min-height: 300px;">
                <div v-if="loading"
                    class="position-absolute top-0 start-0 w-100 h-100 bg-white bg-opacity-50
                           d-flex align-items-center justify-content-center"
                    style="z-index:10;">
                    <div class="spinner-border text-primary border-3"></div>
                </div>
                <canvas id="mainChart"></canvas>
            </div>
        </div>

    <?php else: ?>

        <!-- ══ PETUGAS: shortcut menu ═════════════════════════════════════════ -->
        <div class="row g-4">
            <div class="col-md-6">
                <a href="<?= base_url('penjualan') ?>" class="text-decoration-none">
                    <div class="glass-panel p-5 text-center shadow-sm hover-up border-0 h-100
                                d-flex flex-column align-items-center justify-content-center"
                        style="border-radius: 28px;">
                        <div class="bg-primary bg-opacity-10 rounded-circle p-4 d-inline-block mb-3 text-primary shadow-sm">
                            <i data-lucide="shopping-cart" style="width: 40px; height: 40px;"></i>
                        </div>
                        <h5 class="fw-bolder text-dark mb-1">Transaksi Baru</h5>
                        <p class="text-muted mb-0">Input penjualan produk hari ini</p>
                    </div>
                </a>
            </div>
            <div class="col-md-6">
                <div class="glass-panel p-5 text-center shadow-sm border-0 h-100
                            d-flex flex-column align-items-center justify-content-center"
                    style="border-radius: 28px;">
                    <div class="bg-info bg-opacity-10 rounded-circle p-4 d-inline-block mb-3 text-info shadow-sm">
                        <i data-lucide="clock" style="width: 40px; height: 40px;"></i>
                    </div>
                    <h5 class="fw-bolder text-dark mb-1"><?= date('H:i') ?> WIB</h5>
                    <p class="text-muted mb-0">Waktu Server Saat Ini</p>
                </div>
            </div>
        </div>

    <?php endif; ?>

</div>

<style>
    .hover-up {
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .hover-up:hover {
        transform: translateY(-8px);
        background: #ffffff !important;
        box-shadow: 0 15px 30px rgba(0, 0, 0, .08) !important;
    }

    [v-cloak] {
        display: none;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    const {
        createApp,
        ref,
        onMounted,
        nextTick
    } = Vue;

    createApp({
        setup() {
            const loading = ref(false);
            const role = "<?= session()->get('role') ?>";
            const filter = ref({
                month: <?= date('n') ?>,
                year: <?= date('Y') ?>
            });
            const months = [
                "Januari", "Februari", "Maret", "April", "Mei", "Juni",
                "Juli", "Agustus", "September", "Oktober", "November", "Desember"
            ];
            const stats = ref([]);
            let chartInstance = null;

            // ── render grouped bar chart ────────────────────────────────
            const renderChart = (chartData) => {
                const el = document.getElementById('mainChart');
                if (!el) return;
                const ctx = el.getContext('2d');
                if (chartInstance) chartInstance.destroy();

                chartInstance = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: chartData.labels,
                        datasets: [{
                                label: 'Pendapatan',
                                data: chartData.datasets,
                                backgroundColor: chartData.datasets.map(v =>
                                    v > 0 ? 'rgba(0,103,192,0.75)' : 'rgba(200,200,200,0.3)'
                                ),
                                hoverBackgroundColor: chartData.datasets.map(v =>
                                    v > 0 ? 'rgba(0,103,192,1)' : 'rgba(180,180,180,0.55)'
                                ),
                                borderRadius: 5,
                                borderSkipped: false,
                                borderWidth: 0,
                            },
                            {
                                label: 'Pengeluaran',
                                data: chartData.pengeluaran,
                                backgroundColor: chartData.pengeluaran.map(v =>
                                    v > 0 ? 'rgba(220,38,38,0.75)' : 'rgba(200,200,200,0.3)'
                                ),
                                hoverBackgroundColor: chartData.pengeluaran.map(v =>
                                    v > 0 ? 'rgba(220,38,38,1)' : 'rgba(180,180,180,0.55)'
                                ),
                                borderRadius: 5,
                                borderSkipped: false,
                                borderWidth: 0,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            mode: 'index',
                            intersect: false,
                        },
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: (ctx) =>
                                        ` ${ctx.dataset.label}: Rp ${ctx.parsed.y.toLocaleString('id-ID')}`
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: (v) => 'Rp ' + v.toLocaleString('id-ID'),
                                    font: {
                                        weight: '600'
                                    }
                                },
                                grid: {
                                    color: '#f0f0f0'
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            };

            // ── fetch data ──────────────────────────────────────────────
            const updateData = async () => {
                if (role === 'petugas') return;
                loading.value = true;
                try {
                    const res = await axios.get(`<?= base_url('dashboard/getData') ?>`, {
                        params: {
                            month: filter.value.month,
                            year: filter.value.year
                        }
                    });
                    stats.value = res.data.stats;
                    renderChart(res.data.chart);
                } catch (e) {
                    console.error('Gagal memuat data', e);
                } finally {
                    loading.value = false;
                    nextTick(() => lucide.createIcons());
                }
            };

            onMounted(() => {
                updateData();
                lucide.createIcons();
            });

            return {
                filter,
                months,
                stats,
                updateData,
                loading
            };
        }
    }).mount('#app');
</script>
<?= $this->endSection() ?>