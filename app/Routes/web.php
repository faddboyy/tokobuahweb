<?php

/**
 * @var \CodeIgniter\Router\RouteCollection $routes
 */

$routes->get('/dashboard', 'Dashboard::index', ['filter' => 'auth']);

$routes->get('/', 'Auth::login', ['filter' => 'guest']);
$routes->post('login', 'Auth::process', ['filter' => 'guest']);
$routes->get('logout', 'Auth::logout', ['filter' => 'auth']);

$routes->group('', ['filter' => 'auth'], function ($routes) {
    $routes->get('dashboard', 'Dashboard::index');
    $routes->get('user', 'User::index', ['filter' => 'role:owner']);
    $routes->get('cabang', 'Cabang::index', ['filter' => 'role:owner,admin']);
    $routes->get('gudangutama', 'GudangUtama::index', ['filter' => 'role:owner,admin']);
    $routes->get('barang', 'Barang::index');
    $routes->get('aset-toko', 'AsetToko::index');
    $routes->get('aset-gudang', 'AssetGudang::index', ['filter' => 'role:owner,admin']);
    $routes->get('surat-jalan', 'SuratJalan::index', ['filter' => 'role:admin']);
    $routes->get('penerimaan-gudang', 'PenerimaanGudang::index', ['filter' => 'role:admin']);
    $routes->get('penjualan', 'Penjualan::index', ['filter' => 'role:petugas']);
    $routes->get('suplier', 'Suplier::index', ['filter' => 'role:admin,owner']);
    $routes->get('customer', 'Customer::index');
    $routes->get('laporan/penjualan', 'Laporan::lap_penjualan');
    $routes->get('laporan/penjualan_detail/(:num)', 'Laporan::penjualan_detail/$1');
    $routes->get('laporan/surat-jalan',                'Laporan::lap_surat_jalan');
    $routes->get('laporan/surat-jalan-detail/(:num)',  'Laporan::surat_jalan_detail/$1');
});
