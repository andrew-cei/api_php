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
// Si no viene el username o el password salir
if ( !array_key_exists("username", $data) || !array_key_exists("password", $data)) {
    http_response_code(400);
    echo json_encode(["message" => "missing login credentials"]);
    exit;
}
// Lectura del usuario de la base de datos
$database = new Database($_ENV["DB_HOST"], $_ENV["DB_NAME"], $_ENV["DB_USER"], $_ENV["DB_PASS"]);
$user_gateway = new UserGateway($database);
$user = $user_gateway->getByUsername($data["username"]);
// Si no hay usuario salir
if ($user === false){
    http_response_code(401);
    echo json_encode(["message" => "invalid authentication"]);
    exit;
}
// Si la contraseña no coincide salir
if( !password_verify($data["password"], $user["password_hash"])) {
    http_response_code(401);
    echo json_encode(["message" => "invalid authentication"]);
    exit;
}
// Creación del codificador-decodificador
$codec = new JWTCodec($_ENV["SECRET_KEY"]);

require __DIR__ . "/tokens.php";
// Creación del objeto gestor de tpkens
$refresh_token_gateway = new RefreshTokenGateway($database, $_ENV["SECRET_KEY"]);
// Creación de nuevo token
$refresh_token_gateway->create($refresh_token, $refresh_token_expiry);