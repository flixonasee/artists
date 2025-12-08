<?php
require_once __DIR__ . '/../lib/db.php';
require_once __DIR__ . '/../lib/util.php';
require_once __DIR__ . '/../lib/response.php';
require_once __DIR__ . '/../lib/jwt.php';

function handle_login(): void {
    $body = get_json_body();
    $email = strtolower(trim($body['email'] ?? ''));
    $password = $body['password'] ?? '';
    if (!$email || !$password) {
        error_response('Invalid credentials', 401);
    }
    $db = get_db();
    $stmt = $db->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    if (!$user || !password_verify($password, $user['password_hash'])) {
        error_response('Invalid credentials', 401);
    }
    $token = create_token([
        'sub' => (int)$user['id'],
        'email' => $user['email'],
        'name' => $user['name'],
    ]);
    json_response([
        'token' => $token,
        'user' => [
            'id' => (int)$user['id'],
            'email' => $user['email'],
            'name' => $user['name'],
        ],
    ]);
}

function handle_me(array $auth): void {
    json_response([
        'user' => [
            'id' => (int)$auth['sub'],
            'email' => $auth['email'] ?? null,
            'name' => $auth['name'] ?? null,
        ],
    ]);
}
