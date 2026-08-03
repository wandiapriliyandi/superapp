<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

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
    // Log Aktivitas
    $routes->get('activity', 'Setting::indexActivity');
    $routes->delete('activity/clear', 'Setting::clearActivity');
});

// Rute REST API Dashboard (Dilindungi JWT)
$routes->group('api/dashboard', ['namespace' => 'App\Controllers\Api', 'filter' => 'jwt'], function ($routes) {
    $routes->get('stats', 'Dashboard::getStats');
});
