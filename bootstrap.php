<?php

require "vendor/autoload.php";
if (!file_exists(__DIR__ . '/.env')){
    echo "Unable to load configurations file.";
    http_response_code(412);
    return;
}
use Dotenv\Dotenv;
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();
