<?php
error_reporting(0);
date_default_timezone_set('Asia/Riyadh');
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>جدول مباريات اليوم - الخدمة الرقمية</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --main: #e11d48; --bg: #050c14; --border: rgba(255, 255, 255, 0.1); }
        body { 
            margin: 0; padding: 0; background-color: var(--bg); 
            font-family: 'Tajawal', sans-serif; color: #fff;
            display: flex; justify-content: center;
        }
        .main-wrapper { 
            width: 100%; max-width: 500px; min-height: 100vh; 
            padding: 15px; box-sizing: border-box;
        }
        .header-box {
            text-align: center; padding: 15px; background: rgba(255,255,255,0.03);
            border: 1px solid var(--border); border-radius: 20px; margin-bottom: 15px;
            backdrop-filter: blur(10px);
        }
        .header-box h2 { margin: 0; font-size: 18px; color: var(--main); font-weight: 900; }

        /* حاوية الجدول المضمونة عثمان */
        .live-score-widget {
            width: 100%; border-radius: 20px; overflow: hidden;
            border: 1px solid var(--border); background: #fff; /* خلفية بيضاء لضمان ظهور المحتوى */
            min-height: 800px;
        }

        footer { text-align: center; padding: 25px; font-size: 10px; opacity: 0.4; }
    </style>
</head>
<body>

<div class="main-wrapper">
    <div class="header-box">
        <h2><i class="fas fa-futbol"></i> مباريات اليوم المباشرة</h2>
    </div>

    <div class="live-score-widget">
        <iframe src="https://www.livescore.in/free/widgets/live-scores/?font-family=tajawal&width=100%&height=800&background-color=050c14&text-color=ffffff&league-color=e11d48&match-color=ffffff&status-color=e11d48&container-color=050c14&content-color=151d27&border-color=252d37&lang=ar" 
                width="100%" 
                height="800" 
                frameborder="0" 
                scrolling="yes">
        </iframe>
    </div>

    <footer>تحديث تلقائي شامل - الخدمة الرقمية © 2026</footer>
</div>

</body>
</html>
