<?php

$routes->group('poskestren', ['namespace' => 'Poskestren\Controllers'], function ($routes) {
    $routes->get('/', 'Poskestren::index');
    
    // Kunjungan / Rekam Medis
    $routes->get('kunjungan', 'Poskestren::kunjungan');
    $routes->get('kunjungan/tambah', 'Poskestren::tambah_kunjungan');
    $routes->post('kunjungan/simpan', 'Poskestren::simpan_kunjungan');
    $routes->get('kunjungan/detail/(:num)', 'Poskestren::detail_kunjungan/$1');
    $routes->get('kunjungan/hapus/(:num)', 'Poskestren::hapus_kunjungan/$1');

    // Obat (master data)
    $routes->get('obat', 'Obat::index');
    $routes->get('obat/tambah', 'Obat::tambah');
    $routes->post('obat/simpan', 'Obat::simpan');
    $routes->get('obat/edit/(:num)', 'Obat::edit/$1');
    $routes->post('obat/update/(:num)', 'Obat::update/$1');
    $routes->get('obat/hapus/(:num)', 'Obat::hapus/$1');

    // Stok: pengadaan, keluar manual, kartu stok
    $routes->get('stok/riwayat', 'Stok::riwayat');
    $routes->get('stok/pengadaan', 'Stok::pengadaan');
    $routes->post('stok/pengadaan/simpan', 'Stok::simpan_pengadaan');
    $routes->get('stok/keluar', 'Stok::keluar');
    $routes->post('stok/keluar/simpan', 'Stok::simpan_keluar');

    // API/Ajax untuk pilih santri atau obat
    $routes->get('api/santri', 'Poskestren::get_santri');
    $routes->get('api/obat', 'Obat::get_obat');
});
