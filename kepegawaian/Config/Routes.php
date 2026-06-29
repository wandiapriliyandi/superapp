<?php

$routes->group('kepegawaian', ['namespace' => 'Kepegawaian\Controllers'], function ($routes) {
    $routes->get('dashboard', 'Dashboard::index');
    
    // Data Pegawai (Asatidz & Staff)
    $routes->get('pegawai', 'Pegawai::index');
    $routes->get('pegawai/add', 'Pegawai::add');
    $routes->post('pegawai/save', 'Pegawai::save');
    $routes->get('pegawai/edit/(:num)', 'Pegawai::edit/$1');
    $routes->post('pegawai/update/(:num)', 'Pegawai::update/$1');
    $routes->get('pegawai/delete/(:num)', 'Pegawai::delete/$1');
    
    // Organisasi
    $routes->get('departemen', 'Departemen::index');
    $routes->post('departemen/save', 'Departemen::save');
    $routes->get('departemen/delete/(:num)', 'Departemen::delete/$1');
    
    $routes->get('jabatan', 'Jabatan::index');
    $routes->post('jabatan/save', 'Jabatan::save');
    $routes->get('jabatan/delete/(:num)', 'Jabatan::delete/$1');

    // Penjadwalan
    $routes->get('jadwal', 'Jadwal::index');
    $routes->post('jadwal/save', 'Jadwal::save');
    $routes->get('jadwal/delete/(:num)', 'Jadwal::delete/$1');
    
    // Operasional
    $routes->get('absensi', 'Absensi::index');
    $routes->post('absensi/save', 'Absensi::save');
    
    $routes->get('cuti', 'Cuti::index');
    $routes->post('cuti/save', 'Cuti::save');
    $routes->get('cuti/approve/(:num)', 'Cuti::approve/$1');
    
    $routes->get('payroll', 'Payroll::index');
    $routes->post('payroll/generate', 'Payroll::generate');
    $routes->get('payroll/slip/(:num)', 'Payroll::slip/$1');
});

// Rute REST API Kepegawaian (Dilindungi JWT)
$routes->group('api/kepegawaian', ['namespace' => 'Kepegawaian\Controllers\Api', 'filter' => 'jwt'], function ($routes) {
    // Departemen
    $routes->get('departemen', 'Departemen::index');
    $routes->post('departemen/save', 'Departemen::save');
    $routes->delete('departemen/delete/(:num)', 'Departemen::delete/$1');
    
    // Pegawai
    $routes->get('pegawai', 'Kepegawaian::indexPegawai');
    $routes->post('pegawai/save', 'Kepegawaian::savePegawai');
    $routes->post('pegawai/update/(:num)', 'Kepegawaian::updatePegawai/$1');
    $routes->delete('pegawai/delete/(:num)', 'Kepegawaian::deletePegawai/$1');
    
    // Jabatan
    $routes->get('jabatan', 'Kepegawaian::indexJabatan');
    $routes->post('jabatan/save', 'Kepegawaian::saveJabatan');
    $routes->delete('jabatan/delete/(:num)', 'Kepegawaian::deleteJabatan/$1');

    // Absensi
    $routes->get('absensi', 'Kepegawaian::indexAbsensi');
    $routes->post('absensi/save', 'Kepegawaian::saveAbsensi');

    // Cuti
    $routes->get('cuti', 'Kepegawaian::indexCuti');
    $routes->post('cuti/save', 'Kepegawaian::saveCuti');
    $routes->post('cuti/status/(:num)/(:any)', 'Kepegawaian::setCutiStatus/$1/$2');

    // Payroll
    $routes->get('payroll', 'Kepegawaian::indexPayroll');
    $routes->post('payroll/generate', 'Kepegawaian::generatePayroll');
    $routes->post('payroll/bayar/(:num)', 'Kepegawaian::bayarPayroll/$1');
});

