<?php
require_once __DIR__ . '/jwt.php';
require_once __DIR__ . '/response.php';
require_once __DIR__ . '/db.php';

if (!function_exists('getallheaders')) {
    function getallheaders() {
        $headers = [];
        foreach ($_SERVER as $name => $value) {
            if (str_starts_with($name, 'HTTP_')) {
                $key = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))));
                $headers[$key] = $value;
            }
        }
        return $headers;
    }
}

function get_json_body(): array {
    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}

function require_auth(): array {
    $headers = getallheaders();
    $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';
    if (str_starts_with($authHeader, 'Bearer ')) {
        $token = substr($authHeader, 7);
        $payload = verify_token($token);
        if ($payload) {
            return $payload;
        }
    }
    error_response('Unauthorized', 401);
}

function cors_preflight(): void {
    header('Access-Control-Allow-Origin: ' . FRONTEND_ORIGIN);
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
    header('Access-Control-Allow-Methods: GET, POST, PATCH, DELETE, OPTIONS');
    header('Access-Control-Allow-Credentials: true');
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        exit;
    }
}

function query_param(string $key, $default = null) {
    return $_GET[$key] ?? $default;
}

function sanitize_tags($input): array {
    return array_values(array_filter(array_map('trim', is_array($input) ? $input : [])));
}

function sanitize_links($input): array {
    return array_values(array_filter(array_map('trim', is_array($input) ? $input : [])));
}
