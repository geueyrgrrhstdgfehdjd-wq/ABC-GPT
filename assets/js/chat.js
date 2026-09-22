// Sidebar
const sidebar = document.getElementById('sidebar');
const overlay = document.getElementById('sbOverlay');
document.getElementById('sbOpen').onclick = () => { sidebar.classList.add('open'); overlay.classList.add('show'); };
document.getElementById('sbClose').onclick = closeSb;
overlay.onclick = closeSb;
function closeSb() { sidebar.classList.remove('open'); overlay.classList.remove('show'); }

// Modals
document.getElementById('openPoints').onclick = () => { closeSb(); document.getElementById('pointsModal').classList.add('show'); };
document.getElementById('openContact').onclick = () => { closeSb(); document.getElementById('contactModal').classList.add('show'); };
document.querySelectorAll('.modal-x').forEach(b => {
    b.onclick = () => document.getElementById(b.dataset.m).classList.remove('show');
});
document.querySelectorAll('.modal').forEach(m => {
    m.addEventListener('click', e => { if (e.target === m) m.classList.remove('show'); });
});

// Package select
let selPts = null;
document.querySelectorAll('.pkg').forEach(p => {
    p.onclick = () => {
        document.querySelectorAll('.pkg').forEach(x => x.classList.remove('sel'));
        p.classList.add('sel');
        selPts = parseInt(p.dataset.pts);
        document.getElementById('redeemSec').hidden = false;
    };
});

// Redeem
document.getElementById('redeemBtn').onclick = async () => {
    const v = document.getElementById('voucherIn').value.trim();
    if (!v) { alert('กรุณากรอกลิงก์ซอง'); return; }
    if (!selPts) { alert('เลือกแพ็คเกจก่อน'); return; }
    const btn = document.getElementById('redeemBtn');
    btn.disabled = true;
    try {
        const r = await fetch('/api/redeem_api.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ voucher: v, points: selPts })
        });
        const d = await r.json();
        if (d.success) {
            document.getElementById('pts').textContent = d.new_points;
            alert('เติมสำเร็จ! +' + selPts + ' พ้อยต์');
            document.getElementById('pointsModal').classList.remove('show');
        } else {
            alert(d.message || 'ไม่สำเร็จ');
        }
    } catch { alert('เกิดข้อผิดพลาด'); }
    btn.disabled = false;
};

// Chat
const msgs = document.getElementById('msgs');
const input = document.getElementById('msgInput');
const sendBtn = document.getElementById('sendBtn');
const modelSel = document.getElementById('modelSel');

input.addEventListener('input', function () {
    this.style.height = 'auto';
    this.style.height = Math.min(this.scrollHeight, 140) + 'px';
});
input.addEventListener('keydown', e => {
    if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); send(); }
});
sendBtn.onclick = send;

async function send() {
    const text = input.value.trim();
    if (!text) return;
    input.value = '';
    input.style.height = 'auto';
    addMsg(text, 'u');
    const loadEl = addMsg('กำลังคิด...', 'load');
    sendBtn.disabled = true;
    try {
        const r = await fetch('/api/chat_api.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ message: text, model: modelSel.value })
        });
        const d = await r.json();
        loadEl.remove();
        if (d.success) {
            addMsg(d.reply, 'a');
            if (d.points_left !== undefined) document.getElementById('pts').textContent = d.points_left;
        } else {
            addMsg(d.message || 'เกิดข้อผิดพลาด', 'a');
        }
    } catch {
        loadEl.remove();
        addMsg('เชื่อมต่อไม่สำเร็จ', 'a');
    }
    sendBtn.disabled = false;
}

function addMsg(text, type) {
    const d = document.createElement('div');
    d.className = 'msg msg-' + type;
    d.textContent = text;
    msgs.appendChild(d);
    msgs.scrollTop = msgs.scrollHeight;
    return d;
}
