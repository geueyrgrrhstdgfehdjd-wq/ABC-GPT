<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/auth.php';
if (isLoggedIn()) { header('Location: /dashboard.php'); exit; }
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BluezyGPT</title>
    <link rel="stylesheet" href="/assets/css/global.css">
    <link rel="stylesheet" href="/assets/css/loading.css">
</head>
<body>
    <div class="loader-wrap">
        <div class="particles" id="particles"></div>

        <div class="hero-3d" id="hero3d">
            <div class="hero-img-box">
                <img src="<?= FLOATING_IMAGE ?>" alt="BluezyGPT">
                <div class="ring r1"></div>
                <div class="ring r2"></div>
                <div class="ring r3"></div>
            </div>
        </div>

        <div class="loader-info">
            <h1 class="brand">
                <span>B</span><span>l</span><span>u</span><span>e</span><span>z</span><span>y</span><span>G</span><span>P</span><span>T</span>
            </h1>
            <div class="progress-track">
                <div class="progress-fill" id="progressFill"></div>
            </div>
            <p class="load-status" id="loadStatus">กำลังเตรียมระบบ...</p>
            <p class="load-percent" id="loadPercent">0%</p>

            <div class="cube-wrap">
                <div class="cube">
                    <div class="face f"></div>
                    <div class="face b"></div>
                    <div class="face l"></div>
                    <div class="face r"></div>
                    <div class="face t"></div>
                    <div class="face d"></div>
                </div>
            </div>
        </div>
    </div>
    <script src="/assets/js/loading.js"></script>
</body>
</html>
