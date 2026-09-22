<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
header('Content-Type: application/json; charset=utf-8');

$in = json_decode(file_get_contents('php://input'), true);
if (!$in) { echo json_encode(['success' => false, 'message' => 'Invalid request']); exit; }

$db = getDB();

if ($in['action'] === 'login') {
    $u = trim($in['username'] ?? '');
    $p = $in['password'] ?? '';
    if (!$u || !$p) {
        echo json_encode(['success' => false, 'message' => 'กรอกข้อมูลให้ครบ']);
        exit;
    }
    $st = $db->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
    $st->execute([$u, $u]);
    $user = $st->fetch();
    if (!$user || !password_verify($p, $user['password_hash'])) {
        echo json_encode(['success' => false, 'message' => 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง']);
        exit;
    }
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    if (!empty($in['remember'])) {
        setcookie('bluezy_auto', bin2hex(random_bytes(32)), time() + 30 * 86400, '/', '', true, true);
    }
    echo json_encode(['success' => true]);

} elseif ($in['action'] === 'register') {
    $u = trim($in['username'] ?? '');
    $e = trim($in['email'] ?? '');
    $p = $in['password'] ?? '';
    if (strlen($u) < 3) {
        echo json_encode(['success' => false, 'message' => 'ชื่อผู้ใช้สั้นเกินไป']);
        exit;
    }
    if (!filter_var($e, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'อีเมลไม่ถูกต้อง']);
        exit;
    }
    if (strlen($p) < 6) {
        echo json_encode(['success' => false, 'message' => 'รหัสผ่านต้องอย่างน้อย 6 ตัวอักษร']);
        exit;
    }
    $st = $db->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $st->execute([$u, $e]);
    if ($st->fetch()) {
        echo json_encode(['success' => false, 'message' => 'ชื่อผู้ใช้หรืออีเมลนี้ถูกใช้แล้ว']);
        exit;
    }
    $db->prepare("INSERT INTO users (username, email, password_hash, points) VALUES (?, ?, ?, 20)")
       ->execute([$u, $e, password_hash($p, PASSWORD_BCRYPT)]);
    $_SESSION['user_id'] = $db->lastInsertId();
    $_SESSION['username'] = $u;
    echo json_encode(['success' => true]);

} else {
    echo json_encode(['success' => false, 'message' => 'Unknown action']);
}
