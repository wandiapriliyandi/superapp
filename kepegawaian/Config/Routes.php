<?php

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
