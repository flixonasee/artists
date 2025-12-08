<?php
require_once __DIR__ . '/../lib/db.php';
require_once __DIR__ . '/../lib/util.php';
require_once __DIR__ . '/../lib/response.php';
require_once __DIR__ . '/../lib/wikipedia.php';
require_once __DIR__ . '/../lib/gamification.php';

function map_artist_row($row) {
    return [
        'id' => (int)$row['id'],
        'name' => $row['name'],
        'birth_year' => $row['birth_year'] !== null ? (int)$row['birth_year'] : null,
        'bio' => $row['bio'],
        'featured_image_url' => $row['featured_image_url'],
        'tags' => $row['tags_json'] ? json_decode($row['tags_json'], true) : [],
        'links' => $row['links_json'] ? json_decode($row['links_json'], true) : [],
        'photos_count' => isset($row['photos_count']) ? (int)$row['photos_count'] : 0,
        'works_count' => isset($row['works_count']) ? (int)$row['works_count'] : 0,
    ];
}

function handle_get_artists(array $auth): void {
    $db = get_db();
    $q = trim((string)query_param('q', ''));
    if ($q !== '') {
        $stmt = $db->prepare('SELECT a.*, 
            (SELECT COUNT(*) FROM photos p WHERE p.artist_id = a.id) AS photos_count,
            (SELECT COUNT(*) FROM works w WHERE w.artist_id = a.id) AS works_count
            FROM artists a WHERE a.name LIKE ? OR a.tags_json LIKE ? ORDER BY a.created_at DESC');
        $like = '%' . $q . '%';
        $stmt->execute([$like, $like]);
    } else {
        $stmt = $db->query('SELECT a.*, 
            (SELECT COUNT(*) FROM photos p WHERE p.artist_id = a.id) AS photos_count,
            (SELECT COUNT(*) FROM works w WHERE w.artist_id = a.id) AS works_count
            FROM artists a ORDER BY a.created_at DESC');
    }
    $artists = array_map('map_artist_row', $stmt->fetchAll());
    json_response(['artists' => $artists]);
}

function handle_post_artists(array $auth): void {
    $db = get_db();
    $body = get_json_body();
    $name = trim($body['name'] ?? '');
    if ($name === '') {
        error_response('Name is required', 400);
    }
    $tags = sanitize_tags($body['tags'] ?? []);
    $links = sanitize_links($body['links'] ?? []);
    $bio = $body['bio'] ?? null;
    $birthYear = $body['birth_year'] ?? null;
    $featured = $body['featured_image_url'] ?? null;

    if (!empty($body['autoFetchWikipedia'])) {
        $wiki = fetch_artist_from_wikipedia($name);
        if ($wiki) {
            if (!$bio && !empty($wiki['bio'])) $bio = $wiki['bio'];
            if (!$birthYear && !empty($wiki['birth_year'])) $birthYear = $wiki['birth_year'];
            if (!$featured && !empty($wiki['image_url'])) $featured = $wiki['image_url'];
            if (!empty($wiki['wikipedia_url'])) $links[] = $wiki['wikipedia_url'];
        }
    }

    $stmt = $db->prepare('INSERT INTO artists (name, birth_year, bio, featured_image_url, tags_json, links_json, created_by, updated_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
    $stmt->execute([
        $name,
        $birthYear,
        $bio,
        $featured,
        json_encode($tags),
        json_encode($links),
        (int)$auth['sub'],
        (int)$auth['sub'],
    ]);
    $id = (int)$db->lastInsertId();

    award_points((int)$auth['sub'], 'CREATE_ARTIST', $id);

    $artist = $db->prepare('SELECT * FROM artists WHERE id = ?');
    $artist->execute([$id]);
    json_response(map_artist_row($artist->fetch()));
}

function handle_patch_artist(array $auth, int $id): void {
    $db = get_db();
    $body = get_json_body();
    $fields = [];
    $params = [];
    $allowed = ['name', 'birth_year', 'bio', 'featured_image_url'];
    foreach ($allowed as $field) {
        if (array_key_exists($field, $body)) {
            $fields[] = "$field = ?";
            $params[] = $body[$field];
        }
    }
    if (array_key_exists('tags', $body)) {
        $fields[] = 'tags_json = ?';
        $params[] = json_encode(sanitize_tags($body['tags']));
    }
    if (array_key_exists('links', $body)) {
        $fields[] = 'links_json = ?';
        $params[] = json_encode(sanitize_links($body['links']));
    }
    if (empty($fields)) {
        error_response('No fields to update', 400);
    }
    $fields[] = 'updated_at = CURRENT_TIMESTAMP';
    $fields[] = 'updated_by = ?';
    $params[] = (int)$auth['sub'];
    $params[] = $id;
    $sql = 'UPDATE artists SET ' . implode(', ', $fields) . ' WHERE id = ?';
    $stmt = $db->prepare($sql);
    $stmt->execute($params);

    $get = $db->prepare('SELECT a.*, 
        (SELECT COUNT(*) FROM photos p WHERE p.artist_id = a.id) AS photos_count,
        (SELECT COUNT(*) FROM works w WHERE w.artist_id = a.id) AS works_count
        FROM artists a WHERE a.id = ?');
    $get->execute([$id]);
    $artist = $get->fetch();
    if (!$artist) {
        error_response('Not found', 404);
    }
    json_response(map_artist_row($artist));
}

function handle_delete_artist(array $auth, int $id): void {
    $db = get_db();
    $stmt = $db->prepare('DELETE FROM artists WHERE id = ?');
    $stmt->execute([$id]);
    json_response(['success' => true]);
}
