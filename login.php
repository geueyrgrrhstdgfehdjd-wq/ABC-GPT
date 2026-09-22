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
    <title>เข้าสู่ระบบ - BluezyGPT</title>
    <link rel="stylesheet" href="/assets/css/global.css">
    <link rel="stylesheet" href="/assets/css/auth.css">
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body>
    <div class="auth-particles" id="ap"></div>
    <div class="auth-wrap">
        <div class="auth-card">
            <div class="auth-logo">
                <img src="<?= FLOATING_IMAGE ?>" alt="Logo">
            </div>
            <h1>เข้าสู่ระบบ</h1>
            <p class="sub">ยินดีต้อนรับกลับ</p>
            <form id="loginForm">
                <div class="fg">
                    <label><i data-lucide="user" class="icon icon-sm"></i> ชื่อผู้ใช้ / อีเมล</label>
                    <input type="text" id="lUser" required placeholder="กรอกชื่อผู้ใช้หรืออีเมล">
                </div>
                <div class="fg">
                    <label><i data-lucide="lock" class="icon icon-sm"></i> รหัสผ่าน</label>
                    <input type="password" id="lPass" required placeholder="กรอกรหัสผ่าน">
                </div>
                <div class="fg check-row">
                    <label class="ck">
                        <input type="checkbox" id="lRemember" checked>
                        <span>จดจำการเข้าสู่ระบบ</span>
                    </label>
                </div>
                <div id="lErr" class="err" hidden></div>
                <button type="submit" class="btn-main" id="lBtn">
                    <i data-lucide="log-in" class="icon icon-sm"></i> เข้าสู่ระบบ
                </button>
            </form>
            <p class="switch">ยังไม่มีบัญชี? <a href="/register.php">สมัครสมาชิก</a></p>
        </div>
    </div>
    <script>lucide.createIcons();</script>
    <script src="/assets/js/auth.js"></script>
</body>
</html>
