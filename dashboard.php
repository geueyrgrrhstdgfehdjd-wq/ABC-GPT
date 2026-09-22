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
    <title>Dashboard - ABC GPT</title>
    <link rel="stylesheet" href="/assets/css/global.css">
    <link rel="stylesheet" href="/assets/css/dashboard.css">
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body>
    <div class="bg-particles" id="bp"></div>

    <nav class="topnav">
        <span class="nav-logo">ABC GPT</span>
        <div class="nav-right">
            <span class="nav-item"><i data-lucide="user" class="icon icon-sm"></i> <?= htmlspecialchars($user['username']) ?></span>
            <span class="nav-item"><i data-lucide="gem" class="icon icon-sm"></i> <?= $user['points'] ?></span>
            <a href="/logout.php" class="nav-out"><i data-lucide="log-out" class="icon icon-sm"></i> ออก</a>
        </div>
    </nav>

    <main class="dash-main">
        <div class="hero-3d" id="hero3d">
            <div class="hero-inner">
                <img src="<?= FLOATING_IMAGE ?>" alt="AI">
                <div class="glow"></div>
                <div class="ring r1"></div>
                <div class="ring r2"></div>
                <div class="ring r3"></div>
            </div>
        </div>

        <h1 class="dash-title">เริ่มต้นใช้งาน AI ของทางเรา</h1>
        <p class="dash-desc">ABC GPT พร้อมช่วยคุณทุกงาน ไม่ว่าจะเป็นการเขียน วิเคราะห์ เขียนโปรแกรม หรือสร้างสรรค์</p>

        <a href="/chat.php" class="btn-start">
            <i data-lucide="rocket" class="icon"></i> เริ่มต้นใช้งาน
        </a>

        <div class="models-grid">
            <?php foreach (MODELS as $k => $m): ?>
            <div class="m-card">
                <h3><?= $m['name'] ?></h3>
                <p><?= $m['desc'] ?></p>
                <?php if ($m['free'] > 0): ?>
                    <span class="badge free"><i data-lucide="gift" class="icon icon-sm"></i> ฟรี <?= $m['free'] ?>/วัน</span>
                <?php else: ?>
                    <span class="badge paid"><i data-lucide="gem" class="icon icon-sm"></i> ใช้พ้อยต์</span>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
    </main>

    <script>lucide.createIcons();</script>
    <script src="/assets/js/dashboard.js"></script>
</body>
</html>
