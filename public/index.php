<?php
require '../helpers.php';
require basePath('Framework/Database.php');
require basePath('Framework/Router.php');

spl_autoload_register(function ($class) {
  $path = basePath('Framework/' . $class . '.php');
  if (file_exists($path)) {
    require $path;
  }
});


$router = new Router();
$routes = require basePath('routes.php');
$url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];
$router->route($method, $url);
