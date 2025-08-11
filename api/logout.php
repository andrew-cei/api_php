<?php

declare(strict_types=1);

require __DIR__ . "/bootstrap.php";
// Revisión del método POST
if($_SERVER["REQUEST_METHOD"] !== "POST"){

    http_response_code(405);
    header("Allow: POST");
    exit;
}
// Lectura del cuerpo con datos JSON
$data = (array) json_decode(file_get_contents("php://input"), true);
// Si no viene el token salir
if ( !array_key_exists("token", $data)) {
    http_response_code(400);
    echo json_encode(["message" => "missing token"]);
    exit;
}
// Creación del codificador-decodificador
$codec = new JWTCodec($_ENV["SECRET_KEY"]);
// Decodifica el token para refrescar
try{
    $payload = $codec->decode($data["token"]);
} catch (Exception) {
    http_response_code(400);
    echo json_encode(["message" => "invalid token"]);
    exit;
}
// Creación del objeto Base de Datos
$database = new Database(
    $_ENV["DB_HOST"],
    $_ENV["DB_NAME"],
    $_ENV["DB_USER"],
    $_ENV["DB_PASS"]
);
// Creación del objeto para refrescar token
$refresh_token_gateway = new RefreshTokenGateway($database, $_ENV["SECRET_KEY"]);
// Borrado del token de la DB
$refresh_token_gateway->delete($data["token"]);