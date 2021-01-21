<?php

use models\Auth;
use models\Users;

require_once __DIR__ . '/connect_db.php';
require_once __DIR__ . '/Models/Users.php';
require_once __DIR__ . '/Models/Auth.php';

$method = $_SERVER['REQUEST_METHOD'];
$route = $params[1];
$id = $params[2];

switch ($route) {
    case 'register':
    case 'login':
        $auth = new Auth($connect, $route);
        echo $auth->apply();
        break;
    case 'users':
        $users = new Users($connect, $method, $id);
        echo $users->apply();
        break;
    default:
        http_response_code(404);
        echo json_encode(['status' => false, 'data' => 'Not Found']);
}