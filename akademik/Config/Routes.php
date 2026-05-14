<?php

$routes->group('akademik', ['namespace' => 'Akademik\Controllers'], function ($routes) {
    $routes->get('/', 'Dashboard::index');
    $routes->get('dashboard', 'Dashboard::index');
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

    // Kelas
    $routes->get('kelas', 'Kelas::index');
    $routes->get('kelas/create', 'Kelas::create');
    $routes->post('kelas/store', 'Kelas::store');
    $routes->get('kelas/edit/(:num)', 'Kelas::edit/$1');
    $routes->post('kelas/update/(:num)', 'Kelas::update/$1');
    $routes->get('kelas/delete/(:num)', 'Kelas::delete/$1');

    // Mata Pelajaran
    $routes->get('mapel', 'Mapel::index');
    $routes->get('mapel/create', 'Mapel::create');
    $routes->post('mapel/store', 'Mapel::store');
    $routes->get('mapel/edit/(:num)', 'Mapel::edit/$1');
    $routes->post('mapel/update/(:num)', 'Mapel::update/$1');
    $routes->get('mapel/delete/(:num)', 'Mapel::delete/$1');

    // Jadwal
    $routes->get('jadwal', 'Jadwal::index');
    $routes->get('jadwal/create', 'Jadwal::create');
    $routes->post('jadwal/store', 'Jadwal::store');
    $routes->get('jadwal/delete/(:num)', 'Jadwal::delete/$1');

    // Presensi
    $routes->get('presensi', 'Presensi::index');
    $routes->get('presensi/input/(:num)', 'Presensi::input/$1');
    $routes->post('presensi/store', 'Presensi::store');
    $routes->get('presensi/rekap', 'Presensi::rekap');


    // Nilai
    $routes->get('nilai', 'Nilai::index');
    $routes->post('nilai/store', 'Nilai::store');
    $routes->get('nilai/rapor/(:num)', 'Nilai::rapor/$1');
});

