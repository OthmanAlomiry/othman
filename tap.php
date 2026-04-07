<?php
error_reporting(0);
date_default_timezone_set('Asia/Riyadh');

// الحصول على التاريخ عثمان
$date_get = isset($_GET['d']) ? $_GET['d'] : date('Y-m-d');
$display_date = date('d / m / Y', strtotime($date_get));

// حساب التاريخ السابق والتالي للأسهم
$prev_date = date('Y-m-d', strtotime($date_get .' -1 day'));
$next_date = date('Y-m-d', strtotime($date_get .' +1 day'));
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>مباريات اليوم - الخدمة الرقمية</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --main: #e11d48; --bg: #050c14; --border: rgba(255, 255, 255, 0.1); }
        body { background: var(--bg); color: #fff; font-family: 'Tajawal', sans-serif; margin: 0; padding: 10px; display: flex; justify-content: center; }
        .container { width: 100%; max-width: 500px; min-height: 100vh; }
        
        /* نظام الأسهم عثمان */
        .date-picker { 
            display: flex; align-items: center; justify-content: space-between; 
            background: rgba(255,255,255,0.03); border: 1px solid var(--border); 
            padding: 12px; border-radius: 20px; margin-bottom: 15px; backdrop-filter: blur(10px); 
        }
        .date-picker a { 
            color: #fff; text-decoration: none; width: 35px; height: 35px; 
            display: flex; align-items: center; justify-content: center; 
            background: var(--main); border-radius: 50%; transition: 0.3s; 
        }
        .current-date h3 { margin: 0; font-size: 15px; font-weight: 900; color: #fff; }

        /* حاوية الجدول عثمان */
        .widget-box {
            width: 100%;
            border-radius: 20px;
            overflow: hidden;
            border: 1px solid var(--border);
            background: #111;
            min-height: 800px;
        }

        footer { text-align: center; padding: 30px; font-size: 10px; opacity: 0.3; }
    </style>
</head>
<body>

<div class="container">
    <div class="date-picker">
        <a href="?d=<?= $prev_date ?>"><i class="fas fa-chevron-right"></i></a>
        <div class="current-date"><h3><?= $display_date ?></h3></div>
        <a href="?d=<?= $next_date ?>"><i class="fas fa-chevron-left"></i></a>
    </div>

    <div class="widget-box">
        <iframe 
            src="https://mainsite-widget.api-itv.com/api/widget/matchcenter?language=ar&timezone=3&date=<?= $date_get ?>&theme=dark" 
            width="100%" 
            height="1500px" 
            frameborder="0" 
            scrolling="yes">
        </iframe>
    </div>

    <footer>تحديث لحظي ذكي - الخدمة الرقمية © 2026</footer>
</div>

</body>
</html>
