<?php

namespace App\Database;
require_once __DIR__ . '/../../autoload.php';

class DVD extends AbstractProduct {
    public $size;

    public function setSize($size) {
        $this->size = $size;
    }

    public function getSize() {
        return $this->size;
    }

    public function getAllDVDs()
    {
        $sql = "SELECT d.*, p.name AS product_name, p.sku, p.price 
                FROM dvds d 
                JOIN products p ON d.product_id = p.id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        $dvds = $stmt->fetchAll(\PDO::FETCH_OBJ);

        foreach ($dvds as $dvd) {
            $dvd->product_id = $dvd->product_id;
            $dvd->name = $dvd->product_name;
            $dvd->sku = $dvd->sku;
            $dvd->price = $dvd->price;
            $dvd->size = $dvd->size;
        }

        return $dvds;
    }


    public function validate()
    {
        $errors = parent::validateProduct();

        if (empty($this->size)) {
            $errors['size'] = 'The size field is required';
        } else if (!is_numeric($this->size)) {
            $errors['size'] = "The size field must only contain numbers";
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

            $stmt = $this->pdo->prepare("INSERT INTO dvds (product_id, size) VALUES (:productId, :size)");
            $stmt->bindParam(':productId', $productId);
            $stmt->bindParam(':size', $this->size);
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