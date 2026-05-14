<?php

$routes->group('monitoring', ['namespace' => 'Monitoring\Controllers'], function ($routes) {
    $routes->get('/', 'Monitoring::index');
    $routes->get('akademik', 'Monitoring::akademik');
    $routes->get('keuangan', 'Monitoring::keuangan');
    $routes->get('santri', 'Monitoring::santri');
});
