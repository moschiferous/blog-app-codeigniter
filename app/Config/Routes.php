<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->group('api', function($routes) {
    $routes->get('/', 'HomeController::index');
    $routes->post('register', 'AuthController::register');
    $routes->post('login', 'AuthController::login');

    $routes->group('/', ['filter'=>'jwtauth'], function($routes) {
        $routes->get('logout', 'AuthController::logout');
        $routes->get('user', 'AuthController::getLoggedInUser');
    });

    $routes->group('blog', ['filter'=>'jwtauth'], function($routes) {
        $routes->get('', 'BlogController::index');
        $routes->get('id/(:num)', 'BlogController::show/$1');
        $routes->get('slug/(:segment)', 'BlogController::showBySlug/$1');
        $routes->get('search', 'BlogController::search');
        $routes->get('random', 'BlogController::getRandomBlogs');
        $routes->get('by-author/(:num)', 'BlogController::getBlogsByAuthor/$1');
        $routes->post('store', 'BlogController::store');
        $routes->put('update/(:num)', 'BlogController::update/$1');
        $routes->delete('destroy/(:num)', 'BlogController::destroy/$1');
    });

});
