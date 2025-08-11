<?php
// Carga el autoload
require dirname(__DIR__) . "/vendor/autoload.php";

// Configurar en ErrorHandler
set_error_handler("ErrorHandler::handleError");
set_exception_handler("ErrorHandler::handleException");

// Cargar variables de entorno
$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__) . "/");
$dotenv->load();

// Configuración del heder tipo JSON
header("Content-type: application/json; charset=UTF-8");