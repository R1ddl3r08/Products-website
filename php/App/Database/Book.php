<?php

namespace App\Database;
require_once __DIR__ . '/../../autoload.php';

class Book extends AbstractProduct {
    public $weight;

    public function setWeight($weight) {
        $this->weight = $weight;
    }

    public function getWeight() {
        return $this->weight;
    }

    public function getAllBooks()
    {
        $sql = "SELECT b.*, p.name AS product_name, p.sku, p.price 
                FROM books b 
                JOIN products p ON b.product_id = p.id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        $books = $stmt->fetchAll(\PDO::FETCH_OBJ);

        foreach ($books as $book) {
            $book->product_id = $book->product_id;
            $book->name = $book->product_name;
            $book->sku = $book->sku;
            $book->price = $book->price;
            $book->weight = $book->weight;
        }
    
        return $books;
    }

    public function validate()
    {
        $errors = parent::validateProduct();

        if (empty($this->weight)) {
            $errors['weight'] = 'The weight field is required';
        } else if (!is_numeric($this->weight)) {
            $errors['weight'] = "The weight field must only contain numbers";
        }

        return $errors;
    }

    public function save() {
        $errors = $this->validate();
        if (!empty($errors)) {
            $response['success'] = false;
            $response['errors'] = $errors;
            header("Content-Type: application/json");
            echo json_encode($response);
            return;
        }

        $this->pdo->beginTransaction();

        try {
            $productId = parent::saveProduct();

            $stmt = $this->pdo->prepare("INSERT INTO books (product_id, weight) VALUES (:productId, :weight)");
            $stmt->bindParam(':productId', $productId);
            $stmt->bindParam(':weight', $this->weight);
            $stmt->execute();

            $this->pdo->commit();

            $response['success'] = true;
            header("Content-Type: application/json");
            echo json_encode($response);
        } catch (\Exception $e) {
            $this->pdo->rollback();
            header("Content-Type: application/json");
            echo json_encode($e);
        }
    }
}

?>