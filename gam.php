<?php
session_start();

// تهيئة أعلى نتيجة إذا لم تكن موجودة
if (!isset($_SESSION['high_score'])) {
    $_SESSION['high_score'] = 0;
}

// استقبال النتيجة الجديدة من JavaScript عبر طلب AJAX (اختياري لتطوير اللعبة)
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لعبة التحدي الاحترافية</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #6c5ce7;
            --secondary: #a29bfe;
            --bg: #2d3436;
            --text: #ffffff;
        }

        body {
            font-family: 'Cairo', sans-serif;
            background-color: var(--bg);
            color: var(--text);
            margin: 0;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        /* لوحة التحكم */
        .stats-bar {
            background: rgba(0, 0, 0, 0.5);
            width: 100%;
            padding: 15px;
            display: flex;
            justify-content: space-around;
            backdrop-filter: blur(10px);
            border-bottom: 2px solid var(--primary);
            z-index: 10;
        }

        .stat-box { font-size: 1.2rem; font-weight: bold; }

        /* منطقة اللعب */
        #game-canvas {
            position: relative;
            width: 100vw;
            height: calc(100vh - 70px);
            cursor: crosshair;
        }

        /* الهدف */
        .target {
            position: absolute;
            width: 50px;
            height: 50px;
            background: radial-gradient(circle, #ff7675, #d63031);
            border-radius: 50%;
            box-shadow: 0 0 15px #ff7675;
            cursor: pointer;
            transition: transform 0.1s;
        }

        .target:active { transform: scale(0.8); }

        /* شاشة البداية والنهاية */
        .overlay {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.8);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            z-index: 100;
        }

        button {
            padding: 15px 40px;
            font-size: 1.5rem;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 30px;
            cursor: pointer;
            font-family: 'Cairo';
            transition: 0.3s;
        }

        button:hover { background: var(--secondary); transform: scale(1.05); }

        .hidden { display: none; }
    </style>
</head>
<body>

<div class="stats-bar">
    <div class="stat-box">النقاط: <span id="score">0</span></div>
    <div class="stat-box">الوقت: <span id="timer">30</span></div>
    <div class="stat-box">أعلى نتيجة: <span><?php echo $_SESSION['high_score']; ?></span></div>
</div>

<div id="game-canvas"></div>

<div id="start-screen" class="overlay">
    <h1>جاهز للتحدي؟</h1>
    <p>اضغط على أكبر عدد من الأهداف قبل انتهاء الوقت!</p>
    <button onclick="startGame()">ابدأ اللعب</button>
</div>

<div id="end-screen" class="overlay hidden">
    <h1>انتهى الوقت!</h1>
    <p>نتيجتك هي: <span id="final-score">0</span></p>
    <button onclick="location.reload()">إعادة المحاولة</button>
</div>

<script>
    let score = 0;
    let timeLeft = 30;
    let gameActive = false;
    const canvas = document.getElementById('game-canvas');
    const scoreEl = document.getElementById('score');
    const timerEl = document.getElementById('timer');

    function startGame() {
        document.getElementById('start-screen').classList.add('hidden');
        gameActive = true;
        spawnTarget();
        const countdown = setInterval(() => {
            timeLeft--;
            timerEl.innerText = timeLeft;
            if (timeLeft <= 0) {
                clearInterval(countdown);
                endGame();
            }
        }, 1000);
    }

    function spawnTarget() {
        if (!gameActive) return;

        const target = document.createElement('div');
        target.className = 'target';
        
        // حساب موقع عشوائي
        const x = Math.random() * (window.innerWidth - 60);
        const y = Math.random() * (canvas.offsetHeight - 60);
        
        target.style.left = x + 'px';
        target.style.top = y + 'px';

        target.onclick = () => {
            score += 10;
            scoreEl.innerText = score;
            target.remove();
            spawnTarget();
        };

        canvas.appendChild(target);

        // اختفاء الهدف بعد ثانية إذا لم يُضغط عليه
        setTimeout(() => {
            if (target.parentElement) {
                target.remove();
                spawnTarget();
            }
        }, 1200);
    }

    function endGame() {
        gameActive = false;
        document.getElementById('end-screen').classList.remove('hidden');
        document.getElementById('final-score').innerText = score;
        
        // إرسال النتيجة للسيرفر لحفظها (يمكنك استخدام fetch هنا لتحديث SESSION)
    }
</script>

</body>
</html>
