<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('login', 'Auth::login');
$routes->post('login/proses', 'Auth::proses_login');
$routes->get('logout', 'Auth::logout');

// Rute Publik REST API Autentikasi
$routes->post('api/auth/login', 'Api\Auth::login');

// Rute OPTIONS global untuk menangani CORS Preflight dari browser/app
$routes->options('(:any)', function() {
    $response = response();
    $response->setStatusCode(200);
    $response->setHeader('Access-Control-Allow-Origin', '*');
    $response->setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
    $response->setHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS, PUT, DELETE');
    return $response;
});

/**
 * --------------------------------------------------------------------
 * Load Modules Routes
 * --------------------------------------------------------------------
 */
$modulesPath = ROOTPATH;
$modules = ['akademik', 'e-learning', 'kepegawaian', 'keuangan', 'osis', 'pembayaran', 'perpustakaan', 'ppdb', 'sarpras', 'spp', 'perijinan', 'poskestren', 'monitoring', 'alquran'];

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

// Rute REST API System Settings (Dilindungi JWT)
$routes->group('api/setting', ['namespace' => 'App\Controllers\Api', 'filter' => 'jwt'], function ($routes) {
    // Profil Pesantren
    $routes->get('profil', 'Setting::getProfil');
    $routes->post('profil/save', 'Setting::saveProfil');
    // Users
    $routes->get('users', 'Setting::indexUsers');
    $routes->post('users/save', 'Setting::saveUser');
    $routes->delete('users/delete/(:num)', 'Setting::deleteUser/$1');
    // Roles
    $routes->get('roles', 'Setting::indexRoles');
    $routes->post('roles/save', 'Setting::saveRole');
    $routes->delete('roles/delete/(:num)', 'Setting::deleteRole/$1');
    // Migrasi Database
    $routes->get('migrate', 'Setting::getMigrationStatus');
    $routes->post('migrate/latest', 'Setting::runMigrationLatest');
    $routes->post('migrate/rollback', 'Setting::runMigrationRollback');
    $routes->post('migrate/refresh', 'Setting::runMigrationRefresh');
    $routes->post('migrate/pull', 'Setting::pullGit');
    $routes->post('migrate/run-seeder', 'Setting::runSeeder');
});


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
