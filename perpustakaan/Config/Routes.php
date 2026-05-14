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
