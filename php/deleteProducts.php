<?php

require_once('autoload.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['productIds'])) {
    $productIds = $_POST['productIds'];
    $success = true;

    $types = ['Book', 'DVD', 'Furniture'];

    foreach ($productIds as $productId) {
        foreach ($types as $type) {
            $className = "App\\Database\\$type";
            $product = new $className();
            $result = $product->delete($productId);
            if (!$result['success']) {
                $success = false;
                header("Content-Type: application/json");
                echo json_encode(['success' => $success, 'error' => $result['error']]);
                exit;
            }
        }
    }
    header("Content-Type: application/json");
    echo json_encode(['success' => $success]);
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
}

?>
