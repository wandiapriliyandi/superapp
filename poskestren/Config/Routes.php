<?php

// Rute REST API Poskestren (Dilindungi JWT)
$routes->group('api/poskestren', ['namespace' => 'Poskestren\Controllers\Api', 'filter' => 'jwt'], function ($routes) {
    $routes->get('dashboard', 'Poskestren::getDashboard');
    $routes->get('kunjungan', 'Poskestren::getKunjungan');
    $routes->post('kunjungan/save', 'Poskestren::saveKunjungan');
    $routes->get('kunjungan/detail/(:num)', 'Poskestren::detailKunjungan/$1');
    $routes->post('kunjungan/update-perkembangan/(:num)', 'Poskestren::updatePerkembangan/$1');
    $routes->delete('kunjungan/delete/(:num)', 'Poskestren::deleteKunjungan/$1');

    $routes->get('obat', 'Poskestren::getObat');
    $routes->post('obat/save', 'Poskestren::saveObat');
    $routes->delete('obat/delete/(:num)', 'Poskestren::deleteObat/$1');

    $routes->get('stok/riwayat', 'Poskestren::getRiwayatStok');
    $routes->post('stok/pengadaan', 'Poskestren::savePengadaan');
    $routes->post('stok/keluar', 'Poskestren::saveMutasiKeluar');

    $routes->get('santri', 'Poskestren::getSantriList');
    $routes->get('obat-aktif', 'Poskestren::getObatAktif');
});
