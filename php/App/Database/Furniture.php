<?php

namespace App\Database;
require_once __DIR__ . '/../../autoload.php';

class Furniture extends AbstractProduct {
    public $height;
    public $width;
    public $length;

    public function setHeight($height) {
        $this->height = $height;
    }

    public function getHeight() {
        return $this->height;
    }

    public function setWidth($width) {
        $this->width = $width;
    }

    public function getWidth() {
        return $this->width;
    }

    public function setLength($length) {
        $this->length = $length;
    }

    public function getLength() {
        return $this->length;
    }

    public function getAllFurnitures()
    {
        $sql = "SELECT f.*, p.name AS product_name, p.sku, p.price 
                FROM furnitures f 
                JOIN products p ON f.product_id = p.id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        $furnitures = $stmt->fetchAll(\PDO::FETCH_OBJ);

        foreach ($furnitures as $furniture) {
            $furniture->product_id = $furniture->product_id;
            $furniture->name = $furniture->product_name;
            $furniture->sku = $furniture->sku;
            $furniture->price = $furniture->price;
            $furniture->height = $furniture->height;
            $furniture->width = $furniture->width;
            $furniture->length = $furniture->length;
        }

        return $furnitures;
    }

    public function validate()
    {
        $errors = parent::validateProduct();

        // Additional validation specific to Furniture
        if (empty($this->height)) {
            $errors['height'] = 'The height field is required';
        } else if (!is_numeric($this->height)) {
            $errors['height'] = "The height field must only contain numbers";
        }

        if (empty($this->width)) {
            $errors['width'] = 'The width field is required';
        } else if (!is_numeric($this->width)) {
            $errors['width'] = "The width field must only contain numbers";
        }

        if (empty($this->length)) {
            $errors['length'] = 'The length field is required';
        } else if (!is_numeric($this->length)) {
            $errors['length'] = "The length field must only contain numbers";
        }

        return $errors;
    }

    public function save() {
        $errors = $this->validate();
        if(!empty($errors)){
            $response['success'] = false;
            $response['errors'] = $errors;
            header("Content-Type: application/json");
            echo json_encode($response);
            return;
        }

        $this->pdo->beginTransaction();

        try {
            $productId = parent::saveProduct();

            $stmt = $this->pdo->prepare("INSERT INTO furnitures (product_id, height, width, length) VALUES (:productId, :height, :width, :length)");
            $stmt->bindParam(':productId', $productId);
            $stmt->bindParam(':height', $this->height);
            $stmt->bindParam(':width', $this->width);
            $stmt->bindParam(':length', $this->length);
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
