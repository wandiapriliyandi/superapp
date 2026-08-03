<?php

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
