<?php
require_once __DIR__ . '/db.php';

const ACTION_POINTS = [
    'CREATE_ARTIST' => 10,
    'ADD_PHOTO' => 5,
    'ADD_WORK' => 7,
];

function award_points(int $userId, string $action, ?int $artistId = null): void {
    $db = get_db();
    $points = ACTION_POINTS[$action] ?? 0;
    if ($points <= 0) {
        return;
    }
    $stmt = $db->prepare('INSERT INTO points_log (user_id, artist_id, action, points) VALUES (?, ?, ?, ?)');
    $stmt->execute([$userId, $artistId, $action, $points]);
    evaluate_badges($userId);
}

function evaluate_badges(int $userId): void {
    $db = get_db();

    $artistCount = (int)$db->query('SELECT COUNT(*) as c FROM artists WHERE created_by = ' . (int)$userId)->fetch()['c'];
    $photoCount = (int)$db->query('SELECT COUNT(*) as c FROM photos WHERE created_by = ' . (int)$userId)->fetch()['c'];
    $workCount = (int)$db->query('SELECT COUNT(*) as c FROM works WHERE created_by = ' . (int)$userId)->fetch()['c'];

    $badgeCodes = [];
    if ($artistCount >= 1) { $badgeCodes[] = 'FIRST_ARTIST'; }
    if ($artistCount >= 10) { $badgeCodes[] = 'TEN_ARTISTS'; }
    if ($photoCount >= 50) { $badgeCodes[] = 'FIFTY_PHOTOS'; }
    if ($workCount >= 5) { $badgeCodes[] = 'FIVE_WORKS'; }

    foreach ($badgeCodes as $code) {
        $badge = $db->prepare('SELECT id FROM badges WHERE code = ?');
        $badge->execute([$code]);
        $badgeRow = $badge->fetch();
        if (!$badgeRow) continue;
        $badgeId = (int)$badgeRow['id'];
        $exists = $db->prepare('SELECT 1 FROM user_badges WHERE user_id = ? AND badge_id = ?');
        $exists->execute([$userId, $badgeId]);
        if (!$exists->fetch()) {
            $insert = $db->prepare('INSERT INTO user_badges (user_id, badge_id) VALUES (?, ?)');
            $insert->execute([$userId, $badgeId]);
        }
    }
}

function get_user_points(int $userId): int {
    $db = get_db();
    $stmt = $db->prepare('SELECT COALESCE(SUM(points),0) as total FROM points_log WHERE user_id = ?');
    $stmt->execute([$userId]);
    $row = $stmt->fetch();
    return (int)($row['total'] ?? 0);
}

function get_user_badges(int $userId): array {
    $db = get_db();
    $stmt = $db->prepare('SELECT b.id, b.code, b.title, b.description, ub.unlocked_at FROM user_badges ub JOIN badges b ON b.id = ub.badge_id WHERE ub.user_id = ?');
    $stmt->execute([$userId]);
    return $stmt->fetchAll();
}
