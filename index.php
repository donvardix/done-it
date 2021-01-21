<?php

$q = key($_GET);
$params = explode('/', $q);

$routes = [
    ''              => '/views/index.html',
    'login'         => '/views/login.html',
    'register'      => '/views/register.html',
    'admin'         => '/views/admin.html',
    'api'           => '/api/index.php',
];

if ( array_key_exists( $params[0], $routes ) ){
    require_once __DIR__ . $routes[$params[0]];
} else {
    echo '404';
}