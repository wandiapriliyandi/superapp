<?php

// Rute REST API Modul Al-Qur'an (Dilindungi JWT)
$routes->group('api/alquran', ['namespace' => 'Alquran\Controllers\Api', 'filter' => 'jwt'], function ($routes) {
    $routes->get('santri', 'Alquran::indexSantri');
    $routes->get('tahsin', 'Alquran::indexTahsin');
    $routes->post('tahsin/save', 'Alquran::saveTahsin');
    $routes->delete('tahsin/delete/(:num)', 'Alquran::deleteTahsin/$1');
    $routes->get('tahfidz', 'Alquran::indexTahfidz');
    $routes->post('tahfidz/save', 'Alquran::saveTahfidz');
    $routes->delete('tahfidz/delete/(:num)', 'Alquran::deleteTahfidz/$1');
    $routes->get('doa', 'Alquran::indexDoa');
    $routes->post('doa/save', 'Alquran::saveDoa');
    $routes->delete('doa/delete/(:num)', 'Alquran::deleteDoa/$1');
    $routes->get('master-doa', 'Alquran::indexMasterDoa');
    $routes->post('master-doa/save', 'Alquran::saveMasterDoa');
    $routes->delete('master-doa/delete/(:num)', 'Alquran::deleteMasterDoa/$1');
    $routes->get('stats/(:num)', 'Alquran::stats/$1');
});
