<?php

// Rute REST API SPP (Dilindungi JWT)
$routes->group('api/spp', ['namespace' => 'Spp\Controllers\Api', 'filter' => 'jwt'], function ($routes) {
    // Tagihan
    $routes->get('tagihan', 'Spp::indexTagihan');
    $routes->post('tagihan/generate-massal', 'Spp::generateTagihanMassal');
    $routes->post('tagihan/generate-tahunan', 'Spp::generateTagihanTahunan');
    $routes->post('tagihan/generate-santri/(:num)', 'Spp::generateTagihanSantri/$1');
    $routes->delete('tagihan/delete/(:num)', 'Spp::deleteTagihan/$1');

    // Pembayaran
    $routes->get('pembayaran', 'Spp::indexPembayaran');
    $routes->post('pembayaran/save', 'Spp::savePembayaran');

    // Tarif
    $routes->get('tarif', 'Spp::indexTarif');
    $routes->post('tarif/save', 'Spp::saveTarif');
    $routes->delete('tarif/delete/(:num)', 'Spp::deleteTarif/$1');

    // Mapping
    $routes->get('mapping', 'Spp::indexMapping');
    $routes->get('mapping/santri/(:num)', 'Spp::santriMapping/$1');
    $routes->post('mapping/save', 'Spp::saveMapping');

    // Referensi & Stats
    $routes->get('stats', 'Spp::stats');
    $routes->get('tahun-akademik', 'Spp::listTahunAkademik');
});
