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
$modules = ['akademik', 'e-learning', 'kepegawaian', 'keuangan', 'osis', 'pembayaran', 'perpustakaan', 'ppdb', 'sarpras', 'spp', 'perijinan', 'poskestren', 'monitoring'];

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

// Rute Migrasi Paksa Database
$routes->get('migrate', 'Migrate::index');
$routes->get('migrate/latest', 'Migrate::latest');
$routes->get('migrate/refresh', 'Migrate::refresh');
$routes->get('migrate/rollback', 'Migrate::rollback');
$routes->get('migrate/force-reset', 'Migrate::forceReset');
$routes->post('migrate/force-run-single', 'Migrate::forceRunSingle');
$routes->get('migrate/table-data', 'Migrate::getTableData');
$routes->get('migrate/pull-git', 'Migrate::pullGit');


// Rute Verifikasi Publik (QR Code)
$routes->get('verify/receipt/(:any)', 'Verify::receipt/$1');
$routes->get('verify/agreement/(:any)', 'Verify::agreement/$1');
