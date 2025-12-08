<?php
require_once __DIR__ . '/../lib/db.php';
require_once __DIR__ . '/../lib/util.php';
require_once __DIR__ . '/../lib/response.php';
require_once __DIR__ . '/../lib/gamification.php';
require_once __DIR__ . '/../lib/config.php';

function handle_post_artist_works(array $auth, int $artistId): void {
    $body = get_json_body();
    $title = trim($body['title'] ?? '');
    if ($title === '') {
        error_response('Title is required', 400);
    }
    $year = $body['year'] ?? null;
    $notes = $body['notes'] ?? null;
    $imagePath = $body['image_path'] ?? null;

    $db = get_db();
    $stmt = $db->prepare('INSERT INTO works (artist_id, title, year, image_path, notes, created_by) VALUES (?, ?, ?, ?, ?, ?)');
    $stmt->execute([$artistId, $title, $year, $imagePath, $notes, (int)$auth['sub']]);
    $id = (int)$db->lastInsertId();

    award_points((int)$auth['sub'], 'ADD_WORK', $artistId);

    json_response([
        'id' => $id,
        'artist_id' => $artistId,
        'title' => $title,
        'year' => $year,
        'image_path' => $imagePath,
        'notes' => $notes,
    ], 201);
}
