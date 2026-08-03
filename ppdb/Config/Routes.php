<?php

namespace Ppdb\Config;

$routes = \Config\Services::routes();

// Rute REST API PPDB (Dilindungi JWT)
$routes->group('api/ppdb', ['namespace' => 'Ppdb\Controllers\Api', 'filter' => 'jwt'], function ($routes) {
    // Pendaftar
    $routes->get('pendaftar', 'Ppdb::indexPendaftar');
    $routes->get('pendaftar/(:num)', 'Ppdb::showPendaftar/$1');
    $routes->post('pendaftar/save', 'Ppdb::savePendaftar');
    $routes->post('pendaftar/status/(:num)/(:any)', 'Ppdb::setStatus/$1/$2');
    $routes->delete('pendaftar/delete/(:num)', 'Ppdb::deletePendaftar/$1');
    // Jadwal Tes
    $routes->get('jadwal', 'Ppdb::indexJadwal');
    $routes->post('jadwal/save', 'Ppdb::saveJadwal');
    $routes->delete('jadwal/delete/(:num)', 'Ppdb::deleteJadwal/$1');
    $routes->get('jadwal/(:num)/peserta', 'Ppdb::pesertaJadwal/$1');
    $routes->post('jadwal/peserta/add', 'Ppdb::addPeserta');
    $routes->delete('jadwal/peserta/remove/(:num)', 'Ppdb::removePeserta/$1');
    $routes->post('jadwal/kehadiran', 'Ppdb::updateKehadiran');
    // Berkas
    $routes->get('syarat', 'Ppdb::indexSyarat');
    $routes->post('syarat/save', 'Ppdb::saveSyarat');
    $routes->delete('syarat/delete/(:num)', 'Ppdb::deleteSyarat/$1');
    $routes->get('berkas/(:num)', 'Ppdb::berkasPendaftar/$1');
    $routes->post('berkas/update', 'Ppdb::updateBerkas');
    // Pembayaran
    $routes->get('pembayaran/(:num)', 'Ppdb::pembayaranPendaftar/$1');
    $routes->post('pembayaran/save', 'Ppdb::savePembayaran');
    // Referensi & Stats
    $routes->get('stats', 'Ppdb::stats');
    $routes->get('tahun-ajaran', 'Ppdb::listTahunAjaran');
});
