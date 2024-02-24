<?php

require_once('autoload.php');

if($_SERVER['REQUEST_METHOD'] == "POST"){

    if($_SERVER['REQUEST_METHOD'] == "POST"){
        $type = $_POST['product-type'];
        if($type != 'Book' && $type != 'Furniture' && $type != 'DVD'){
            $response['success'] = false;
            $response['errors']['product-type'] = "Invalid product type"; 
            header("Content-Type: application/json");
            echo json_encode($response);
            return;
        }
        $className = "App\\Database\\$type";
        $product = new $className();
        $product->setSku($_POST['sku']);
        $product->setName($_POST['name']);
        $product->setPrice($_POST['price']);
            
        $keysToRemove = ['sku', 'name', 'price'];
        $additionalData = array_diff_key($_POST, array_flip($keysToRemove));
    
        foreach($additionalData as $key => $value) {
            $methodName = 'set' . ucfirst($key);
            if (method_exists($product, $methodName)) {
                $product->$methodName($value); 
            }
        }
    
        $product->save();
    }
}

?>