<?php

$routes->group('perijinan', ['namespace' => 'Perijinan\Controllers'], function ($routes) {
    $routes->get('/', 'Perijinan::index');
    $routes->get('tambah', 'Perijinan::tambah');
    $routes->post('simpan', 'Perijinan::simpan');
    $routes->get('approve/(:num)', 'Perijinan::approve/$1');
    $routes->post('reject/(:num)', 'Perijinan::reject/$1');
    $routes->get('aktifkan/(:num)', 'Perijinan::aktifkan/$1');
    $routes->get('kembali/(:num)', 'Perijinan::kembali/$1');
    $routes->get('hapus/(:num)', 'Perijinan::hapus/$1');
    $routes->get('rekap', 'Perijinan::rekap');
    $routes->get('pengaturan', 'Perijinan::pengaturan');
});
