<?php
declare(strict_types=1);
//require dirname(__DIR__) . "/api/src/TaskController.php";
require __DIR__ . '/bootstrap.php';

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


$path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

$parts = explode("/", $path);

$resource = $parts[2];

$id = $parts[3] ?? null;

if ($resource != "tasks") {
    //header("{$_SERVER['SERVER_PROTOCOL']} 404 Not Found");
    http_response_code(404);
    exit;
}



$database = new Database(
    $_ENV["DB_HOST"],
    $_ENV["DB_NAME"],
    $_ENV["DB_USER"],
    $_ENV["DB_PASS"]
);

$user_gateway = new UserGateway($database);

$codec = new JWTCodec($_ENV["SECRET_KEY"]);

$auth = new Auth($user_gateway, $codec);

if (! $auth->authenticateAccessToken()) {
    exit;
}

$user_id = $auth->getUserID();

$task_gateway = new TaskGateway($database);

$controller = new TaskController($task_gateway, $user_id);

$controller->processRequest($_SERVER['REQUEST_METHOD'], $id);
