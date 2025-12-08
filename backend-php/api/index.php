<?php
require_once __DIR__ . '/../lib/util.php';
require_once __DIR__ . '/../lib/response.php';
require_once __DIR__ . '/../lib/db.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/artists.php';
require_once __DIR__ . '/photos.php';
require_once __DIR__ . '/works.php';
require_once __DIR__ . '/dashboard.php';
require_once __DIR__ . '/../lib/config.php';

cors_preflight();
get_db(); // ensure db exists

$method = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if (str_starts_with($uri, '/api/uploads/')) {
    $filePath = __DIR__ . '/../uploads/' . basename($uri);
    if (file_exists($filePath)) {
        $mime = mime_content_type($filePath) ?: 'application/octet-stream';
        header('Content-Type: ' . $mime);
        readfile($filePath);
        exit;
    }
    error_response('Not found', 404);
}

switch (true) {
    case $method === 'POST' && $uri === '/api/login':
        handle_login();
        break;
    case $method === 'GET' && $uri === '/api/artists':
        $auth = require_auth();
        handle_get_artists($auth);
        break;
    case $method === 'POST' && $uri === '/api/artists':
        $auth = require_auth();
        handle_post_artists($auth);
        break;
    case $method === 'PATCH' && preg_match('#^/api/artists/(\d+)$#', $uri, $m):
        $auth = require_auth();
        handle_patch_artist($auth, (int)$m[1]);
        break;
    case $method === 'DELETE' && preg_match('#^/api/artists/(\d+)$#', $uri, $m):
        $auth = require_auth();
        handle_delete_artist($auth, (int)$m[1]);
        break;
    case $method === 'POST' && preg_match('#^/api/artists/(\d+)/photos$#', $uri, $m):
        $auth = require_auth();
        handle_post_artist_photos($auth, (int)$m[1]);
        break;
    case $method === 'POST' && preg_match('#^/api/artists/(\d+)/works$#', $uri, $m):
        $auth = require_auth();
        handle_post_artist_works($auth, (int)$m[1]);
        break;
    case $method === 'GET' && $uri === '/api/dashboard':
        $auth = require_auth();
        handle_get_dashboard($auth);
        break;
    case $method === 'GET' && $uri === '/api/me':
        $auth = require_auth();
        handle_me($auth);
        break;
    default:
        error_response('Not found', 404);
}
