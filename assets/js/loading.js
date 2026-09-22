// Particles
const pc = document.getElementById('particles');
for (let i = 0; i < 50; i++) {
    const s = document.createElement('span');
    s.style.left = Math.random() * 100 + '%';
    s.style.top = Math.random() * 100 + '%';
    s.style.animationDelay = Math.random() * 7 + 's';
    s.style.animationDuration = (5 + Math.random() * 4) + 's';
    pc.appendChild(s);
}

// Loading
const texts = [
    'กำลังเตรียมระบบ...',
    'เชื่อมต่อ AI Engine...',
    'โหลดโมเดล...',
    'ตรวจสอบความปลอดภัย...',
    'เกือบเสร็จแล้ว...',
    'พร้อม!'
];
let p = 0;
const fill = document.getElementById('progressFill');
const status = document.getElementById('loadStatus');
const pct = document.getElementById('loadPercent');

function step() {
    if (p >= 100) {
        status.textContent = texts[5];
        pct.textContent = '100%';
        fill.style.width = '100%';
        setTimeout(() => { location.href = '/login.php'; }, 700);
        return;
    }
    p = Math.min(p + Math.random() * 7 + 2, 100);
    fill.style.width = p + '%';
    pct.textContent = Math.floor(p) + '%';
    status.textContent = texts[Math.min(Math.floor(p / 20), texts.length - 1)];
    setTimeout(step, 120 + Math.random() * 180);
}

// 3D tilt
document.addEventListener('mousemove', e => {
    const hero = document.getElementById('hero3d');
    if (!hero) return;
    const x = (e.clientX / innerWidth - .5) * 25;
    const y = (e.clientY / innerHeight - .5) * 25;
    hero.style.transform = `rotateY(${x}deg) rotateX(${-y}deg)`;
});

setTimeout(step, 600);
