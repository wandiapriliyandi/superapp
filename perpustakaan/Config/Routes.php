<?php

// Rute REST API Perpustakaan (Dilindungi JWT)
$routes->group('api/perpustakaan', ['namespace' => 'Perpustakaan\Controllers\Api', 'filter' => 'jwt'], function ($routes) {
    $routes->get('buku', 'Perpustakaan::indexBuku');
    $routes->post('buku/save', 'Perpustakaan::saveBuku');
    $routes->delete('buku/delete/(:num)', 'Perpustakaan::deleteBuku/$1');
    $routes->get('stats', 'Perpustakaan::stats');
    $routes->get('drive-token', 'Perpustakaan::driveToken');
});
