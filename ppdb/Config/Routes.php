<?php

namespace Ppdb\Config;

$routes = \Config\Services::routes();

$routes->group('ppdb', ['namespace' => 'Ppdb\Controllers'], function ($routes) {
    // Admin Side
    $routes->get('dashboard', 'Dashboard::index');
    $routes->get('pendaftar', 'Pendaftar::index');
    $routes->get('pendaftar/add', 'Pendaftar::add');
    $routes->match(['get', 'post'], 'pendaftar/save', 'Pendaftar::save');
    $routes->get('pendaftar/status/(:num)/(:any)', 'Pendaftar::set_status/$1/$2');
    $routes->get('pendaftar/show/(:num)', 'Pendaftar::show/$1');
    $routes->get('pendaftar/pembayaran/(:num)', 'Pendaftar::pembayaran/$1');
    $routes->match(['get', 'post'], 'pendaftar/save-pembayaran', 'Pendaftar::save_pembayaran');
    $routes->get('pendaftar/cetak-struk/(:num)', 'Pendaftar::cetak_struk/$1');
    $routes->get('pendaftar/delete/(:num)', 'Pendaftar::delete/$1');
    $routes->get('pendaftar/export-excel', 'Pendaftar::export_excel');
    $routes->get('pendaftar/export-word', 'Pendaftar::export_word');
    $routes->get('pendaftar/export-pdf', 'Pendaftar::export_pdf');
    $routes->get('pendaftar/selection', 'Pendaftar::selection');
    
    // Pengaturan
    $routes->get('pengaturan', 'Pengaturan::index');
    $routes->match(['get', 'post'], 'pengaturan/save', 'Pengaturan::save');

    // Berkas Dinamis (Checklist Fisik)
    $routes->get('berkas', 'Berkas::index');
    $routes->match(['get', 'post'], 'berkas/syarat-save', 'Berkas::syarat_save');
    $routes->get('berkas/syarat-delete/(:num)', 'Berkas::syarat_delete/$1');
    $routes->get('berkas/verifikasi', 'Berkas::verifikasi');
    $routes->get('berkas/detail/(:num)', 'Berkas::detail/$1');
    $routes->match(['get', 'post'], 'berkas/update-status', 'Berkas::update_status');

    // Penjadwalan Tes
    $routes->get('jadwal', 'JadwalTes::index');
    $routes->match(['get', 'post'], 'jadwal/save', 'JadwalTes::save');
    $routes->get('jadwal/delete/(:num)', 'JadwalTes::delete/$1');
    $routes->get('jadwal/detail/(:num)', 'JadwalTes::detail/$1');
    $routes->match(['get', 'post'], 'jadwal/add-peserta', 'JadwalTes::add_peserta');
    $routes->get('jadwal/remove-peserta/(:num)', 'JadwalTes::remove_peserta/$1');
    $routes->match(['get', 'post'], 'jadwal/update-kehadiran', 'JadwalTes::update_kehadiran');
    $routes->get('workflow', 'Workflow::index');

    // Public Side (Front-end)
    $routes->get('daftar', 'PublicPendaftaran::index');
    $routes->match(['get', 'post'], 'daftar/submit', 'PublicPendaftaran::submit');
    $routes->get('daftar/success/(:any)', 'PublicPendaftaran::success/$1');
});
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

