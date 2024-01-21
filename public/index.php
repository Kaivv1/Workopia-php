<?php
require '../helpers.php';
require basePath('Database.php');
require basePath('Router.php');

$router = new Router();
$routes = require basePath('routes.php');
$url = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];
$router->route($method, $url);
