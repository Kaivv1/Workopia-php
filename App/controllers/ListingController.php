<?php

namespace App\Controllers;

use Framework\Database;
use Framework\Validation;

class ListingController
{
  protected $db;

  public function __construct()
  {
    $config = require basePath('config/db.php');
    $this->db = new Database($config);
  }

  public function index()
  {
    $listings = $this->db->query('SELECT * FROM workopia.listings')->fetchAll();

    loadView('listings/index', ['listings' => $listings]);
  }

  public function create()
  {
    loadView('listings/create');
  }

  public function show($params)
  {
    $id = $params['id'] ?? '';
    $params = ['id' => $id];
    $listing = $this->db->query('SELECT * FROM workopia.listings WHERE id = :id', $params)->fetch();

    if (!$listing) {
      ErrorController::notFound('Listing not found');
      return;
    }

    loadView('listings/show', ['listing' => $listing]);
  }

  public function store()
  {
    $allowedFields = ['title', 'description', 'salary', 'tags', 'company', 'address', 'city', 'state', 'phone', 'email', 'requirements', 'benefits'];

    $newListingData = array_intersect_key($_POST, array_flip($allowedFields));

    $newListingData["user_id"] = 1;

    $newListingData = array_map('sanatize', $newListingData);

    $requiredFields = ['title', 'description', 'email', 'state', 'salary'];

    $errors = [];


    foreach ($requiredFields as $field) {
      if (empty($newListingData[$field]) || !Validation::string($newListingData[$field])) {
        $errors[$field] = ucfirst($field) . ' is required!';
      }
    }
    if (!empty($errors)) {
      loadView('listings/create', [
        'errors' => $errors,
        'listing' => $newListingData,
      ]);
    } else {
      echo "Success";

      $fields = [];
      foreach ($newListingData as $field => $value) {
        $fields[] = $field;
      }

      $fields = implode(', ', $fields);
      $values = [];

      foreach ($newListingData as $field => $value) {
        if ($value === '') {
          $newListingData[$field] = null;
        }
        $values[] = ':' . $field;
      }

      $values = implode(', ', $values);

      $query = "INSERT INTO workopia.listings ({$fields}) VALUES ({$values})";

      $this->db->query($query, $newListingData);
      redirect('/listings');
    }
  }


  public function destroy($params)
  {
    $id = $params['id'];
    $params = [
      'id' => $id
    ];
    $listing = $this->db->query('SELECT * FROM workopia.listings WHERE id = :id', $params)->fetch();
    if (!$listing) {
      ErrorController::notFound('Listing not found');
      return;
    }

    $this->db->query('DELETE FROM workopia.listings WHERE id = :id', $params);

    $_SESSION['success_message'] = 'Listing deleted successfully';
    redirect('/listings');
  }


  public function edit($params)
  {
    $id = $params['id'] ?? '';
    $params = ['id' => $id];
    $listing = $this->db->query('SELECT * FROM workopia.listings WHERE id = :id', $params)->fetch();

    if (!$listing) {
      ErrorController::notFound('Listing not found');
      return;
    }

    loadView('listings/edit', ['listing' => $listing]);
  }



  public function update($params)
  {
    $id = $params['id'] ?? '';
    $params = ['id' => $id];
    $listing = $this->db->query('SELECT * FROM workopia.listings WHERE id = :id', $params)->fetch();

    if (!$listing) {
      ErrorController::notFound('Listing not found');
      return;
    }

    $allowedFields = ['title', 'description', 'salary', 'tags', 'company', 'address', 'city', 'state', 'phone', 'email', 'requirements', 'benefits'];

    $updateValues = [];

    $updateValues = array_intersect_key($_POST, array_flip($allowedFields));
    $updateValues = array_map('sanatize', $updateValues);
    $requiredFields = ['title', 'description', 'email', 'state', 'salary'];
    $errors = [];

    foreach ($requiredFields as $field) {
      if (empty($updateValues[$field]) || !Validation::string($updateValues[$field])) {
        $errors[$field] = ucwords($field) . ' is required!';
      }
    }

    if (!empty($errors)) {
      loadView('listings/edit', ['listing' => $listing, 'errors' => $errors]);
      exit;
    } else {
      $updateFields = [];
      foreach (array_keys($updateValues) as $field) {
        $updateFields[] = "{$field} = :{$field}";
      }
      $updateFields = implode(', ', $updateFields);

      $updateQuery = "UPDATE workopia.listings SET {$updateFields} WHERE id = :id";
      $updateValues['id'] = $id;
      $this->db->query($updateQuery, $updateValues);

      $_SESSION['success_message'] = 'Listing updated';
      redirect('/listings/' . $id);
    }
  }
}
