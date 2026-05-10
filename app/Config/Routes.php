<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

/**
 * --------------------------------------------------------------------
 * Load Modules Routes
 * --------------------------------------------------------------------
 */
$modulesPath = ROOTPATH;
$modules = ['akademik', 'e-learning', 'keuangan', 'osis', 'pembayaran', 'perpustakaan', 'ppdb', 'sarpras'];

foreach ($modules as $module) {
    $routesFile = $modulesPath . $module . '/Config/Routes.php';
    if (file_exists($routesFile)) {
        require $routesFile;
    }
}

$routes->get('setting', 'Setting::index');
$routes->get('setting/theme', 'Setting::theme');
$routes->post('setting/update', 'Setting::update');
$routes->get('activity', 'Activity::index');

$routes->group('kepegawaian', ['namespace' => 'App\Controllers'], function ($routes) {
    $routes->get('karyawan', 'Karyawan::index');
    $routes->get('karyawan/add', 'Karyawan::add');
    $routes->post('karyawan/save', 'Karyawan::save');
    $routes->get('karyawan/selection', 'Karyawan::selection');
    $routes->get('karyawan/export-excel', 'Karyawan::export_excel');
    $routes->get('karyawan/show/(:num)', 'Karyawan::show/$1');
    $routes->get('karyawan/delete/(:num)', 'Karyawan::delete/$1');
});
