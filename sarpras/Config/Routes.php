<?php

$routes->group('api/sarpras', ['namespace' => 'Sarpras\Controllers\Api', 'filter' => 'jwt'], function ($routes) {
    // Master Barang
    $routes->get('barang', 'Sarpras::indexBarang');
    $routes->post('barang/save', 'Sarpras::saveBarang');
    $routes->delete('barang/delete/(:num)', 'Sarpras::deleteBarang/$1');

    // Mutasi Stok
    $routes->get('mutasi', 'Sarpras::indexMutasi');
    $routes->post('mutasi/save', 'Sarpras::saveMutasi');

    // Peminjaman
    $routes->get('peminjaman', 'Sarpras::indexPeminjaman');
    $routes->post('peminjaman/save', 'Sarpras::savePeminjaman');
    $routes->post('peminjaman/kembalikan/(:num)', 'Sarpras::kembalikanPeminjaman/$1');
    $routes->delete('peminjaman/delete/(:num)', 'Sarpras::deletePeminjaman/$1');
});
