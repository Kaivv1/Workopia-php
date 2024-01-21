<?php

class Router
{
  protected $routes = [];

  public function registerRoute($method, $url, $controller)
  {
    $this->routes[] = [
      'method' => $method,
      'url' => $url,
      'controller' => $controller,
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

  public function error($httpCode = 404)
  {
    http_response_code($httpCode);
    loadView("error/{$httpCode}");
    exit;
  }


  public function route($method, $url)
  {
    foreach ($this->routes as $route) {
      if ($route['url'] === $url && $route['method'] === $method) {
        require basePath('App/' . $route['controller']);
        return;
      }
    }
    $this->error();
  }
}
