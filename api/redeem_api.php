<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
header('Content-Type: application/json; charset=utf-8');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'กรุณาเข้าสู่ระบบ']);
    exit;
}

$in = json_decode(file_get_contents('php://input'), true);
$voucher = trim($in['voucher'] ?? '');
$pts = intval($in['points'] ?? 0);

if (!$voucher || !str_contains($voucher, 'gift.truemoney.com')) {
    echo json_encode(['success' => false, 'message' => 'ลิงก์ซองไม่ถูกต้อง']);
    exit;
}

$valid = [50 => 29, 100 => 50, 300 => 159, 500 => 200, 1000 => 459];
if (!isset($valid[$pts])) {
    echo json_encode(['success' => false, 'message' => 'แพ็คเกจไม่ถูกต้อง']);
    exit;
}

// เรียก InwCloud API
$ch = curl_init(INWCLOUD_API_URL);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode(['voucher_link' => $voucher]),
    CURLOPT_HTTPHEADER => [
        'Authorization: Bearer ' . INWCLOUD_API_KEY,
        'Content-Type: application/json'
    ],
    CURLOPT_TIMEOUT => 30
]);
$resp = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$result = json_decode($resp, true);
$db = getDB();
$uid = $_SESSION['user_id'];

if ($code === 200 && ($result['status'] ?? '') === 'success') {
    $db->prepare("UPDATE users SET points = points + ? WHERE id = ?")->execute([$pts, $uid]);
    $db->prepare("INSERT INTO transactions (user_id, amount, type, voucher_link, status) VALUES (?, ?, 'redeem', ?, 'success')")
       ->execute([$uid, $pts, $voucher]);
    $stmt = $db->prepare("SELECT points FROM users WHERE id = ?");
    $stmt->execute([$uid]);
    echo json_encode(['success' => true, 'new_points' => $stmt->fetch()['points']]);
} else {
    $db->prepare("INSERT INTO transactions (user_id, amount, type, voucher_link, status) VALUES (?, ?, 'redeem', ?, 'failed')")
       ->execute([$uid, $pts, $voucher]);
    echo json_encode(['success' => false, 'message' => 'ซองไม่ถูกต้องหรือถูกใช้แล้ว']);
}
