<?php

namespace App\Database;

use PDO;

class Database
{

    protected static PDO|null $instance = null;


    private function __construct()
    {
        try {
            self::$instance = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
        }catch (\PDOException $e){
            echo $e->getMessage();
        }
    }

    public static function connect(){


        if (is_null(self::$instance)){
            new self();
        }

        return self::$instance;
    }

}

?>