<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/auth.php';
requireLogin();
$user = getCurrentUser();
resetDailyFree($user['id']);
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat - ABC GPT</title>
    <link rel="stylesheet" href="/assets/css/global.css">
    <link rel="stylesheet" href="/assets/css/chat.css">
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body>

<aside class="sidebar" id="sidebar">
    <div class="sb-head">
        <h3>เมนู</h3>
        <button class="sb-close" id="sbClose"><i data-lucide="x" class="icon"></i></button>
    </div>
    <div class="sb-body">
        <button class="sb-btn" id="openPoints"><i data-lucide="gem" class="icon"></i> เติมพ้อยต์</button>
        <button class="sb-btn" id="openContact"><i data-lucide="message-circle" class="icon"></i> ติดต่อทางเรา</button>
    </div>
    <div class="sb-foot">
        <p><i data-lucide="user" class="icon icon-sm"></i> <?= htmlspecialchars($user['username']) ?></p>
        <p><i data-lucide="gem" class="icon icon-sm"></i> <?= $user['points'] ?> พ้อยต์</p>
        <a href="/logout.php"><i data-lucide="log-out" class="icon icon-sm"></i> ออกจากระบบ</a>
    </div>
</aside>
<div class="sb-overlay" id="sbOverlay"></div>

<div class="chat-wrap">
    <header class="chat-top">
        <button class="hamburger" id="sbOpen"><span></span><span></span><span></span></button>
        <select id="modelSel">
            <?php foreach (MODELS as $k => $m): ?>
            <option value="<?= $k ?>"><?= $m['name'] ?></option>
            <?php endforeach; ?>
        </select>
        <span class="top-points"><i data-lucide="gem" class="icon icon-sm"></i> <span id="pts"><?= $user['points'] ?></span></span>
    </header>

    <div class="msgs" id="msgs">
        <div class="welcome">
            <img src="<?= FLOATING_IMAGE ?>" alt="" class="w-img">
            <h2>สวัสดี <?= htmlspecialchars($user['username']) ?></h2>
            <p>เลือกโมเดลแล้วเริ่มแชทได้เลย</p>
        </div>
    </div>

    <div class="input-bar">
        <textarea id="msgInput" rows="1" placeholder="พิมพ์ข้อความ..."></textarea>
        <button class="send" id="sendBtn"><i data-lucide="send" class="icon"></i></button>
    </div>
</div>

<div class="modal" id="pointsModal">
    <div class="modal-box">
        <div class="modal-head">
            <h2><i data-lucide="gem" class="icon"></i> เติมพ้อยต์</h2>
            <button class="modal-x" data-m="pointsModal"><i data-lucide="x" class="icon"></i></button>
        </div>
        <div class="pkgs">
            <?php foreach (POINT_PACKAGES as $p): ?>
            <div class="pkg" data-pts="<?= $p['points'] ?>" data-price="<?= $p['price'] ?>">
                <span class="pkg-pts"><?= $p['points'] ?> พ้อยต์</span>
                <span class="pkg-pr"><?= $p['price'] ?> ฿</span>
            </div>
            <?php endforeach; ?>
        </div>
        <div id="redeemSec" hidden>
            <h4>กรอกซองอั่งเปา TrueMoney</h4>
            <input type="text" id="voucherIn" placeholder="วางลิงก์ซองอั่งเปา">
            <button class="btn-main" id="redeemBtn"><i data-lucide="check-circle" class="icon icon-sm"></i> เติมพ้อยต์</button>
        </div>
    </div>
</div>

<div class="modal" id="contactModal">
    <div class="modal-box">
        <div class="modal-head">
            <h2><i data-lucide="message-circle" class="icon"></i> ติดต่อทางเรา</h2>
            <button class="modal-x" data-m="contactModal"><i data-lucide="x" class="icon"></i></button>
        </div>
        <div class="contact-body">
            <p>เข้าร่วม Discord เพื่อติดต่อและรับความช่วยเหลือ</p>
            <a href="<?= DISCORD_INVITE ?>" target="_blank" class="dc-btn">
                <i data-lucide="gamepad-2" class="icon"></i> เข้า Discord
            </a>
        </div>
    </div>
</div>

<script>lucide.createIcons();</script>
<script src="/assets/js/chat.js"></script>
</body>
</html>
