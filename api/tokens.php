<?php
// Datos a codificar en el token
$payload = [
    "sub" => $user["id"],
    "name" => $user["name"],
    "exp" => time() + 300,
];
// Creación del token de acceso
$access_token = $codec->encode($payload);
// Creación del token para refrescar
$refresh_token_expiry = time() + 432000;
$refresh_token = $codec->encode([
    "sub" => $user["id"],
    "exp" => $refresh_token_expiry,
]);
// Respuesta con ambos tokens
echo json_encode([
    "access_token" => $access_token,
    "refresh_token" => $refresh_token,
]);