<?php
require __DIR__ . '/../vendor/autoload.php';

use Framework\Router, Framework\Session;

Session::start();

require '../helpers.php';
$router = new Router();
$routes = require basePath('routes.php');
$url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$router->route($url);
