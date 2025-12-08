<?php
require_once __DIR__ . '/config.php';

function get_db(): PDO {
    static $db = null;
    if ($db === null) {
        $needInit = !file_exists(DB_PATH);
        $db = new PDO('sqlite:' . DB_PATH);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        if ($needInit) {
            init_db_if_needed($db);
        }
    }
    return $db;
}

function init_db_if_needed(PDO $db): void {
    $schemaPath = __DIR__ . '/../schema.sql';
    $schemaSql = file_get_contents($schemaPath);
    $db->exec($schemaSql);

    // seed users if empty
    $countStmt = $db->query('SELECT COUNT(*) as cnt FROM users');
    $count = (int)($countStmt->fetch()['cnt'] ?? 0);
    if ($count === 0) {
        $users = [
            ['gianmaria@example.com', 'password123', 'Gianmaria'],
            ['giulio@example.com', 'password123', 'Giulio'],
        ];
        $stmt = $db->prepare('INSERT INTO users (email, password_hash, name) VALUES (?, ?, ?)');
        foreach ($users as $u) {
            $stmt->execute([$u[0], password_hash($u[1], PASSWORD_DEFAULT), $u[2]]);
        }
    }

    // seed badges
    $badgeCount = (int)($db->query('SELECT COUNT(*) as cnt FROM badges')->fetch()['cnt'] ?? 0);
    if ($badgeCount === 0) {
        $badges = [
            ['FIRST_ARTIST', 'First Artist', 'Created the first artist'],
            ['TEN_ARTISTS', 'Ten Artists', 'Created ten artists'],
            ['FIFTY_PHOTOS', 'Fifty Photos', 'Uploaded fifty photos'],
            ['FIVE_WORKS', 'Five Works', 'Added five works'],
        ];
        $stmt = $db->prepare('INSERT INTO badges (code, title, description) VALUES (?, ?, ?)');
        foreach ($badges as $b) {
            $stmt->execute($b);
        }
    }
}
