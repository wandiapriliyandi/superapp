<?php

$routes->group('perijinan', ['namespace' => 'Perijinan\Controllers'], function ($routes) {
    $routes->get('/', 'Perijinan::index');
    $routes->get('tambah', 'Perijinan::tambah');
    $routes->post('simpan', 'Perijinan::simpan');
    $routes->get('approve/(:any)', 'Perijinan::approve/$1');
    $routes->post('reject/(:any)', 'Perijinan::reject/$1');
    $routes->get('aktifkan/(:any)', 'Perijinan::aktifkan/$1');
    $routes->get('kembali/(:any)', 'Perijinan::kembali/$1');
    $routes->get('hapus/(:any)', 'Perijinan::hapus/$1');
    $routes->get('cetak/(:any)', 'Perijinan::cetak/$1');
    $routes->get('rekap', 'Perijinan::rekap');
    $routes->get('pengaturan', 'Perijinan::pengaturan');
});

// Rute REST API Perijinan (Dilindungi JWT)
$routes->group('api/perijinan', ['namespace' => 'Perijinan\Controllers\Api', 'filter' => 'jwt'], function ($routes) {
    $routes->get('/', 'Perijinan::index');
    $routes->get('santri', 'Perijinan::santri');
    $routes->post('save', 'Perijinan::save');
    $routes->post('approve/(:any)', 'Perijinan::approve/$1');
    $routes->post('reject/(:any)', 'Perijinan::reject/$1');
    $routes->post('aktifkan/(:any)', 'Perijinan::aktifkan/$1');
    $routes->post('kembali/(:any)', 'Perijinan::kembali/$1');
    $routes->delete('delete/(:any)', 'Perijinan::delete/$1');
});
