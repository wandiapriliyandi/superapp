<?php

$routes->group('spp', ['namespace' => 'Spp\Controllers'], function ($routes) {
    // Dashboard Keuangan
    $routes->get('/', 'Dashboard::index');
    
    // Master Tarif SPP
    $routes->group('tarif', function($routes) {
        $routes->get('/', 'Tarif::index');
        $routes->get('add', 'Tarif::add');
        $routes->get('export/(:any)', 'Tarif::export/$1');
        $routes->get('edit/(:num)', 'Tarif::edit/$1');
        $routes->post('save', 'Tarif::save');
        $routes->post('update/(:num)', 'Tarif::update/$1');
        $routes->get('delete/(:num)', 'Tarif::delete/$1');
    });

    // Tagihan SPP
    $routes->group('tagihan', function($routes) {
        $routes->get('/', 'Tagihan::index');
        $routes->get('generate', 'Tagihan::generate');
        $routes->get('export/(:any)', 'Tagihan::export/$1');
        $routes->get('generate-santri/(:num)', 'Tagihan::generateSantri/$1');
        $routes->post('process-generate', 'Tagihan::processGenerate');
        $routes->get('edit/(:num)', 'Tagihan::edit/$1');
        $routes->post('update/(:num)', 'Tagihan::update/$1');
        $routes->get('delete/(:num)', 'Tagihan::delete/$1');
    });

    // Pemetaan Tarif (Kesepakatan Bayaran)
    $routes->group('mapping', function($routes) {
        $routes->get('/', 'Mapping::index');
        $routes->get('export/(:any)', 'Mapping::export/$1');
        $routes->get('santri/(:num)', 'Mapping::santri/$1');
        $routes->post('save', 'Mapping::save');
        $routes->get('print/(:num)', 'Mapping::print/$1');
        $routes->get('export-word/(:num)', 'Mapping::exportWord/$1');
    });

    // Pembayaran
    $routes->group('pembayaran', function($routes) {
        $routes->get('/', 'Pembayaran::index');
        $routes->get('cari', 'Pembayaran::cari');
        $routes->get('transaksi', 'Pembayaran::transaksi');
        $routes->get('transaksi/export/(:any)', 'Pembayaran::export_transaksi/$1');
        $routes->get('detail/(:any)', 'Pembayaran::detail/$1');
        $routes->get('bayar/(:num)', 'Pembayaran::bayar/$1');
        $routes->get('kwitansi/(:any)', 'Pembayaran::kwitansi/$1');
        $routes->post('bayar-massal', 'Pembayaran::bayarMassal');
        $routes->post('save', 'Pembayaran::save');
        $routes->post('save-massal', 'Pembayaran::saveMassal');
    });

    $routes->get('workflow', 'Workflow::index');
});
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
