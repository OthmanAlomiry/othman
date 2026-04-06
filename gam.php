<?php
session_start();
// في الألعاب الاحترافية، نستخدم PHP هنا للتحقق من الجلسة، وتأمين التوكنات، وتهيئة البيانات
$highScore = $_SESSION['pro_high_score'] ?? 0;
?>
<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>Alien Hunter - Pro Edition</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;900&display=swap" rel="stylesheet">
    <style>
        body { margin: 0; background: #0a0a0a; color: white; overflow: hidden; font-family: 'Cairo', sans-serif; cursor: crosshair; }
        #game-container { position: relative; width: 100vw; height: 100vh; display: flex; justify-content: center; align-items: center; }
        canvas { background: #141414; border: 4px solid #222; box-shadow: 0 0 100px #000; }
        
        /* واجهة المستخدم (HUD) الاحترافية */
        .hud { position: absolute; padding: 20px; font-weight: 900; text-transform: uppercase; letter-spacing: 2px; text-shadow: 2px 2px 0 #000; }
        .top-left { top: 10px; left: 10px; color: #ff0055; }
        .top-right { top: 10px; right: 10px; color: #00d2ff; }
        .hp-bar { width: 250px; height: 10px; background: #333; border: 1px solid #ff0055; margin-bottom: 5px; }
        .hp-fill { width: 100%; height: 100%; background: #ff0055; transition: 0.1s linear; }

        /* شاشة النهاية */
        #game-over-screen {
            position: absolute; display: none; text-align: center;
            background: rgba(0,0,0,0.9); padding: 50px; border: 1px solid #ff0055;
        }
    </style>
</head>
<body>

<div id="game-container">
    <div class="hud top-left">
        <div class="hp-bar"><div id="hp-fill" class="hp-fill"></div></div>
        AMMO: INF
    </div>
    <div class="hud top-right">
        SCORE: <span id="score">0</span>
    </div>

    <canvas id="gameCanvas"></canvas>

    <div id="game-over-screen">
        <h1 style="color:#ff0055; font-size:60px;">GAME OVER</h1>
        <p>تصديك للهجوم فشل.</p>
        <button onclick="location.reload()" style="padding:10px 20px; cursor:pointer;">إعادة المحاولة</button>
    </div>
</div>

<script>
// --- إعدادات المحرك الاحترافي ---
const canvas = document.getElementById('gameCanvas');
const ctx = canvas.getContext('2d');
const hpFill = document.getElementById('hp-fill');
const scoreEl = document.getElementById('score');

// حجم الخريطة (كبيرة) - VIEWPORT
canvas.width = window.innerWidth * 0.9;
canvas.height = window.innerHeight * 0.9;

const input = { left: false, right: false, up: false, down: false };
const mouse = { x: 0, y: 0 };
let gameState = 'PLAYING';
let score = 0;
let frame = 0;

// --- الفئات (Classes) للأجسام - هيكلة احترافية ---

// 1. اللاعب (Player)
class Player {
    constructor(x, y) {
        this.x = x; this.y = y; this.radius = 15;
        this.color = '#00d2ff'; this.speed = 5; this.health = 100;
        this.angle = 0;
    }
    draw() {
        ctx.save();
        ctx.translate(this.x, this.y);
        ctx.rotate(this.angle);
        // جسم اللاعب
        ctx.fillStyle = this.color;
        ctx.shadowBlur = 15; ctx.shadowColor = this.color;
        ctx.beginPath(); ctx.arc(0, 0, this.radius, 0, Math.PI * 2); ctx.fill();
        // سلاح اللاعب لتعيين الاتجاه
        ctx.fillStyle = '#fff';
        ctx.fillRect(0, -3, this.radius + 10, 6);
        ctx.restore();
    }
    update() {
        if (input.left) this.x -= this.speed;
        if (input.right) this.x += this.speed;
        if (input.up) this.y -= this.speed;
        if (input.down) this.y += this.speed;
        
        // تدوير اللاعب نحو الماوس
        this.angle = Math.atan2(mouse.y - this.y, mouse.x - this.x);
    }
}

// 2. الرصاص (Projectile)
class Projectile {
    constructor(x, y, angle) {
        this.x = x; this.y = y; this.radius = 4; this.color = '#fff000';
        this.speed = 12;
        this.velocity = {
            x: Math.cos(angle) * this.speed,
            y: Math.sin(angle) * this.speed
        };
    }
    draw() {
        ctx.fillStyle = this.color;
        ctx.shadowBlur = 10; ctx.shadowColor = this.color;
        ctx.beginPath(); ctx.arc(this.x, this.y, this.radius, 0, Math.PI * 2); ctx.fill();
    }
    update() {
        this.x += this.velocity.x;
        this.y += this.velocity.y;
    }
}

// 3. الكائنات الفضائية (Aliens)
class Alien {
    constructor(x, y, level) {
        this.x = x; this.y = y; this.radius = 12 + level;
        this.color = '#55ff55'; this.speed = 2 + (level/10);
    }
    draw() {
        ctx.fillStyle = this.color;
        ctx.shadowBlur = 10; ctx.shadowColor = this.color;
        ctx.beginPath(); ctx.arc(this.x, this.y, this.radius, 0, Math.PI * 2); ctx.fill();
        // تأثير "عين" مرعب
        ctx.fillStyle = 'red'; ctx.beginPath(); ctx.arc(this.x, this.y, 4, 0, Math.PI * 2); ctx.fill();
    }
    update(player) {
        // AI بسيط: ملاحقة اللاعب
        let angle = Math.atan2(player.y - this.y, player.x - this.x);
        this.x += Math.cos(angle) * this.speed;
        this.y += Math.sin(angle) * this.speed;
    }
}

// 4. نظام الجسيمات (Particles/Blood)
class Particle {
    constructor(x, y, color) {
        this.x = x; this.y = y; this.radius = Math.random() * 3;
        this.color = color;
        this.velocity = { x: (Math.random() - 0.5) * 6, y: (Math.random() - 0.5) * 6 };
        this.alpha = 1; this.decay = Math.random() * 0.02 + 0.01;
    }
    draw() {
        ctx.save();
        ctx.globalAlpha = this.alpha;
        ctx.fillStyle = this.color;
        ctx.beginPath(); ctx.arc(this.x, this.y, this.radius, 0, Math.PI * 2); ctx.fill();
        ctx.restore();
    }
    update() {
        this.x += this.velocity.x;
        this.y += this.velocity.y;
        this.alpha -= this.decay;
    }
}

// --- تهيئة الكائنات ---
const player = new Player(canvas.width / 2, canvas.height / 2);
let projectiles = [];
let aliens = [];
let particles = [];

// --- نظام الإدخال ---
window.addEventListener('keydown', e => {
    if (e.key === 'a' || e.key === 'ArrowLeft') input.left = true;
    if (e.key === 'd' || e.key === 'ArrowRight') input.right = true;
    if (e.key === 'w' || e.key === 'ArrowUp') input.up = true;
    if (e.key === 's' || e.key === 'ArrowDown') input.down = true;
});
window.addEventListener('keyup', e => {
    if (e.key === 'a' || e.key === 'ArrowLeft') input.left = false;
    if (e.key === 'd' || e.key === 'ArrowRight') input.right = false;
    if (e.key === 'w' || e.key === 'ArrowUp') input.up = false;
    if (e.key === 's' || e.key === 'ArrowDown') input.down = false;
});
window.addEventListener('mousemove', e => {
    let rect = canvas.getBoundingClientRect();
    mouse.x = e.clientX - rect.left;
    mouse.y = e.clientY - rect.top;
});
window.addEventListener('mousedown', () => {
    if (gameState === 'PLAYING') {
        projectiles.push(new Projectile(player.x, player.y, player.angle));
    }
});

// --- الحلقة الرئيسية (Game Loop) ---
function animate() {
    if (gameState !== 'PLAYING') return;
    frame++;

    // 1. تنظيف الشاشة (خلفية شبه شفافة لتأثير Trail)
    ctx.fillStyle = 'rgba(20, 20, 20, 0.3)';
    ctx.fillRect(0, 0, canvas.width, canvas.height);

    // 2. تحديث ورسم اللاعب
    player.update();
    player.draw();

    // 3. إدارة الرصاص
    projectiles.forEach((p, i) => {
        p.update(); p.draw();
        // إزالة الرصاص خارج الشاشة
        if (p.x < 0 || p.x > canvas.width || p.y < 0 || p.y > canvas.height) {
            projectiles.splice(i, 1);
        }
    });

    // 4. إدارة الجسيمات (الدماء)
    particles.forEach((part, i) => {
        if (part.alpha <= 0) { particles.splice(i, 1); }
        else { part.update(); part.draw(); }
    });

    // 5. إدارة الأعداء (AI)
    // سباون عشوائي للأعداء خارج الشاشة
    if (frame % 60 === 0) {
        let x, y;
        if (Math.random() < 0.5) { x = Math.random() < 0.5 ? -30 : canvas.width + 30; y = Math.random() * canvas.height; }
        else { x = Math.random() * canvas.width; y = Math.random() < 0.5 ? -30 : canvas.height + 30; }
        aliens.push(new Alien(x, y, score/100));
    }

    aliens.forEach((a, i) => {
        a.update(player); a.draw();

        // اصطدام العدو باللاعب
        let distToPlayer = Math.hypot(player.x - a.x, player.y - a.y);
        if (distToPlayer < a.radius + player.radius) {
            player.health -= 0.5; // ضرر مستمر عند الملامسة
            hpFill.style.width = player.health + '%';
            if (player.health <= 0) gameOver();
        }

        // اصطدام الرصاص بالعدو
        projectiles.forEach((p, pi) => {
            let distToBullet = Math.hypot(p.x - a.x, p.y - a.y);
            if (distToBullet < a.radius + p.radius) {
                // توليد دماء ( Particles)
                for (let k = 0; k < 10; k++) { particles.push(new Particle(a.x, a.y, '#55ff55')); }
                
                aliens.splice(i, 1);
                projectiles.splice(pi, 1);
                score += 10;
                scoreEl.innerText = score;
            }
        });
    });

    requestAnimationFrame(animate);
}

function gameOver() {
    gameState = 'END';
    document.getElementById('game-over-screen').style.display = 'block';
    canvas.style.filter = 'blur(10px)';
}

// البدء
animate();
</script>
</body>
</html>
