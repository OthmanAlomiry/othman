<?php
session_start();
// افتراضاً أننا سنخزن أفضل سكور في الجلسة
$highScore = isset($_SESSION['high_score']) ? $_SESSION['high_score'] : 0;
?>
<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>الهروب من العقبات - برو</title>
    <style>
        body {
            margin: 0; background: #0f0c29; 
            color: white; font-family: 'Segoe UI', sans-serif;
            overflow: hidden; display: flex; justify-content: center; align-items: center; height: 100vh;
        }
        #gameContainer { position: relative; border: 4px solid #00d2ff; box-shadow: 0 0 20px #00d2ff; }
        canvas { background: linear-gradient(to bottom, #0f0c29, #302b63, #24243e); display: block; }
        .ui {
            position: absolute; top: 10px; left: 10px; font-size: 20px;
            text-shadow: 2px 2px #000; pointer-events: none;
        }
        #menu {
            position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);
            text-align: center; background: rgba(0,0,0,0.8); padding: 30px; border-radius: 15px;
        }
        button {
            padding: 10px 25px; font-size: 18px; cursor: pointer;
            background: #00d2ff; border: none; border-radius: 5px; color: #000; font-weight: bold;
        }
    </style>
</head>
<body>

<div id="gameContainer">
    <div class="ui">السكور: <span id="score">0</span> | أفضل نتيجة: <?php echo $highScore; ?></div>
    <canvas id="gameCanvas" width="800" height="400"></canvas>
    
    <div id="menu">
        <h1 id="title">مكعب النيون</h1>
        <p id="desc">اضغط "مسافة" أو انقر للقفز</p>
        <button onclick="startGame()">ابدأ التحدي</button>
    </div>
</div>

<script>
    const canvas = document.getElementById('gameCanvas');
    const ctx = canvas.getContext('2d');
    const scoreEl = document.getElementById('score');
    const menu = document.getElementById('menu');

    // إعدادات اللعبة
    let player = { x: 50, y: 300, w: 30, h: 30, dy: 0, jump: -12, gravity: 0.6 };
    let obstacles = [];
    let score = 0;
    let isGameOver = true;
    let gameSpeed = 5;

    function startGame() {
        isGameOver = false;
        score = 0;
        gameSpeed = 5;
        obstacles = [];
        player.y = 300;
        menu.style.display = 'none';
        update();
    }

    // التحكم
    window.addEventListener('keydown', (e) => { if(e.code === 'Space') player.dy = player.jump; });
    canvas.addEventListener('mousedown', () => { player.dy = player.jump; });

    function update() {
        if(isGameOver) return;

        // فيزياء اللاعب
        player.dy += player.gravity;
        player.y += player.dy;

        // اصطدام بالأرض
        if(player.y + player.h > canvas.height) {
            player.y = canvas.height - player.h;
            player.dy = 0;
        }

        // توليد العقبات
        if(Math.random() < 0.02) {
            obstacles.push({ x: canvas.width, y: canvas.height - 40, w: 20, h: 40 });
        }

        // تحريك العقبات
        obstacles.forEach((obs, index) => {
            obs.x -= gameSpeed;
            
            // تحقق من الاصطدام
            if (player.x < obs.x + obs.w && player.x + player.w > obs.x &&
                player.y < obs.y + obs.h && player.y + player.h > obs.y) {
                gameOver();
            }

            if(obs.x + obs.w < 0) {
                obstacles.splice(index, 1);
                score++;
                scoreEl.innerText = score;
                gameSpeed += 0.1; // زيادة الصعوبة تدريجياً
            }
        });

        draw();
        requestAnimationFrame(update);
    }

    function draw() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);

        // رسم اللاعب (تأثير نيون)
        ctx.fillStyle = '#00d2ff';
        ctx.shadowBlur = 15;
        ctx.shadowColor = '#00d2ff';
        ctx.fillRect(player.x, player.y, player.w, player.h);

        // رسم العقبات
        ctx.fillStyle = '#ff0055';
        ctx.shadowColor = '#ff0055';
        obstacles.forEach(obs => {
            ctx.fillRect(obs.x, obs.y, obs.w, obs.h);
        });
        ctx.shadowBlur = 0; // إعادة التصفير للأداء
    }

    function gameOver() {
        isGameOver = true;
        menu.style.display = 'block';
        document.getElementById('title').innerText = "للأسف خسرت!";
        document.getElementById('desc').innerText = "نتيجتك: " + score;
        
        // إرسال النتيجة لـ PHP لحفظها (اختياري عبر AJAX)
    }
</script>
</body>
</html>
