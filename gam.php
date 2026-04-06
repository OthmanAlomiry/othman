<?php
session_start();
// نظام متطور لحفظ السجل الشخصي
$highScore = $_SESSION['high_score'] ?? 0;
?>
<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>Neon Warrior - Ultimate Edition</title>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;900&display=swap" rel="stylesheet">
    <style>
        body { margin: 0; background: #000; overflow: hidden; font-family: 'Orbitron', sans-serif; }
        #game-wrapper {
            position: relative; width: 100vw; height: 100vh;
            display: flex; justify-content: center; align-items: center;
        }
        canvas { background: #050505; border: 2px solid #333; box-shadow: 0 0 50px rgba(0,0,0,1); }
        
        /* واجهة المستخدم */
        .hud {
            position: absolute; top: 20px; left: 20px; width: 300px;
            pointer-events: none; z-index: 10;
        }
        .hp-bar-bg { width: 100%; height: 15px; background: #444; border-radius: 10px; overflow: hidden; }
        .hp-bar-fill { width: 100%; height: 100%; background: linear-gradient(90deg, #ff0055, #ff7675); transition: 0.3s; }
        
        .score-box { color: #00d2ff; font-size: 24px; margin-top: 10px; text-shadow: 0 0 10px #00d2ff; }

        /* شاشة البداية والنهاية */
        .overlay {
            position: absolute; background: rgba(0,0,0,0.9);
            color: white; padding: 40px; text-align: center; border: 1px solid #00d2ff;
        }
        button {
            background: transparent; color: #00d2ff; border: 2px solid #00d2ff;
            padding: 10px 30px; font-family: 'Orbitron'; cursor: pointer; transition: 0.3s;
        }
        button:hover { background: #00d2ff; color: black; box-shadow: 0 0 20px #00d2ff; }
        .hidden { display: none; }
    </style>
</head>
<body>

<div id="game-wrapper">
    <div class="hud">
        <div class="hp-bar-bg"><div id="hp-fill" class="hp-bar-fill"></div></div>
        <div class="score-box">SCORE: <span id="current-score">0</span></div>
    </div>

    <canvas id="gameCanvas"></canvas>

    <div id="start-screen" class="overlay">
        <h1 style="color:#00d2ff; font-size: 48px;">NEON WARRIOR</h1>
        <p>استخدم المسافة (SPACE) للقفز فوق النيازك</p>
        <button onclick="initGame()">إطلاق المحرك</button>
    </div>
</div>

<script>
const canvas = document.getElementById('gameCanvas');
const ctx = canvas.getContext('2d');
const hpFill = document.getElementById('hp-fill');
const scoreText = document.getElementById('current-score');

canvas.width = 1000;
canvas.height = 400;

// إعدادات اللعبة الاحترافية
let gameState = 'START';
let score = 0;
let health = 100;
let frame = 0;

// كائن اللاعب (المحارب)
const player = {
    x: 100, y: 300, w: 40, h: 60,
    dy: 0, jumpPower: -15, gravity: 0.8,
    isGrounded: false,
    color: '#00d2ff'
};

// العقبات (النيازك)
let obstacles = [];

function initGame() {
    document.getElementById('start-screen').classList.add('hidden');
    gameState = 'PLAYING';
    score = 0;
    health = 100;
    obstacles = [];
    animate();
}

window.addEventListener('keydown', e => {
    if (e.code === 'Space' && player.isGrounded) {
        player.dy = player.jumpPower;
        player.isGrounded = false;
    }
});

function drawBackground() {
    // تأثير Grid (شبكة) متحركة لتعطي شعور بالسرعة
    ctx.strokeStyle = '#111';
    for(let i=0; i<canvas.width; i+=40) {
        ctx.beginPath();
        ctx.moveTo(i - (frame % 40), 0);
        ctx.lineTo(i - (frame % 40), canvas.height);
        ctx.stroke();
    }
}

function animate() {
    if (gameState !== 'PLAYING') return;

    ctx.clearRect(0, 0, canvas.width, canvas.height);
    frame++;

    drawBackground();

    // فيزياء اللاعب
    player.dy += player.gravity;
    player.y += player.dy;

    if (player.y + player.h > 350) { // الأرضية
        player.y = 350 - player.h;
        player.dy = 0;
        player.isGrounded = true;
    }

    // رسم المحارب (تأثير وهج النيون)
    ctx.fillStyle = player.color;
    ctx.shadowBlur = 20;
    ctx.shadowColor = player.color;
    ctx.fillRect(player.x, player.y, player.w, player.h);
    
    // رسم "عين" المحارب لتعطيه شخصية
    ctx.fillStyle = "white";
    ctx.fillRect(player.x + 25, player.y + 15, 10, 5);

    // إدارة العقبات
    if (frame % 70 === 0) {
        obstacles.push({ x: canvas.width, y: 310, w: 30, h: 40, color: '#ff0055' });
    }

    obstacles.forEach((obs, i) => {
        obs.x -= (7 + score/100); // تزداد السرعة مع السكور
        
        ctx.fillStyle = obs.color;
        ctx.shadowColor = obs.color;
        ctx.beginPath();
        ctx.moveTo(obs.x, obs.y + obs.h);
        ctx.lineTo(obs.x + obs.w/2, obs.y);
        ctx.lineTo(obs.x + obs.w, obs.y + obs.h);
        ctx.fill();

        // تحقق التصادم
        if (player.x < obs.x + obs.w && player.x + player.w > obs.x &&
            player.y < obs.y + obs.h && player.y + player.h > obs.y) {
            health -= 2;
            hpFill.style.width = health + '%';
            ctx.fillStyle = 'white'; // وميض عند الإصابة
            ctx.fillRect(0,0, canvas.width, canvas.height);
            if (health <= 0) gameOver();
        }

        if (obs.x + obs.w < 0) {
            obstacles.splice(i, 1);
            score += 10;
            scoreText.innerText = score;
        }
    });

    requestAnimationFrame(animate);
}

function gameOver() {
    gameState = 'END';
    document.getElementById('start-screen').classList.remove('hidden');
    document.getElementById('start-screen').innerHTML = `
        <h1 style="color:#ff0055">MISSION FAILED</h1>
        <p>Score: ${score}</p>
        <button onclick="location.reload()">REBOOT SYSTEM</button>
    `;
}
</script>
</body>
</html>
