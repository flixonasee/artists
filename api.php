<?php
session_start();
require_once __DIR__ . '/db.php';

header('Content-Type: application/json');

$action = $_GET['action'] ?? $_POST['action'] ?? '';

function respond($data, int $code = 200): void {
    http_response_code($code);
    echo json_encode($data);
    exit;
}

function current_user(): ?array {
    if (!isset($_SESSION['user_id'])) {
        return null;
    }
    $pdo = get_db();
    $stmt = $pdo->prepare('SELECT id, name, email, role, created_at FROM users WHERE id = ?');
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    return $user ?: null;
}

function require_login(): array {
    $user = current_user();
    if (!$user) {
        respond(['error' => 'Not authenticated'], 401);
    }
    return $user;
}

function ensure_user_stats(int $user_id): array {
    $pdo = get_db();
    $stmt = $pdo->prepare('SELECT * FROM user_stats WHERE user_id = ?');
    $stmt->execute([$user_id]);
    $row = $stmt->fetch();
    if (!$row) {
        $stmt = $pdo->prepare('INSERT INTO user_stats (user_id, points, level, badges_json) VALUES (?, 0, "Novice Archivist", "[]")');
        $stmt->execute([$user_id]);
        $stmt = $pdo->prepare('SELECT * FROM user_stats WHERE user_id = ?');
        $stmt->execute([$user_id]);
        $row = $stmt->fetch();
    }
    if (!$row['badges_json']) {
        $row['badges_json'] = '[]';
    }
    return $row;
}

function level_for_points(int $points): string {
    if ($points >= 5000) return 'Collector Whisperer';
    if ($points >= 2000) return 'Archivist-in-Chief';
    if ($points >= 1000) return 'Curator';
    if ($points >= 500) return 'Connoisseur';
    if ($points >= 200) return 'Researcher';
    return 'Novice Archivist';
}

function badges_for_points(int $points): array {
    $badges = [];
    if ($points >= 200) $badges[] = 'early_archivist';
    if ($points >= 500) $badges[] = 'researcher';
    if ($points >= 1000) $badges[] = 'archivio_vivo';
    return $badges;
}

function add_points(int $user_id, int $points): array {
    $stats = ensure_user_stats($user_id);
    $newPoints = max(0, (int)$stats['points'] + $points);
    $level = level_for_points($newPoints);
    $badges = badges_for_points($newPoints);
    $pdo = get_db();
    $stmt = $pdo->prepare('UPDATE user_stats SET points = ?, level = ?, badges_json = ?, updated_at = NOW() WHERE user_id = ?');
    $stmt->execute([$newPoints, $level, json_encode($badges), $user_id]);
    return ['points' => $newPoints, 'level' => $level, 'badges' => $badges];
}

function validate_id($value): int {
    if (!isset($value) || !ctype_digit(strval($value))) {
        respond(['error' => 'Invalid ID'], 400);
    }
    return (int)$value;
}

function handle_register(): void {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    if (!$name || !$email || !$password || strlen($password) < 6) {
        respond(['error' => 'Invalid input'], 400);
    }
    $pdo = get_db();
    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        respond(['error' => 'Email already registered'], 409);
    }
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare('INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, "user")');
    $stmt->execute([$name, $email, $hash]);
    $user_id = (int)$pdo->lastInsertId();
    ensure_user_stats($user_id);
    $_SESSION['user_id'] = $user_id;
    respond(['success' => true, 'user' => current_user()]);
}

function handle_login(): void {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    if (!$email || !$password) {
        respond(['error' => 'Invalid input'], 400);
    }
    $pdo = get_db();
    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    if (!$user || !password_verify($password, $user['password_hash'])) {
        respond(['error' => 'Invalid credentials'], 401);
    }
    $_SESSION['user_id'] = (int)$user['id'];
    ensure_user_stats((int)$user['id']);
    respond(['success' => true, 'user' => current_user()]);
}

function handle_logout(): void {
    session_destroy();
    respond(['success' => true]);
}

function handle_list_artists(array $user): void {
    $q = trim($_GET['q'] ?? '');
    $pdo = get_db();
    if ($user['role'] === 'admin') {
        if ($q) {
            $stmt = $pdo->prepare('SELECT * FROM artists WHERE name LIKE ? ORDER BY updated_at DESC');
            $stmt->execute(['%' . $q . '%']);
        } else {
            $stmt = $pdo->query('SELECT * FROM artists ORDER BY updated_at DESC');
        }
    } else {
        if ($q) {
            $stmt = $pdo->prepare('SELECT * FROM artists WHERE owner_id = ? AND name LIKE ? ORDER BY updated_at DESC');
            $stmt->execute([$user['id'], '%' . $q . '%']);
        } else {
            $stmt = $pdo->prepare('SELECT * FROM artists WHERE owner_id = ? ORDER BY updated_at DESC');
            $stmt->execute([$user['id']]);
        }
    }
    $artists = $stmt->fetchAll();
    respond(['artists' => $artists]);
}

function owner_check_artist(int $artist_id, array $user): array {
    $pdo = get_db();
    $stmt = $pdo->prepare('SELECT * FROM artists WHERE id = ?');
    $stmt->execute([$artist_id]);
    $artist = $stmt->fetch();
    if (!$artist) {
        respond(['error' => 'Artist not found'], 404);
    }
    if ($user['role'] !== 'admin' && (int)$artist['owner_id'] !== (int)$user['id']) {
        respond(['error' => 'Forbidden'], 403);
    }
    return $artist;
}

function handle_get_artist(array $user): void {
    $id = validate_id($_GET['id'] ?? null);
    $artist = owner_check_artist($id, $user);
    respond(['artist' => $artist]);
}

function handle_save_artist(array $user): void {
    $pdo = get_db();
    $id = isset($_POST['id']) && $_POST['id'] !== '' ? validate_id($_POST['id']) : null;
    $name = trim($_POST['name'] ?? '');
    $nationality = trim($_POST['nationality'] ?? '');
    $birth_year = $_POST['birth_year'] !== '' ? (int)$_POST['birth_year'] : null;
    $death_year = $_POST['death_year'] !== '' ? (int)$_POST['death_year'] : null;
    $tag1 = trim($_POST['tag1'] ?? '');
    $tag2 = trim($_POST['tag2'] ?? '');
    $notes = trim($_POST['notes'] ?? '');
    $image_path = trim($_POST['image_path'] ?? '');
    if (!$name) {
        respond(['error' => 'Name required'], 400);
    }
    if ($id) {
        owner_check_artist($id, $user);
        $stmt = $pdo->prepare('UPDATE artists SET name=?, nationality=?, birth_year=?, death_year=?, tag1=?, tag2=?, notes=?, image_path=?, updated_at=NOW() WHERE id=?');
        $stmt->execute([$name, $nationality ?: null, $birth_year, $death_year, $tag1 ?: null, $tag2 ?: null, $notes, $image_path ?: null, $id]);
        $points = add_points((int)$user['id'], 3);
    } else {
        $stmt = $pdo->prepare('INSERT INTO artists (owner_id, name, nationality, birth_year, death_year, tag1, tag2, notes, image_path, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())');
        $stmt->execute([$user['id'], $name, $nationality ?: null, $birth_year, $death_year, $tag1 ?: null, $tag2 ?: null, $notes, $image_path ?: null]);
        $id = (int)$pdo->lastInsertId();
        $points = add_points((int)$user['id'], 10);
    }
    $artist = owner_check_artist($id, $user);
    respond(['artist' => $artist, 'gamification' => $points]);
}

function handle_delete_artist(array $user): void {
    $id = validate_id($_POST['id'] ?? null);
    owner_check_artist($id, $user);
    $pdo = get_db();
    $stmt = $pdo->prepare('DELETE FROM artists WHERE id = ?');
    $stmt->execute([$id]);
    respond(['success' => true]);
}

function handle_list_artworks(array $user): void {
    $artist_id = validate_id($_GET['artist_id'] ?? null);
    owner_check_artist($artist_id, $user);
    $pdo = get_db();
    $stmt = $pdo->prepare('SELECT * FROM artworks WHERE artist_id = ? ORDER BY updated_at DESC');
    $stmt->execute([$artist_id]);
    $artworks = $stmt->fetchAll();
    respond(['artworks' => $artworks]);
}

function owner_check_artwork(int $artwork_id, array $user): array {
    $pdo = get_db();
    $stmt = $pdo->prepare('SELECT a.*, ar.owner_id FROM artworks a JOIN artists ar ON a.artist_id = ar.id WHERE a.id = ?');
    $stmt->execute([$artwork_id]);
    $artwork = $stmt->fetch();
    if (!$artwork) {
        respond(['error' => 'Artwork not found'], 404);
    }
    if ($user['role'] !== 'admin' && (int)$artwork['owner_id'] !== (int)$user['id']) {
        respond(['error' => 'Forbidden'], 403);
    }
    return $artwork;
}

function handle_save_artwork(array $user): void {
    $pdo = get_db();
    $id = isset($_POST['id']) && $_POST['id'] !== '' ? validate_id($_POST['id']) : null;
    $artist_id = validate_id($_POST['artist_id'] ?? null);
    owner_check_artist($artist_id, $user);
    $title = trim($_POST['title'] ?? '');
    $year_text = trim($_POST['year_text'] ?? '');
    $medium = trim($_POST['medium'] ?? '');
    $notes = trim($_POST['notes'] ?? '');
    $image_path = trim($_POST['image_path'] ?? '');
    if (!$title) {
        respond(['error' => 'Title required'], 400);
    }
    if ($id) {
        owner_check_artwork($id, $user);
        $stmt = $pdo->prepare('UPDATE artworks SET title=?, year_text=?, medium=?, notes=?, image_path=?, updated_at=NOW() WHERE id=?');
        $stmt->execute([$title, $year_text ?: null, $medium ?: null, $notes, $image_path ?: null, $id]);
        $points = add_points((int)$user['id'], 2);
    } else {
        $stmt = $pdo->prepare('INSERT INTO artworks (artist_id, title, year_text, medium, notes, image_path, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())');
        $stmt->execute([$artist_id, $title, $year_text ?: null, $medium ?: null, $notes, $image_path ?: null]);
        $id = (int)$pdo->lastInsertId();
        $points = add_points((int)$user['id'], 6);
    }
    $stmt = $pdo->prepare('SELECT * FROM artworks WHERE id = ?');
    $stmt->execute([$id]);
    $artwork = $stmt->fetch();
    respond(['artwork' => $artwork, 'gamification' => $points]);
}

function handle_delete_artwork(array $user): void {
    $id = validate_id($_POST['id'] ?? null);
    owner_check_artwork($id, $user);
    $pdo = get_db();
    $stmt = $pdo->prepare('DELETE FROM artworks WHERE id = ?');
    $stmt->execute([$id]);
    respond(['success' => true]);
}

function resize_image($sourcePath, $targetPath, $mime): bool {
    [$width, $height] = getimagesize($sourcePath);
    $maxSize = 2000;
    $ratio = min($maxSize / $width, $maxSize / $height, 1);
    $newW = (int)($width * $ratio);
    $newH = (int)($height * $ratio);

    switch ($mime) {
        case 'image/jpeg':
            $src = imagecreatefromjpeg($sourcePath);
            break;
        case 'image/png':
            $src = imagecreatefrompng($sourcePath);
            break;
        case 'image/webp':
            if (!function_exists('imagecreatefromwebp')) return false;
            $src = imagecreatefromwebp($sourcePath);
            break;
        default:
            return false;
    }
    if (!$src) return false;

    $dst = imagecreatetruecolor($newW, $newH);
    imagealphablending($dst, false);
    imagesavealpha($dst, true);
    imagecopyresampled($dst, $src, 0, 0, 0, 0, $newW, $newH, $width, $height);

    $result = false;
    if ($mime === 'image/jpeg') {
        $result = imagejpeg($dst, $targetPath, 75);
    } elseif ($mime === 'image/png') {
        $result = imagepng($dst, $targetPath, 6);
    } elseif ($mime === 'image/webp') {
        $result = imagewebp($dst, $targetPath, 75);
    }
    imagedestroy($src);
    imagedestroy($dst);
    return $result;
}

function handle_upload_image(array $user): void {
    if (!isset($_FILES['file'])) {
        respond(['error' => 'No file'], 400);
    }
    $target = $_POST['target'] ?? 'artists';
    if (!in_array($target, ['artists', 'artworks'], true)) {
        respond(['error' => 'Invalid target'], 400);
    }
    $file = $_FILES['file'];
    if ($file['error'] !== UPLOAD_ERR_OK) {
        respond(['error' => 'Upload error'], 400);
    }
    if ($file['size'] > 10 * 1024 * 1024) {
        respond(['error' => 'File too large'], 400);
    }
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    if (!isset($allowed[$mime])) {
        respond(['error' => 'Invalid file type'], 400);
    }
    $ext = $allowed[$mime];
    $safeName = bin2hex(random_bytes(8)) . '.' . $ext;
    $folder = __DIR__ . '/uploads/' . $target;
    if (!is_dir($folder)) {
        mkdir($folder, 0755, true);
    }
    $dest = $folder . '/' . $safeName;
    if (!move_uploaded_file($file['tmp_name'], $dest)) {
        respond(['error' => 'Failed to save'], 500);
    }
    resize_image($dest, $dest, $mime);
    $publicPath = 'uploads/' . $target . '/' . $safeName;
    respond(['path' => $publicPath]);
}

function handle_export(array $user): void {
    if ($user['role'] !== 'admin') {
        respond(['error' => 'Forbidden'], 403);
    }
    $pdo = get_db();
    $data = [];
    foreach (['users', 'artists', 'artworks', 'user_stats'] as $table) {
        $stmt = $pdo->query("SELECT * FROM {$table}");
        $data[$table] = $stmt->fetchAll();
    }
    header('Content-Type: application/json');
    header('Content-Disposition: attachment; filename="artist_notebook_export.json"');
    echo json_encode($data);
    exit;
}

function handle_get_stats(array $user): void {
    $stats = ensure_user_stats((int)$user['id']);
    $badges = $stats['badges_json'] ? json_decode($stats['badges_json'], true) : [];
    respond([
        'points' => (int)$stats['points'],
        'level' => $stats['level'],
        'badges' => $badges,
    ]);
}

function handle_add_points(array $user): void {
    $event = $_POST['event'] ?? '';
    $map = [
        'create_artist' => 10,
        'update_artist' => 3,
        'create_artwork' => 6,
        'update_artwork' => 2,
    ];
    if (!isset($map[$event])) {
        respond(['error' => 'Unknown event'], 400);
    }
    $result = add_points((int)$user['id'], $map[$event]);
    respond(['gamification' => $result]);
}

switch ($action) {
    case 'register':
        handle_register();
        break;
    case 'login':
        handle_login();
        break;
    case 'logout':
        handle_logout();
        break;
    case 'current_user':
        respond(['user' => current_user()]);
        break;
    default:
        $user = require_login();
        switch ($action) {
            case 'list_artists':
                handle_list_artists($user);
                break;
            case 'get_artist':
                handle_get_artist($user);
                break;
            case 'save_artist':
                handle_save_artist($user);
                break;
            case 'delete_artist':
                handle_delete_artist($user);
                break;
            case 'list_artworks':
                handle_list_artworks($user);
                break;
            case 'save_artwork':
                handle_save_artwork($user);
                break;
            case 'delete_artwork':
                handle_delete_artwork($user);
                break;
            case 'upload_image':
                handle_upload_image($user);
                break;
            case 'export_json':
                handle_export($user);
                break;
            case 'get_stats':
                handle_get_stats($user);
                break;
            case 'add_points':
                handle_add_points($user);
                break;
            default:
                respond(['error' => 'Unknown action'], 400);
        }
}
?>
