<?php

$routes->group('keuangan', ['namespace' => 'Keuangan\Controllers'], function ($routes) {
    // Dashboard Keuangan
    $routes->get('/', 'Dashboard::index');
    
    // Tarif SPP
    $routes->group('tarif', function($routes) {
        $routes->get('/', 'Tarif::index');
        $routes->get('add', 'Tarif::add');
        $routes->post('save', 'Tarif::save');
        $routes->get('delete/(:num)', 'Tarif::delete/$1');
    });

    // Tagihan SPP
    $routes->group('tagihan', function($routes) {
        $routes->get('/', 'Tagihan::index');
        $routes->get('generate', 'Tagihan::generate');
        $routes->post('process-generate', 'Tagihan::processGenerate');
        $routes->get('delete/(:num)', 'Tagihan::delete/$1');
    });

    // Pembayaran
    $routes->group('pembayaran', function($routes) {
        $routes->get('/', 'Pembayaran::index');
        $routes->get('bayar/(:num)', 'Pembayaran::bayar/$1');
        $routes->post('save', 'Pembayaran::save');
    });
});
