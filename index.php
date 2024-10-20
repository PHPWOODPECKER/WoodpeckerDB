<?php

  require_once __DIR__ . '/../vendor/autoload.php';

  use Woodpeacker\DatabaseTable;

    $username = '';
    $password = '';
    $dbname = "";

    $dsn = "mysql:host=localhost;dbname=$dbname;charset=utf8mb4";
    
    try 
    {
      $pdo = new \PDO($dsn, $username, $password);

    }
    catch(PDOException $e) 
    {
        echo 'Connection failed: ' . $e->getMessage();
    }
    
    $database = new DatabaseTable($pdo, "users", "id");
    try{
      
      $database->transaction(function($database){
        $array = $database->findWithAnd([
          "name" => "ali",
          "age" => "18",
          "example@gmail.com"
          ]);
          if($array !== FALSE){
            $database->delete('id', 2);
          }
      });
    
    }catch(Exception $e){
    echo $e->getMessage();  
    }