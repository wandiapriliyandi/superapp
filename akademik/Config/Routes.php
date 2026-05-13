<?php

$routes->group('akademik', ['namespace' => 'Akademik\Controllers'], function ($routes) {
    $routes->get('santri', 'Santri::index');
    $routes->get('santri/add', 'Santri::add');
    $routes->get('santri/selection', 'Santri::selection');
    $routes->get('santri/export-excel', 'Santri::export_excel');
    $routes->get('santri/export-word', 'Santri::export_word');
    $routes->get('santri/export-pdf', 'Santri::export_pdf');
    $routes->get('santri/show/(:num)', 'Santri::show/$1');
    $routes->get('santri/edit/(:num)', 'Santri::edit/$1');
    $routes->post('santri/save', 'Santri::save');
    $routes->get('santri/delete/(:num)', 'Santri::delete/$1');
    
    // Tahun Ajaran
    $routes->get('tahun-ajaran', 'TahunAjaran::index');
    $routes->get('tahun-ajaran/add', 'TahunAjaran::add');
    $routes->post('tahun-ajaran/save', 'TahunAjaran::save');
    $routes->get('tahun-ajaran/export-excel', 'TahunAjaran::export_excel');
    $routes->get('tahun-ajaran/export-word', 'TahunAjaran::export_word');
    $routes->get('tahun-ajaran/export-pdf', 'TahunAjaran::export_pdf');
    $routes->get('tahun-ajaran/set-active/(:num)', 'TahunAjaran::set_active/$1');
    $routes->get('tahun-ajaran/delete/(:num)', 'TahunAjaran::delete/$1');
});
