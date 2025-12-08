<?php
require_once __DIR__ . '/../lib/db.php';
require_once __DIR__ . '/../lib/response.php';
require_once __DIR__ . '/../lib/gamification.php';

function handle_get_dashboard(array $auth): void {
    $db = get_db();
    $totals = [
        'artists' => (int)$db->query('SELECT COUNT(*) as c FROM artists')->fetch()['c'],
        'photos' => (int)$db->query('SELECT COUNT(*) as c FROM photos')->fetch()['c'],
        'works' => (int)$db->query('SELECT COUNT(*) as c FROM works')->fetch()['c'],
    ];

    $usersStmt = $db->query('SELECT id, name, email FROM users');
    $users = [];
    while ($row = $usersStmt->fetch()) {
        $users[] = [
            'id' => (int)$row['id'],
            'name' => $row['name'],
            'email' => $row['email'],
            'points' => get_user_points((int)$row['id']),
            'badges' => get_user_badges((int)$row['id']),
        ];
    }

    $activity = $db->query('SELECT pl.*, u.name as user_name, a.name as artist_name FROM points_log pl LEFT JOIN users u ON u.id = pl.user_id LEFT JOIN artists a ON a.id = pl.artist_id ORDER BY pl.created_at DESC LIMIT 10')->fetchAll();

    json_response([
        'totals' => $totals,
        'users' => $users,
        'activity' => $activity,
    ]);
}
