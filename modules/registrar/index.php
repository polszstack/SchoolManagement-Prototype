<?php


date_default_timezone_set('Asia/Manila');
require_once __DIR__ . '/app/bootstrap.php';

use App\Core\Router;
use App\Core\Session;

Session::start();



// for the routing
$router = new Router();
$router->dispatch();







