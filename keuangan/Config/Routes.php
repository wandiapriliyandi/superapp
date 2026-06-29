<?php

$routes->group('keuangan', ['namespace' => 'Keuangan\Controllers'], function ($routes) {
    $routes->get('/', 'Dashboard::index');
    
    // Akun / COA
    $routes->group('akun', function($routes) {
        $routes->get('/', 'Akun::index');
        $routes->get('add', 'Akun::add');
        $routes->post('save', 'Akun::save');
        $routes->get('edit/(:num)', 'Akun::edit/$1');
        $routes->post('update/(:num)', 'Akun::update/$1');
        $routes->get('delete/(:num)', 'Akun::delete/$1');
    });

    // Jurnal Umum
    $routes->group('jurnal', function($routes) {
        $routes->get('/', 'Jurnal::index');
        $routes->get('add', 'Jurnal::add');
        $routes->get('pemasukan', 'Jurnal::pemasukan');
        $routes->get('pengeluaran', 'Jurnal::pengeluaran');
        $routes->post('save', 'Jurnal::save');
        $routes->post('save_simple', 'Jurnal::save_simple');
        $routes->get('delete/(:any)', 'Jurnal::delete/$1');
    });

    // Buku Besar
    $routes->get('buku-besar', 'BukuBesar::index');
    $routes->get('buku-besar/filter', 'BukuBesar::filter');

    // Laporan
    $routes->group('laporan', function($routes) {
        $routes->get('neraca', 'Laporan::neraca');
        $routes->get('laba-rugi', 'Laporan::labaRugi');
        $routes->get('arus-kas', 'Laporan::arusKas');
    });
});

// Rute REST API Keuangan (Dilindungi JWT)
$routes->group('api/keuangan', ['namespace' => 'Keuangan\Controllers\Api', 'filter' => 'jwt'], function ($routes) {
    // Akun / COA
    $routes->get('akun', 'Keuangan::indexAkun');
    $routes->post('akun/save', 'Keuangan::saveAkun');
    $routes->delete('akun/delete/(:num)', 'Keuangan::deleteAkun/$1');

    // Jurnal Umum
    $routes->get('jurnal', 'Keuangan::indexJurnal');
    $routes->post('jurnal/save', 'Keuangan::saveJurnal');
    $routes->delete('jurnal/delete/(:num)', 'Keuangan::deleteJurnal/$1');
    $routes->get('jurnal/nomor', 'Keuangan::getJurnalNomor');

    // Buku Besar
    $routes->get('buku-besar', 'Keuangan::indexBukuBesar');

    // Laporan
    $routes->get('laporan/laba-rugi', 'Keuangan::laporanLabaRugi');
    $routes->get('laporan/neraca', 'Keuangan::laporanNeraca');
});
