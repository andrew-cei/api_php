<?php
declare(strict_types=1);
require __DIR__ . '/bootstrap.php';

// Clientes y métodos permitidos
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("Access-Control-Allow-Origin: http://localhost:5173");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization, x-api-key");
    http_response_code(200);
    exit();
}

header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, PATCH, DELETE");
header("Access-Control-Allow-Headers *");

// Lectura de la URI
$path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
// Separación de la URI
$parts = explode("/", $path);
// Selección del recurso mediante URI
$resource = $parts[2];
// Selección del ID mediante URI
$id = $parts[3] ?? null;

if ($resource != "tasks") {
    //header("{$_SERVER['SERVER_PROTOCOL']} 404 Not Found");
    http_response_code(404);
    exit;
}

// Creación del objeto Base de Datos
$database = new Database(
    $_ENV["DB_HOST"],
    $_ENV["DB_NAME"],
    $_ENV["DB_USER"],
    $_ENV["DB_PASS"]
);
// Creación de la interfaz con la base de datos
$user_gateway = new UserGateway($database);
// Creación del codificador-decodificador
$codec = new JWTCodec($_ENV["SECRET_KEY"]);
// Creación del autorizador
$auth = new Auth($user_gateway, $codec);
// Salir si no hay autorización
if (! $auth->authenticateAccessToken()) {
    exit;
}
// Seleccionar ID del usuario
$user_id = $auth->getUserID();
// Creación del objeto gestor de tareas
$task_gateway = new TaskGateway($database);
// Creación de un controlador
$controller = new TaskController($task_gateway, $user_id);
// Invocación al método que procesa las peticiones
$controller->processRequest($_SERVER['REQUEST_METHOD'], $id);
