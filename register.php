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
    <title>สมัครสมาชิก - ABC GPT</title>
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
            <h1>สมัครสมาชิก</h1>
            <p class="sub">สร้างบัญชี ABC GPT เพื่อเริ่มต้นใช้งาน</p>
            <form id="regForm">
                <div class="fg">
                    <label><i data-lucide="user" class="icon icon-sm"></i> ชื่อผู้ใช้</label>
                    <input type="text" id="rUser" required minlength="3" maxlength="50" placeholder="3-50 ตัวอักษร">
                </div>
                <div class="fg">
                    <label><i data-lucide="mail" class="icon icon-sm"></i> อีเมล</label>
                    <input type="email" id="rEmail" required placeholder="your@email.com">
                </div>
                <div class="fg">
                    <label><i data-lucide="lock" class="icon icon-sm"></i> รหัสผ่าน</label>
                    <input type="password" id="rPass" required minlength="6" placeholder="อย่างน้อย 6 ตัวอักษร">
                </div>
                <div class="fg">
                    <label><i data-lucide="shield-check" class="icon icon-sm"></i> ยืนยันรหัสผ่าน</label>
                    <input type="password" id="rConfirm" required placeholder="กรอกอีกครั้ง">
                </div>
                <div id="rErr" class="err" hidden></div>
                <button type="submit" class="btn-main" id="rBtn">
                    <i data-lucide="user-plus" class="icon icon-sm"></i> สมัครสมาชิก
                </button>
            </form>
            <p class="switch">มีบัญชีแล้ว? <a href="/login.php">เข้าสู่ระบบ</a></p>
        </div>
    </div>
    <script>lucide.createIcons();</script>
    <script src="/assets/js/auth.js"></script>
</body>
</html>
