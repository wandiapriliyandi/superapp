<?php

$routes->group('e-learning', ['namespace' => 'ELearning\Controllers'], function ($routes) {
    // Dashboard E-Learning
    $routes->get('/', 'Materi::index');
    
    // Kelola Materi Belajar
    $routes->get('materi', 'Materi::index');
    $routes->get('materi/add', 'Materi::add');
    $routes->post('materi/save', 'Materi::save');
    $routes->get('materi/show/(:num)', 'Materi::show/$1');
    $routes->get('materi/delete/(:num)', 'Materi::delete/$1');
    $routes->get('materi/export-excel', 'Materi::export_excel');
    $routes->get('materi/export-word', 'Materi::export_word');
    $routes->get('materi/export-pdf', 'Materi::export_pdf');

    // Kelola Ujian / Tugas Online
    $routes->get('ujian', 'Ujian::index');
    $routes->get('ujian/add', 'Ujian::add');
    $routes->post('ujian/save', 'Ujian::save');
    $routes->get('ujian/show/(:num)', 'Ujian::show/$1');
    $routes->get('ujian/delete/(:num)', 'Ujian::delete/$1');
    $routes->get('ujian/export-excel', 'Ujian::export_excel');
    $routes->get('ujian/export-word', 'Ujian::export_word');
    $routes->get('ujian/export-pdf', 'Ujian::export_pdf');
});
