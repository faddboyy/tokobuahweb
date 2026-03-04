<?php

/**
 * @var \CodeIgniter\Router\RouteCollection $routes
 */

$routes->group('', ['filter' => 'auth'], function ($routes) {
    $routes->get('dashboard/getData', 'Dashboard::getData');

    $routes->group('user', ['filter' => 'role:admin,owner'], function ($routes) {
        $routes->get('list', 'User::list');
        $routes->post('store', 'User::store');
        $routes->post('update/(:num)', 'User::update/$1');
        $routes->delete('delete/(:num)', 'User::delete/$1');
        $routes->post('toggle/(:num)', 'User::toggle/$1');
    });

    $routes->group('gudangutama', ['filter' => 'role:admin,owner'], function ($routes) {
        $routes->get('/', 'GudangUtama::index');
        $routes->get('list', 'GudangUtama::list');
        $routes->get('mandor-list', 'GudangUtama::mandorList');
        $routes->post('store', 'GudangUtama::store');
        $routes->post('update/(:num)', 'GudangUtama::update/$1');
        $routes->delete('delete/(:num)', 'GudangUtama::delete/$1');
    });

    $routes->group('cabang', ['filter' => 'role:owner,admin'], function ($routes) {
        $routes->get('list', 'Cabang::list');
        $routes->get('petugas-list', 'Cabang::petugasList');
        $routes->post('store', 'Cabang::store');
        $routes->post('update/(:num)', 'Cabang::update/$1');
        $routes->delete('delete/(:num)', 'Cabang::delete/$1');
    });

    $routes->group('barang', function ($routes) {
        $routes->get('diskon-aktif', 'Barang::diskonAktif');
        $routes->get('list', 'Barang::list');
        $routes->post('store', 'Barang::store');
        $routes->post('update/(:num)', 'Barang::update/$1');
        $routes->delete('delete/(:num)', 'Barang::delete/$1');
        $routes->post('printBatch', 'Barang::printBatch');

        $routes->group('jenis', function ($routes) {
            $routes->get('list', 'Jenis::list');
            $routes->post('store', 'Jenis::store');
            $routes->delete('delete/(:num)', 'Jenis::delete/$1');
        });

        $routes->group('satuan', function ($routes) {
            $routes->get('list', 'Satuan::list');
            $routes->post('store', 'Satuan::store');
            $routes->delete('delete/(:num)', 'Satuan::delete/$1');
        });
    });

    $routes->group('aset-toko', function ($routes) {
        $routes->get('list/(:num)', 'AsetToko::list/$1');
        $routes->get('get-available-barang/(:num)', 'AsetToko::getAvailableBarang/$1');
        $routes->post('import', 'AsetToko::import');
        $routes->post('remove', 'AsetToko::remove');
        $routes->get('print-pdf/(:num)/(:alpha)', 'AsetToko::printPdf/$1/$2');
    });

    // Diskon Terbatas — owner only (guard di controller)
    $routes->get('diskon-terbatas',              'DiskonTerbatas::index');
    $routes->get('diskon-terbatas/list',         'DiskonTerbatas::list');
    $routes->get('diskon-terbatas/detail/(:num)', 'DiskonTerbatas::detail/$1');
    $routes->post('diskon-terbatas/store',        'DiskonTerbatas::store');
    $routes->post('diskon-terbatas/update/(:num)', 'DiskonTerbatas::update/$1');
    $routes->post('diskon-terbatas/delete/(:num)', 'DiskonTerbatas::delete/$1');
    $routes->post('diskon-terbatas/toggle/(:num)', 'DiskonTerbatas::toggle/$1');

    $routes->get('aset-gudang/data', 'AssetGudang::data', ['filter' => 'role:owner,admin']);

    $routes->get('laporan/penerimaan-gudang',              'LaporanPenerimaanGudang::index');
    $routes->get('laporan/penerimaan-gudang/list',         'LaporanPenerimaanGudang::list');
    $routes->get('laporan/penerimaan-gudang/detail/(:num)', 'LaporanPenerimaanGudang::detail/$1');
    $routes->get('laporan/penerimaan-gudang/get-detail/(:num)', 'LaporanPenerimaanGudang::getDetail/$1');

    $routes->group('pengiriman-gudang', ['filter' => 'role:admin'], function ($routes) {
        $routes->get('/',                   'PengirimanGudang::index');
        $routes->get('search-barang',       'PengirimanGudang::searchBarang');
        $routes->get('get-cabang',          'PengirimanGudang::getCabang');
        $routes->post('simpan',             'PengirimanGudang::simpan');
        $routes->post('batalkan/(:num)',    'PengirimanGudang::batalkan/$1');
        $routes->get('cetak/(:num)',          'PengirimanGudang::cetakSurat/$1');
        // Force download
        $routes->get('cetak-download/(:num)', 'PengirimanGudang::cetakDownload/$1');
    });

    $routes->get('laporan/pengiriman-gudang',             'LaporanPengirimanGudang::index');
    $routes->get('laporan/pengiriman-gudang/list',        'LaporanPengirimanGudang::list');
    $routes->get('laporan/pengiriman-gudang/detail/(:num)', 'LaporanPengirimanGudang::detail/$1');
    $routes->get('laporan/pengiriman-gudang/get-detail/(:num)', 'LaporanPengirimanGudang::getDetail/$1');


    $routes->group('suplier', ['filter' => 'role:admin,owner'], function ($routes) {
        $routes->get('list', 'Suplier::list');
        $routes->post('store', 'Suplier::store');
        $routes->post('update/(:num)', 'Suplier::update/$1');
        $routes->delete('delete/(:num)', 'Suplier::delete/$1');
    });

    $routes->group('customer', function ($routes) {
        $routes->get('list', 'Customer::list');
        $routes->post('store', 'Customer::store');
        $routes->post('update/(:num)', 'Customer::update/$1');
        $routes->delete('delete/(:num)', 'Customer::delete/$1');
    });

    $routes->group('suratjalan', ['filter' => 'role:admin'], function ($routes) {
        $routes->post('mulai', 'SuratJalan::mulaiTransaksi');
        $routes->post('add-item', 'SuratJalan::addItem');
        $routes->get('detail', 'SuratJalan::detail');
        $routes->post('delete-item/(:num)', 'SuratJalan::deleteItem/$1');
        $routes->get('satuan-list', 'SuratJalan::satuanList');
        $routes->get('search-barang', 'SuratJalan::searchBarang');
        $routes->post('finalisasi', 'SuratJalan::finalisasi');
        $routes->get('cetak/(:num)', 'SuratJalan::cetak/$1');
        $routes->post('batalkan/(:num)', 'SuratJalan::batalkan/$1');
    });

    $routes->get('barang-masuk',        'BarangMasuk::index');
    $routes->get('barang-masuk/scan',   'BarangMasuk::scan');
    $routes->post('barang-masuk/simpan', 'BarangMasuk::simpan');

    $routes->group('penerimaan-gudang', ['filter' => 'role:admin'], function ($routes) {
        $routes->get('scan-po', 'PenerimaanGudang::scanPO');
        $routes->post('simpan', 'PenerimaanGudang::simpan');
        $routes->get('detail/(:num)',      'PenerimaanGudang::detail/$1');
        $routes->post('update-item/(:num)', 'PenerimaanGudang::updateItem/$1');
        $routes->post('batalkan/(:num)',    'PenerimaanGudang::batalkan/$1');
        $routes->get('session-scan',   'PenerimaanGudang::sessionScan');
        $routes->post('clear-session', 'PenerimaanGudang::clearSession');
    });

    $routes->group('penjualan', ['filter' => 'role:petugas'], function ($routes) {

        $routes->post('mulai', 'Penjualan::mulaiTransaksi');
        $routes->post('add-item', 'Penjualan::addItem');

        $routes->get('search', 'Penjualan::searchBarang');
        $routes->get('scan', 'Penjualan::scanBarcode');

        $routes->get('detail', 'Penjualan::detail');
        $routes->post('update-qty/(:num)', 'Penjualan::updateQty/$1');

        $routes->post('delete-item/(:num)', 'Penjualan::deleteItem/$1');

        $routes->get('customers', 'Penjualan::getCustomers');
        $routes->post('finalisasi', 'Penjualan::finalisasi');

        $routes->get('cetak/(:num)', 'Penjualan::cetak/$1');
    });

    $routes->group('laporan', function ($routes) {
        $routes->get('list_penjualan', 'Laporan::list_penjualan');
        $routes->get('get_detail/(:num)', 'Laporan::get_detail/$1');
        $routes->get('list-surat-jalan', 'Laporan::list_surat_jalan');
        $routes->get('get-surat-jalan-detail/(:num)', 'Laporan::get_surat_jalan_detail/$1');
        $routes->get('list-barang-masuk', 'Laporan::list_barang_masuk');
        $routes->get('barang-masuk/(:num)', 'Laporan::barang_masuk_detail/$1');
        $routes->get('get-barang-masuk-detail/(:num)', 'Laporan::get_barang_masuk_detail/$1');
    });

    $routes->get('laporan/barang-masuk',              'LaporanBarangMasuk::index');
    $routes->get('laporan/barang-masuk/list',         'LaporanBarangMasuk::list');
    $routes->get('laporan/barang-masuk/detail/(:num)', 'LaporanBarangMasuk::detail/$1');
    $routes->get('laporan/barang-masuk/get-detail/(:num)', 'LaporanBarangMasuk::getDetail/$1');
});
