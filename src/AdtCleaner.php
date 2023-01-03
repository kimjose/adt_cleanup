<?php

namespace Joseph\AdtCleanUp;

use Dotenv\Dotenv;

class AdtCleaner
{
    private $conn;
    public function __construct()
    {
        try {
            $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
            $dotenv->load();
            $conn = mysqli_connect($_ENV['DB_HOST'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD'], $_ENV['DB_NAME'], $_ENV['DB_PORT']);
            if($this->conn == null) throw new \Exception("Error Processing Request", 1);
            $query = "SELECT * FROM patients WHERE 'patient_ccc_number' LIKE '%/%'";
            $result = $conn->execute_query($query);
        } catch (\Throwable $th) {
        }
    }

    public function removeSeparator($separator)
    {
        try {
        } catch (\Throwable $th) {
        }
    }
}
