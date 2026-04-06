<?php
session_start();
// هنا يمكن إضافة نظام تسجيل دخول بالـ PHP لحماية النتائج
?>
<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>Space Warrior - Ultra Pro</title>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;900&display=swap" rel="stylesheet">
    <style>
        body { margin: 0; background: #000; overflow: hidden; font-family: 'Orbitron', sans-serif; cursor: crosshair; }
        canvas { display: block; }
        
        /* واجهة المستخدم السينمائية */
        #ui-layer {
            position: absolute; top: 0; left: 0; width: 100%; height: 100%;
            pointer-events: none; color: #00f2ff; padding: 20px;
        }
        .stat { font-size: 20px; text-shadow: 0 0 10px #00f2ff; margin-bottom: 10px; }
        .hp-container { width: 200px; height: 10px; background: rgba(0,242,255,0.2); border: 1px solid #00f2ff; }
        #hp-bar { width: 100%; height: 100%; background: #00f2ff; transition: 0.2s; }

        #overlay {
            position: absolute; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.85); display: flex; flex-direction: column;
            justify-content: center; align-items: center; z-index: 100;
        }
        h1 { font-size: 60px; color: #00f2ff; text-shadow: 0 0 20px #00f2ff; margin: 0; }
        button {
            background: transparent; border: 2px solid #00f2ff; color: #00f2ff;
            padding: 15px 40px; font-family: 'Orbitron'; cursor: pointer; font-size: 20px;
            transition: 0.3s; margin-top: 20px;
        }
        button:hover { background: #00f2ff; color: #000; box-shadow: 0 0 30px #00f2ff; }
    </style>
</head>
<body>

<div id="ui-layer">
    <div class="stat">SCORE: <span id="scoreText">0</span></div>
    <div class="hp-container"><div id="hp-bar"></div></div>
</div>

<div id="overlay">
    <h1 id="mainTitle">SPACE WARRIOR</h1>
    <p style="color: #fff;">استخدم الماوس للتحرك والضغط للإطلاق</p>
    <button onclick="startGame()">INITIALIZE SYSTEM</button>
</div>

<canvas id="gameCanvas"></canvas>

<script>
/**
 * نظام برمجة الألعاب الاحترافي
 * استخدام الـ Canvas API مع تقنيات OOP
 */

const canvas = document.getElementById('gameCanvas');
const ctx = canvas.getContext('2d');
const hpBar = document.getElementById('hp-bar');
const scoreText = document.getElementById('scoreText');
const overlay = document.getElementById('overlay');

canvas.width = window.innerWidth;
canvas.height = window.innerHeight;

let score = 0;
let health = 100;
let gameActive = false;
let frames = 0;

// مصفوفات الكائنات
let projectiles = [];
let enemies = [];
let particles = [];
let stars = [];

// 1. نظام الخلفية المتحركة (Parallax Stars)
class Star {
    constructor() {
        this.x = Math.random() * canvas.width;
        this.y = Math.random() * canvas.height;
        this.size = Math.random() * 2;
        this.speed = Math.random() * 3 + 1;
    }
    draw() {
        ctx.fillStyle = "white";
        ctx.beginPath();
        ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
        ctx.fill();
    }
    update() {
        this.y += this.speed;
        if (this.y > canvas.height) {
            this.y = 0;
            this.x = Math.random() * canvas.width;
        }
    }
}

// 2. نظام اللاعب (Ship)
const player = {
    x: canvas.width / 2,
    y: canvas.height - 100,
    w: 50, h: 50,
    angle: 0,
    draw() {
        ctx.save();
        ctx.translate(this.x, this.y);
        ctx.rotate(this.angle);
        
        // رسم سفينة فضائية احترافية (مثلث نيون)
        ctx.shadowBlur = 20;
        ctx.shadowColor = "#00f2ff";
        ctx.strokeStyle = "#00f2ff";
        ctx.lineWidth = 3;
        ctx.beginPath();
        ctx.moveTo(0, -25);
        ctx.lineTo(-20, 20);
        ctx.lineTo(20, 20);
        ctx.closePath();
        ctx.stroke();
        
        // المحرك
        ctx.fillStyle = "rgba(0, 242, 255, 0.5)";
        ctx.fillRect(-5, 20, 10, 10);
        ctx.restore();
    },
    update() {
        // ملاحقة الماوس بسلاسة
        let dx = mouse.x - this.x;
        let dy = mouse.y - this.y;
        this.angle = Math.atan2(dy, dx) + Math.PI/2;
        this.x += dx * 0.1;
        this.y += dy * 0.1;
    }
};

// 3. نظام الجسيمات (In-depth Particle System)
class Particle {
    constructor(x, y, color) {
        this.x = x; this.y = y;
        this.color = color;
        this.size = Math.random() * 3 + 1;
        this.speedX = (Math.random() - 0.5) * 8;
        this.speedY = (Math.random() - 0.5) * 8;
        this.life = 1.0;
    }
    update() {
        this.x += this.speedX;
        this.y += this.speedY;
        this.life -= 0.02;
    }
    draw() {
        ctx.globalAlpha = this.life;
        ctx.fillStyle = this.color;
        ctx.beginPath();
        ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
        ctx.fill();
        ctx.globalAlpha = 1;
    }
}

// 4. الأعداء (Enemies)
class Enemy {
    constructor() {
        this.x = Math.random() * canvas.width;
        this.y = -50;
        this.size = 30;
        this.speed = Math.random() * 2 + 2;
        this.color = "#ff0055";
    }
    draw() {
        ctx.shadowBlur = 15;
        ctx.shadowColor = this.color;
        ctx.strokeStyle = this.color;
        ctx.strokeRect(this.x - 15, this.y - 15, 30, 30);
    }
    update() {
        this.y += this.speed;
    }
}

// التحكم بالماوس
const mouse = { x: canvas.width/2, y: canvas.height/2 };
window.addEventListener('mousemove', (e) => {
    mouse.x = e.clientX;
    mouse.y = e.clientY;
});

window.addEventListener('mousedown', () => {
    if(!gameActive) return;
    projectiles.push({
        x: player.x, y: player.y,
        vX: Math.cos(player.angle - Math.PI/2) * 15,
        vY: Math.sin(player.angle - Math.PI/2) * 15
    });
});

// تهيئة النجوم
for(let i=0; i<150; i++) stars.push(new Star());

function startGame() {
    overlay.style.display = 'none';
    gameActive = true;
    score = 0;
    health = 100;
    enemies = [];
    projectiles = [];
    animate();
}

function animate() {
    if(!gameActive) return;
    frames++;
    
    // تنظيف الشاشة مع تأثير Trail بسيط
    ctx.fillStyle = "rgba(0,0,0,0.2)";
    ctx.fillRect(0, 0, canvas.width, canvas.height);

    // تحديث الخلفية
    stars.forEach(s => { s.update(); s.draw(); });

    // تحديث اللاعب
    player.update();
    player.draw();

    // إدارة الرصاص
    projectiles.forEach((p, index) => {
        p.x += p.vX; p.y += p.vY;
        ctx.fillStyle = "#fff";
        ctx.beginPath(); ctx.arc(p.x, p.y, 3, 0, Math.PI*2); ctx.fill();
        
        if(p.x<0 || p.x>canvas.width || p.y<0 || p.y>canvas.height) projectiles.splice(index, 1);
    });

    // توليد وإدارة الأعداء
    if(frames % 40 === 0) enemies.push(new Enemy());
    enemies.forEach((en, eIdx) => {
        en.update();
        en.draw();

        // اصطدام العدو باللاعب
        let dist = Math.hypot(player.x - en.x, player.y - en.y);
        if(dist < 40) {
            health -= 10;
            hpBar.style.width = health + "%";
            enemies.splice(eIdx, 1);
            if(health <= 0) endGame();
        }

        // اصطدام الرصاص بالعدو
        projectiles.forEach((p, pIdx) => {
            let pDist = Math.hypot(p.x - en.x, p.y - en.y);
            if(pDist < 25) {
                // إنشاء انفجار جسيمات
                for(let i=0; i<15; i++) particles.push(new Particle(en.x, en.y, en.color));
                
                enemies.splice(eIdx, 1);
                projectiles.splice(pIdx, 1);
                score += 100;
                scoreText.innerText = score;
            }
        });
    });

    // إدارة الجسيمات
    particles.forEach((part, index) => {
        if(part.life <= 0) particles.splice(index, 1);
        else { part.update(); part.draw(); }
    });

    requestAnimationFrame(animate);
}

function endGame() {
    gameActive = false;
    overlay.style.display = 'flex';
    document.getElementById('mainTitle').innerText = "CORE BREACHED";
    document.getElementById('mainTitle').style.color = "#ff0055";
}
</script>
</body>
</html>
