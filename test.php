<?php 
require_once __DIR__ . "/vendor/autoload.php";
use Dotenv\Dotenv;

try {
    $dotenv = Dotenv::createImmutable(__DIR__ . '/');
    $dotenv->load();
    $conn = mysqli_connect($_ENV['DB_HOST'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD'], $_ENV['DB_NAME'], $_ENV['DB_PORT']);
    if($conn == null) throw new \Exception("Error Processing Request", 1);
    $query = "SELECT * FROM patient";
    $result = $conn->query($query);
    $items = array();
    // echo $result;
    while($row = $result->fetch_array()){
        // echo 'Here';
        $items[] = $row;
    }
    print_r($items);
} catch (Throwable $th) {
    echo $th->getMessage();
}
