<?php
require_once __DIR__ . '/../lib/db.php';
require_once __DIR__ . '/../lib/util.php';
require_once __DIR__ . '/../lib/response.php';
require_once __DIR__ . '/../lib/gamification.php';
require_once __DIR__ . '/../lib/config.php';

function handle_post_artist_photos(array $auth, int $artistId): void {
    if (!isset($_FILES['file'])) {
        error_response('File is required', 400);
    }
    $file = $_FILES['file'];
    if ($file['error'] !== UPLOAD_ERR_OK) {
        error_response('Upload error', 400);
    }
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $basename = uniqid('photo_', true) . ($ext ? '.' . $ext : '');
    $target = rtrim(UPLOAD_DIR, '/') . '/' . $basename;
    if (!is_dir(UPLOAD_DIR)) {
        mkdir(UPLOAD_DIR, 0755, true);
    }
    if (!move_uploaded_file($file['tmp_name'], $target)) {
        error_response('Failed to store file', 500);
    }
    $caption = $_POST['caption'] ?? null;
    $db = get_db();
    $stmt = $db->prepare('INSERT INTO photos (artist_id, file_path, caption, created_by) VALUES (?, ?, ?, ?)');
    $stmt->execute([$artistId, '/api/uploads/' . $basename, $caption, (int)$auth['sub']]);
    $id = (int)$db->lastInsertId();

    award_points((int)$auth['sub'], 'ADD_PHOTO', $artistId);

    json_response([
        'id' => $id,
        'artist_id' => $artistId,
        'file_path' => '/api/uploads/' . $basename,
        'caption' => $caption,
    ], 201);
}
