<?php

function basePath($path = '')
{
  return __DIR__ . '/' . $path;
}

function loadView($name, $data = [])
{
  $viewPath =  basePath("App/views/{$name}.view.php");

  if (file_exists($viewPath)) {
    extract($data);
    require $viewPath;
  } else {
    echo "View {$name} not found!";
  }
}

function loadPartial($name)
{
  $partialPath = basePath("App/views/partials/{$name}.php");

  if (file_exists($partialPath)) {
    require $partialPath;
  } else {
    echo "Partial {$name} was not found!";
  }
}


function inspect($value)
{
  echo '<pre>';
  var_dump($value);
  echo '</pre>';
}

function inspectAndDie($value)
{
  echo '<pre>';
  die(var_dump($value));
  echo '</pre>';
}

function formatSalary($salary)
{
  return '$' . number_format($salary);
}


function sanatize($dirty)
{
  return filter_var(trim($dirty), FILTER_SANITIZE_SPECIAL_CHARS);
}
