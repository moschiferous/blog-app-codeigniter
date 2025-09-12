<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/api', 'HomeController::index');

$routes->group('api', function($routes) {
    $routes->post('register', 'AuthController::register');
    $routes->post('login', 'AuthController::login');

    $routes->group('/', ['filter'=>'jwtauth'], function($routes) {
        $routes->get('logout', 'AuthController::logout');
        $routes->get('user', 'AuthController::getLoggedInUser');
    });

});
