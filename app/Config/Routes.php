<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('login', 'Auth::login');
$routes->post('login/proses', 'Auth::proses_login');
$routes->get('logout', 'Auth::logout');

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

// Rute Pengelolaan User & Role
$routes->get('setting/users', 'Setting::users');
$routes->get('setting/users/tambah', 'Setting::users_tambah');
$routes->post('setting/users/simpan', 'Setting::users_simpan');
$routes->get('setting/users/edit/(:num)', 'Setting::users_edit/$1');
$routes->post('setting/users/update/(:num)', 'Setting::users_update/$1');
$routes->get('setting/users/hapus/(:num)', 'Setting::users_hapus/$1');

$routes->get('setting/roles', 'Setting::roles');
$routes->get('setting/roles/tambah', 'Setting::roles_tambah');
$routes->post('setting/roles/simpan', 'Setting::roles_simpan');
$routes->get('setting/roles/edit/(:num)', 'Setting::roles_edit/$1');
$routes->post('setting/roles/update/(:num)', 'Setting::roles_update/$1');
$routes->get('setting/roles/hapus/(:num)', 'Setting::roles_hapus/$1');


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
