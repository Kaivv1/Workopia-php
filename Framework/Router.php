<?php

namespace Framework;

use App\Controllers\ErrorController;

class Router
{
  protected $routes = [];

  public function registerRoute($method, $url, $action)
  {
    list($controller, $controllerMethod) = explode('@', $action);

    $this->routes[] = [
      'method' => $method,
      'url' => $url,
      'controller' => $controller,
      'controllerMethod' => $controllerMethod
    ];
  }

  public function get($url, $controller)
  {
    $this->registerRoute('GET', $url, $controller);
  }
  public function post($url, $controller)
  {
    $this->registerRoute('POST', $url, $controller);
  }
  public function put($url, $controller)
  {
    $this->registerRoute('PUT', $url, $controller);
  }
  public function delete($url, $controller)
  {
    $this->registerRoute('DELETE', $url, $controller);
  }


  public function route($url)
  {
    $requestMethod = $_SERVER['REQUEST_METHOD'];

    if ($requestMethod === 'POST' && isset($_POST['_method'])) {
      $requestMethod = strtoupper($_POST['_method']);
    }


    foreach ($this->routes as $route) {
      $urlSegments = explode('/', trim($url, '/'));

      $routeSegments = explode('/', trim($route['url'], '/'));
      $match = true;

      if (count($urlSegments) === count($routeSegments) && strtoupper($route['method'] === $requestMethod)) {
        $params = [];
        $match = true;
        for ($i = 0; $i < count($urlSegments); $i++) {
          if ($routeSegments[$i] !== $urlSegments[$i] && !preg_match('/\{(.+?)\}/', $routeSegments[$i])) {
            $match = false;
            break;
          }

          if (preg_match('/\{(.+?)\}/', $routeSegments[$i], $matches)) {
            $params[$matches[1]] = $urlSegments[$i];
          }
        }
        if ($match) {
          $controller = 'App\\Controllers\\' . $route['controller'];
          $controllerMethod = $route['controllerMethod'];

          $controllerInstance = new $controller();
          $controllerInstance->$controllerMethod($params);
          return;
        }
      }
    }
    ErrorController::notFound();
  }
}
