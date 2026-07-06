<?php

use CodeIgniter\Router\RouteCollection;
//INFO GESTI ZgOR7rEPAHSVbW
/**
 * @var RouteCollection $routes
 */
$routes->get('manifest.webmanifest', 'Pwa::manifest');
$routes->get('login', 'Auth::login');
$routes->post('login', 'Auth::attempt');
$routes->get('register', 'Auth::register');
$routes->post('register', 'Auth::store');
$routes->get('logout', 'Auth::logout');

$routes->group('', ['filter' => 'auth'], function($routes) {
    $routes->get('/', 'C_Dashboard::index');
    $routes->get('admin/dashboard', 'C_Dashboard::index');

    // =========================
    // SALES / VENTES
    // =========================
    $routes->get('sales/create', 'Sales::create');
    $routes->get('sales/searchProducts', 'Sales::searchProducts');
    $routes->post('sales/addToCart', 'Sales::addToCart');
    $routes->post('sales/updateCartItem', 'Sales::updateCartItem');
    $routes->post('sales/updateCartPrice', 'Sales::updateCartPrice');
    $routes->post('sales/removeFromCart', 'Sales::removeFromCart');
    $routes->post('sales/store', 'Sales::store');
    $routes->get('sales/list', 'Sales::list');
    $routes->get('sales/detail/(:num)', 'Sales::detail/$1');

    // =========================
    // PRODUCTS / STOCK
    // =========================

    $routes->get('stock', 'C_Product::index');
    $routes->post('stock/store', 'C_Product::store');
    $routes->get('stock/edit/(:num)', 'C_Product::edit/$1');
    $routes->post('stock/update/(:num)', 'C_Product::update/$1');
    $routes->get('stock/delete/(:num)', 'C_Product::delete/$1');

    // DETTES
    $routes->get('debts', 'Debts::index');
    $routes->get('debts/delete/(:num)', 'Debts::delete/$1');

    // PARAMÈTRES
    $routes->get('settings', 'Settings::index');
    $routes->get('settings/revenue', 'Settings::revenueReport');
    $routes->post('settings/updateProfile', 'Settings::updateProfile');
    $routes->post('settings/updateShopInfo', 'Settings::updateShopInfo');
    $routes->post('settings/changePassword', 'Settings::changePassword');
    $routes->get('settings/toggleUser/(:num)', 'Settings::toggleUserStatus/$1');
    $routes->post('settings/approveUser/(:num)', 'Settings::approveUser/$1');
    $routes->post('settings/rejectUser/(:num)', 'Settings::rejectUser/$1');
});
