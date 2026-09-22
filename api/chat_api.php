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
$msg = trim($in['message'] ?? '');
$model = $in['model'] ?? 'bluezygpt-free';

if (!$msg) { echo json_encode(['success' => false, 'message' => 'ข้อความว่าง']); exit; }
if (!isset(MODELS[$model])) { echo json_encode(['success' => false, 'message' => 'โมเดลไม่ถูกต้อง']); exit; }

$db = getDB();
$uid = $_SESSION['user_id'];
$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$uid]);
$u = $stmt->fetch();

if (!$u) { echo json_encode(['success' => false, 'message' => 'ไม่พบผู้ใช้']); exit; }

// ตรวจสอบพ้อยต์
$free = MODELS[$model]['free'];
if ($free > 0) {
    if ($u['free_used_today'] >= $free) {
        echo json_encode(['success' => false, 'message' => 'ฟรีพ้อยต์วันนี้หมดแล้ว (' . $u['free_used_today'] . '/' . $free . ')']);
        exit;
    }
    $db->prepare("UPDATE users SET free_used_today = free_used_today + 1 WHERE id = ?")->execute([$uid]);
} else {
    if ($u['points'] < 1) {
        echo json_encode(['success' => false, 'message' => 'พ้อยต์ไม่เพียงพอ กรุณาเติมพ้อยต์']);
        exit;
    }
    $db->prepare("UPDATE users SET points = points - 1 WHERE id = ?")->execute([$uid]);
}

// บันทึกข้อความ
$db->prepare("INSERT INTO chat_history (user_id, model, role, content) VALUES (?, ?, ?, ?)")
   ->execute([$uid, $model, 'user', $msg]);

// ดึงประวัติ 10 ข้อความล่าสุด
$hist = $db->prepare("SELECT role, content FROM chat_history WHERE user_id = ? ORDER BY id DESC LIMIT 10");
$hist->execute([$uid]);
$messages = array_reverse($hist->fetchAll());

// เรียก BluezyGPT API
$payload = [
    'model' => $model,
    'messages' => $messages,
    'stream' => false
];

$ch = curl_init(BLUEZY_API_URL . '/chat/completions');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($payload),
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        'Authorization: Bearer ' . BLUEZY_API_KEY,
        'x-bluezy-client: web'
    ],
    CURLOPT_TIMEOUT => 120
]);
$resp = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($code !== 200) {
    echo json_encode(['success' => false, 'message' => 'AI ตอบกลับไม่ได้ (' . $code . ')']);
    exit;
}

$data = json_decode($resp, true);
$reply = $data['choices'][0]['message']['content'] ?? 'ขออภัย ไม่สามารถตอบได้';

$db->prepare("INSERT INTO chat_history (user_id, model, role, content) VALUES (?, ?, ?, ?)")
   ->execute([$uid, $model, 'assistant', $reply]);

$stmt2 = $db->prepare("SELECT points FROM users WHERE id = ?");
$stmt2->execute([$uid]);
$pts = $stmt2->fetch()['points'];

echo json_encode(['success' => true, 'reply' => $reply, 'points_left' => $pts]);
