<?php

// Rute REST API Akademik (Dilindungi JWT)
$routes->group('api/akademik', ['namespace' => 'Akademik\Controllers\Api', 'filter' => 'jwt'], function ($routes) {
    // Santri
    $routes->get('santri', 'Akademik::indexSantri');
    $routes->get('santri/(:num)', 'Akademik::showSantri/$1');
    $routes->post('santri/save', 'Akademik::saveSantri');
    $routes->delete('santri/delete/(:num)', 'Akademik::deleteSantri/$1');

    // Kelas
    $routes->get('kelas', 'Akademik::indexKelas');
    $routes->post('kelas/save', 'Akademik::saveKelas');
    $routes->delete('kelas/delete/(:num)', 'Akademik::deleteKelas/$1');

    // Mapel
    $routes->get('mapel', 'Akademik::indexMapel');
    $routes->post('mapel/save', 'Akademik::saveMapel');
    $routes->delete('mapel/delete/(:num)', 'Akademik::deleteMapel/$1');

    // Jadwal
    $routes->get('jadwal', 'Akademik::indexJadwal');
    $routes->post('jadwal/save', 'Akademik::saveJadwal');
    $routes->delete('jadwal/delete/(:num)', 'Akademik::deleteJadwal/$1');

    // Presensi
    $routes->get('presensi', 'Akademik::indexPresensi');
    $routes->post('presensi/save', 'Akademik::savePresensi');

    // Nilai
    $routes->get('nilai', 'Akademik::indexNilai');
    $routes->post('nilai/save', 'Akademik::saveNilai');

    // Referensi
    $routes->get('tahun-ajaran', 'Akademik::listTahunAjaran');
    $routes->get('guru', 'Akademik::listGuru');
});
