<?php

$routes->group('osis', ['namespace' => 'Osis\Controllers'], function ($routes) {
    $routes->get('/', 'Home::index');
});
