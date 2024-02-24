<?php

namespace App\Database;
require_once __DIR__ . '/../../autoload.php';

abstract class AbstractProduct {
    public $pdo;
    public $sku;
    public $name;
    public $price;

    public function __construct() {
        $this->pdo = Database::connect();
    }

    public function setSku($sku) {
        $this->sku = $sku;
    }

    public function getSku() {
        return $this->sku;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function getName() {
        return $this->name;
    }

    public function setPrice($price) {
        $this->price = $price;
    }

    public function getPrice() {
        return $this->price;
    }

    public function getProduct()
    {
        $stmt = $this->pdo->prepare("SELECT * FROM products WHERE sku = :sku");

        $stmt->bindParam(':sku', $this->sku, \PDO::PARAM_STR);

        $stmt->execute();

        $product = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $product;
    }

    public function delete($productId)
    {
        $stmt = $this->pdo->prepare("DELETE FROM products WHERE id = :product_id");

        $stmt->bindParam(':product_id', $productId, \PDO::PARAM_INT);

        $success = $stmt->execute();

        return ['success' => $success];
    }

    protected function validateProduct()
    {
        $errors = [];

        if (empty($this->sku)) {
            $errors['sku'] = 'The sku field is required';
        } else if (!empty($this->getProduct())) {
            $errors['sku'] = "The sku must be unique";
        }

        if (empty($this->name)) {
            $errors['name'] = 'The name field is required';
        }

        if (empty($this->price)) {
            $errors['price'] = 'The price field is required';
        } else if (!is_numeric($this->price)) {
            $errors['price'] = "The price field must only contain numbers";
        }

        return $errors;
    }

    public function saveProduct()
    {
        try {
            $stmt = $this->pdo->prepare("INSERT INTO products (name, sku, price) VALUES (:name, :sku, :price)");
            $stmt->bindParam(':name', $this->name);
            $stmt->bindParam(':sku', $this->sku);
            $stmt->bindParam(':price', $this->price);
            $stmt->execute();

            return $this->pdo->lastInsertId();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    abstract public function validate();

    abstract public function save();
}


?>