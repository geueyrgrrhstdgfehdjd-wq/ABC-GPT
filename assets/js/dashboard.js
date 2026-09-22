const bp = document.getElementById('bp');
if (bp) {
    for (let i = 0; i < 35; i++) {
        const s = document.createElement('span');
        s.style.cssText = `width:${2+Math.random()*3}px;height:${2+Math.random()*3}px;left:${Math.random()*100}%;top:${Math.random()*100}%;animation-delay:${Math.random()*7}s;animation-duration:${5+Math.random()*5}s`;
        bp.appendChild(s);
    }
}

document.addEventListener('mousemove', e => {
    const h = document.getElementById('hero3d');
    if (!h) return;
    const x = (e.clientX / innerWidth - .5) * 28;
    const y = (e.clientY / innerHeight - .5) * 28;
    h.style.transform = `rotateY(${x}deg) rotateX(${-y}deg)`;
});
