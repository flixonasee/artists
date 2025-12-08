<?php
require_once __DIR__ . '/config.php';

function base64url_encode(string $data): string {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

function base64url_decode(string $data): string {
    $remainder = strlen($data) % 4;
    if ($remainder) {
        $padlen = 4 - $remainder;
        $data .= str_repeat('=', $padlen);
    }
    return base64_decode(strtr($data, '-_', '+/'));
}

function create_token(array $payload): string {
    $header = ['alg' => 'HS256', 'typ' => 'JWT'];
    $payload['iat'] = $payload['iat'] ?? time();
    $payload['exp'] = $payload['exp'] ?? ($payload['iat'] + (TOKEN_EXPIRY_DAYS * 86400));
    $segments = [
        base64url_encode(json_encode($header)),
        base64url_encode(json_encode($payload)),
    ];
    $signingInput = implode('.', $segments);
    $signature = hash_hmac('sha256', $signingInput, JWT_SECRET, true);
    $segments[] = base64url_encode($signature);
    return implode('.', $segments);
}

function verify_token(string $token): ?array {
    $parts = explode('.', $token);
    if (count($parts) !== 3) {
        return null;
    }
    [$header64, $payload64, $sig64] = $parts;
    $signingInput = $header64 . '.' . $payload64;
    $expected = base64url_encode(hash_hmac('sha256', $signingInput, JWT_SECRET, true));
    if (!hash_equals($expected, $sig64)) {
        return null;
    }
    $payload = json_decode(base64url_decode($payload64), true);
    if (!is_array($payload)) {
        return null;
    }
    if (isset($payload['exp']) && time() > $payload['exp']) {
        return null;
    }
    return $payload;
}
