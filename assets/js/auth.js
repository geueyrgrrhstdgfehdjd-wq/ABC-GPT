// Particles
const ap = document.getElementById('ap');
if (ap) {
    for (let i = 0; i < 25; i++) {
        const s = document.createElement('span');
        s.style.cssText = `width:${2+Math.random()*3}px;height:${2+Math.random()*3}px;left:${Math.random()*100}%;top:${Math.random()*100}%;animation-delay:${Math.random()*6}s;animation-duration:${4+Math.random()*4}s`;
        ap.appendChild(s);
    }
}

// Login
const loginForm = document.getElementById('loginForm');
if (loginForm) {
    loginForm.onsubmit = async e => {
        e.preventDefault();
        const btn = document.getElementById('lBtn');
        const err = document.getElementById('lErr');
        btn.disabled = true;
        err.hidden = true;
        try {
            const r = await fetch('/api/auth_api.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    action: 'login',
                    username: document.getElementById('lUser').value,
                    password: document.getElementById('lPass').value,
                    remember: document.getElementById('lRemember').checked
                })
            });
            const d = await r.json();
            if (d.success) { location.href = '/dashboard.php'; }
            else { err.textContent = d.message; err.hidden = false; }
        } catch {
            err.textContent = 'เกิดข้อผิดพลาด กรุณาลองใหม่';
            err.hidden = false;
        }
        btn.disabled = false;
    };
}

// Register
const regForm = document.getElementById('regForm');
if (regForm) {
    regForm.onsubmit = async e => {
        e.preventDefault();
        const btn = document.getElementById('rBtn');
        const err = document.getElementById('rErr');
        const pass = document.getElementById('rPass').value;
        const conf = document.getElementById('rConfirm').value;
        if (pass !== conf) {
            err.textContent = 'รหัสผ่านไม่ตรงกัน';
            err.hidden = false;
            return;
        }
        btn.disabled = true;
        err.hidden = true;
        try {
            const r = await fetch('/api/auth_api.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    action: 'register',
                    username: document.getElementById('rUser').value,
                    email: document.getElementById('rEmail').value,
                    password: pass
                })
            });
            const d = await r.json();
            if (d.success) { location.href = '/dashboard.php'; }
            else { err.textContent = d.message; err.hidden = false; }
        } catch {
            err.textContent = 'เกิดข้อผิดพลาด กรุณาลองใหม่';
            err.hidden = false;
        }
        btn.disabled = false;
    };
}
