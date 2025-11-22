<?php
function generarToken($email) {
    $secret = $_ENV['SECRET_KEY'];
    $exp = time() + 900; // 15 minutos de expiración

    $data = base64_encode(json_encode([
        'email' => $email,
        'exp' => $exp
    ]));

    $firma = hash_hmac('sha256', $data, $secret);

    return $data . '.' . $firma;
}

function verificarToken($token) {
    $secret = $_ENV['SECRET_KEY'];
    if (!strpos($token, '.')) return false;

    list($data, $firma) = explode('.', $token);

    if (hash_hmac('sha256', $data, $secret) !== $firma) {
        return false;
    }

    $payload = json_decode(base64_decode($data), true);
    if ($payload['exp'] < time()) {
        return false;
    }

    return $payload['email'];
}
