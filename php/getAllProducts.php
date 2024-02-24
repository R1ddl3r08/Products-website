<?php

require_once('autoload.php');

$book = new App\Database\Book();
$furniture = new App\Database\Furniture();
$dvd = new App\Database\DVD();

$allBooks = $book->getAllBooks();
$allFurnitures = $furniture->getAllFurnitures();
$allDVDs = $dvd->getAllDVDs();

$response = ['allBooks' => $allBooks, 'allFurnitures' => $allFurnitures, 'allDVDs' => $allDVDs];

header("Content-Type: application/json");
echo json_encode($response);

?>