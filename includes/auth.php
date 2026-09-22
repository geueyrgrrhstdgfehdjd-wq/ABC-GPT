<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';

function isLoggedIn(): bool {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function requireLogin(): void {
    if (!isLoggedIn()) {
        header('Location: /login.php');
        exit;
    }
}

function getCurrentUser(): ?array {
    if (!isLoggedIn()) return null;
    $stmt = getDB()->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch() ?: null;
}

function resetDailyFree(int $uid): void {
    $today = date('Y-m-d');
    $db = getDB();
    $stmt = $db->prepare("SELECT last_free_reset FROM users WHERE id = ?");
    $stmt->execute([$uid]);
    $u = $stmt->fetch();
    if ($u && $u['last_free_reset'] !== $today) {
        $db->prepare("UPDATE users SET free_used_today = 0, last_free_reset = ? WHERE id = ?")
           ->execute([$today, $uid]);
    }
}
