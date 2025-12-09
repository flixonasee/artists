<?php
require_once __DIR__ . '/db.php';

// Simple bootstrap script to create an admin user.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    if (!$name || !$email || strlen($password) < 6) {
        die('Invalid input.');
    }
    $pdo = get_db();
    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        die('User already exists.');
    }
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare('INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, "admin")');
    $stmt->execute([$name, $email, $hash]);
    echo 'Admin user created.';
    exit;
}
?>
<!DOCTYPE html>
<html>
<head><title>Create Admin</title></head>
<body>
    <h1>Create Admin User</h1>
    <form method="post">
        <div><label>Name: <input type="text" name="name" required></label></div>
        <div><label>Email: <input type="email" name="email" required></label></div>
        <div><label>Password: <input type="password" name="password" required></label></div>
        <button type="submit">Create</button>
    </form>
</body>
</html>
