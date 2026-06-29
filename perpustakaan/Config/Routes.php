<?php

$routes->group('perpustakaan', ['namespace' => 'Perpustakaan\Controllers'], function ($routes) {
    $routes->get('/', 'Dashboard::index');
    $routes->get('dashboard', 'Dashboard::index');
    $routes->get('list/(:any)', 'Dashboard::list/$1');
    $routes->get('tambah/(:any)', 'Dashboard::tambah/$1');
    $routes->post('simpan', 'Dashboard::simpan');
    $routes->get('hapus/(:num)', 'Dashboard::hapus/$1');
    $routes->get('pengaturan', 'Dashboard::pengaturan');
    $routes->post('simpan-konfigurasi', 'Dashboard::simpan_konfigurasi');
});

// Rute REST API Perpustakaan (Dilindungi JWT)
$routes->group('api/perpustakaan', ['namespace' => 'Perpustakaan\Controllers\Api', 'filter' => 'jwt'], function ($routes) {
    $routes->get('buku', 'Perpustakaan::indexBuku');
    $routes->post('buku/save', 'Perpustakaan::saveBuku');
    $routes->delete('buku/delete/(:num)', 'Perpustakaan::deleteBuku/$1');
    $routes->get('stats', 'Perpustakaan::stats');
});
